<?php

namespace App\Helpers;

use App\Models\FormData;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use stdClass;

use function PHPUnit\Framework\isArray;

interface Field
{
    public function getName(): string;
    public function getTitle(): ?string;
    public function getType(): string;
    public function getDeskripsi(): string;
    public function getValidator(): array;
    public function getOption(): array;
}


class FormField implements Field
{
    protected string $name;
    protected ?string $title;
    protected string $type;
    protected string $deskripsi;
    protected array $validator;
    protected array $option;

    public function __construct(
        string $name,
        ?string $title = null,
        string $type = 'text',
        string $deskripsi = '',
        array $validator = [],
        array $option = []
    ) {
        $this->name = $name;
        $this->title = $title ?? ucfirst($name);
        $this->type = $type;
        $this->deskripsi = $deskripsi;
        $this->validator = $validator;
        $this->option = $option;
    }

    public function getName(): string
    {
        return $this->name;
    }
    public function getTitle(): ?string
    {
        return $this->title;
    }
    public function getType(): string
    {
        return $this->type;
    }
    public function getDeskripsi(): string
    {
        return $this->deskripsi;
    }
    public function getValidator(): array
    {
        return $this->validator;
    }
    public function getOption(): array
    {
        return $this->option;
    }
}

class FormHelper
{
    private $kode = null;
    private $values = [];
    private $data   = null;
    private $rule   = null;
    private $jenis;
    private $beasiswa;
    private $tahun_kegiatan;
    private $disabledVue = true;

    public function __construct(string $jenis, string|Field|array|null $beasiswa = null, string|null $tahun_kegiatan = null)
    {
        $this->kode = Str::snake(strtolower($jenis));
        $this->jenis = $jenis;
        $this->data = [];
        if ($beasiswa == null || $tahun_kegiatan == null) {
            if ($beasiswa != null) {
                if (is_array($beasiswa)) {
                    foreach ($$beasiswa as $key => $value) {
                        $this->appendField($value);
                    }
                } else {
                    $this->appendField($beasiswa);
                }
            }
            return;
        }
        $this->beasiswa = $beasiswa;
        $this->tahun_kegiatan = $tahun_kegiatan;
        $dataf = FormData::where('jenis', $this->jenis)
            ->where('beasiswa_id', $this->beasiswa)
            ->where('tahun_kegiatan_id', $this->tahun_kegiatan)
            ->orderBy('indexed', 'asc')
            ->get();

        foreach ($dataf as $key => $item) {
            $config = $item->config ? json_decode($item->config) : null;
            if (! isset($config->validator)) {
                $config->validator = (object) [];
            }

            if (! isset($config->option)) {
                $config->option = [];
            }

            $item->config = $config;
            array_push($this->data, $item);
        }
        $this->registerValue();
        $this->rule = $this->getValidator();
    }

    public function appendField(
        Field $field,
        $after = null
    ) {
        $item = (object)[
            'jenis' => $this->jenis,
            'deskripsi' => $field->getDeskripsi(),
            'config' => (object)[
                'title' => $field->getTitle(),
                'name' => $field->getName(),
                'type' => $field->getType(),
                'validator' => (object)$field->getValidator(),
                'option' => collect($field->getOption())->map(fn($o) => (object)$o)->toArray(),
            ],
            'kode' => Str::snake(strtolower($field->getName()))
        ];

        $data = $this->data instanceof \Illuminate\Support\Collection
            ? $this->data->all()
            : (array) $this->data;

        if ($after === false) {
            // sisipkan di awal
            array_unshift($data, $item);
        } elseif ($after !== null) {
            // cari field dengan name tertentu, lalu sisipkan setelahnya
            $inserted = false;
            foreach ($data as $index => $fieldItem) {
                if ($this->kode . '_' . $fieldItem->config->name === $this->kode . '_' . $after) {
                    array_splice($data, $index + 1, 0, [$item]);
                    $inserted = true;
                    break;
                }
            }
            if (! $inserted) {
                $data[] = $item; // fallback taruh di akhir kalau tidak ketemu
            }
        } else {
            // default null → tambahkan di akhir
            $data[] = $item;
        }

        $this->data = collect($data);
        $this->registerValue($item->config->name);

        if (isset($item->config->validator)) {
            foreach ((array) $item->config->validator as $key1 => $value1) {
                $this->setValidator($item->config->name, $key1, $value1);
            }
        }
    }


    public function execValidator($name = null)
    {
        if ($name) {
            $name = $this->kode . '_' . $name;
            $rules   = [];
            $message = [];
            foreach ($this->rule->rules as $key => $value) {
                if ($key === $name) {
                    $rules[$name] = $value;
                }
            }
            foreach ($this->rule->rules_message as $key => $value) {
                $n = explode('.', $key);
                if ($n[0] === $name) {
                    $message[$key] = $value;
                }
            }
            $validator = Validator::make(request()->only($name), $rules, $message);
            return $validator;
        } else {
            $validator = Validator::make(request()->all(), $this->rule->rules, $this->rule->rules_message);
            return $validator;
        }
    }

    public function getValidator()
    {
        if ($this->rule) {
            return $this->rule;
        }

        $mapValidator = function ($n) {
            $r = [];
            foreach ($n as $key => $value) {
                array_push($r, $key);
            }
            return implode('|', $r);
        };
        $normalizerValidator = function ($key) {
            $n = explode(':', $key);
            return $n[0];
        };
        $form          = $this->data;
        $rules         = [];
        $rules_message = [];
        foreach ($form as $key => $value) {
            $rules[$this->kode . '_' . $value->config->name] = $mapValidator($value->config->validator);
        }
        foreach ($form as $key => $value) {
            $key_validator = [];
            foreach ($value->config->validator as $key_ => $value_) {
                $rules_message[$this->kode . '_' . $value->config->name . '.' . $normalizerValidator($key_)] = $value_;
            }
        }
        $rule                = new \stdClass();
        $rule->rules         = $rules;
        $rule->rules_message = $rules_message;
        return $rule;
    }

    public function setValidator($name, $validator, $message = '')
    {
        $name = $this->kode . '_' . $name;
        $normalizerValidator = function ($key) {
            $n = explode(':', $key);
            return $n[0];
        };
        if (isset($this->rule->rules[$name])) {
            $this->rule->rules[$name] = $this->rule->rules[$name] . '|' . $validator;
        } else {
            $this->rule->rules[$name] = $validator;
        }
        if ($message) {
            $this->rule->rules_message[$name . '.' . $normalizerValidator($validator)] = $message;
        }
    }

    public function removeValidator($name, $validator)
    {
        $name = $this->kode . '_' . $name;
        if (isset($this->rule->rules[$name])) {
            $expl             = explode('|', $this->rule->rules[$name]);
            $new_rule         = [];
            $new_rule_message = [];
            for ($i = 0; $i < count($expl); $i++) {
                $spr = explode(':', $expl[$i]);
                if (! ($spr[0] === $validator || $expl[$i] === $validator)) {
                    array_push($new_rule, $expl[$i]);
                    // if(isset($this->rule->rules_message[$name . '.' . $spr[0]])){
                    //     array_push($new_rule_message, $this->rule->rules_message[$name . '.' . $spr[0]]);
                    // }
                }
            }
            $this->rule->rules[$name] = join('|', $new_rule); //join('|',$new_rule);
        }
    }

    public function setValue($name, $data)
    {
        $name = $this->kode . '_' . $name;
        foreach ($this->data as $key => $value) {
            if ($this->kode . '_' . $value->config->name === $name) {
                $value->value = $data;
            }
        }
        $this->values[$this->kode . '_' . $name] = $data;
        session()->flash('_old_input.' . $this->kode . '_' . $name, $data);
    }

    public function getValue($name = '')
    {
        $values = new \stdClass();
        $names  = $this->getNames();
        foreach ($names as $key => $value) {
            $values->{$value} = isset($this->values[$this->kode . '_' . $value]) ? $this->values[$this->kode . '_' . $value] : $this->removeSpace(request()->get($this->kode . '_' . $value));
        }
        if ($name) {
            return $values->{$name};
        }

        return $values;
    }

    public function getNames($string = false)
    {
        $names = [];
        foreach ($this->data as $key => $value) {
            array_push($names, $value->config->name);
        }
        if ($string) {
            return implode(', ', $names);
        } else {
            return $names;
        }
    }

    public function setOption($name, $options = [])
    {
        foreach ($this->data as $key => $value) {
            if ($this->kode . '_' . $value->config->name === $this->kode . '_' . $name) {
                $value->config->option = (object) $options;
            }
        }
    }

    public function addOption($name, $options = [])
    {
        foreach ($this->data as $key => $value) {
            if ($this->kode . '_' . $value->config->name === $this->kode . '_' . $name) {
                array_push($value->config->option, (object) $options);
            }
        }
    }

    public function getInstance()
    {
        return $this->data;
    }

    public function render($exclude = [])
    {
        return view('engine-form.generate-form', ['form' => $this->getForm(), 'exclude' => $exclude, 'disabledVue' => $this->disabledVue])->render();
    }

    public function setDisabled($name)
    {
        foreach ($this->data as $key => $value) {
            if ($this->kode . '_' . $value->config->name === $this->kode . '_' . $name) {
                $value->config->disabled = true;
            }
        }
    }

    public function setReadOnly($name)
    {
        foreach ($this->data as $key => $value) {
            if ($this->kode . '_' . $value->config->name === $this->kode . '_' . $name) {
                $value->config->readonly = true;
            }
        }
    }

    public function setType($name, $type = 'text')
    {
        foreach ($this->data as $key => $value) {
            if ($this->kode . '_' . $value->config->name === $this->kode . '_' . $name) {
                $value->config->type = $type;
            }
        }
    }

    public function getType($name = '')
    {
        $nametypes = new \stdClass();
        foreach ($this->data as $key => $value) {
            $nametypes->{$value->config->name} = $value->config->type;
            if ($this->kode . '_' . $value->config->name === $this->kode . '_' . $name) {
                if ($name) {
                    return $value->config->type;
                }
            }
        }
        if ($name) return null;
        return $nametypes;
    }

    public function setLabel($name, $data)
    {
        foreach ($this->data as $key => $value) {
            if ($this->kode . '_' . $value->config->name === $this->kode . '_' . $name) {
                $value->judul = $data;
            }
        }
    }

    public function setDescription($name, $data)
    {
        foreach ($this->data as $key => $value) {
            if ($this->kode . '_' . $value->config->name === $this->kode . '_' . $name) {
                $value->deskripsi = $data;
            }
        }
    }

    public function setNewName($name, $new_name)
    {
        foreach ($this->data as $key => $value) {
            if ($this->kode . '_' . $value->config->name === $this->kode . '_' . $name) {
                unset($value->config->{$name});
                $value->config->name = $new_name;
            }
        }
    }

    public function integrateVue($status = true)
    {
        $this->disabledVue = !$status;
    }

    public function saveFile($name, $folder)
    {
        $files = request()->file($this->kode . '_' . $name);
        if (is_array($files)) {
            $results = [];

            foreach ($files as $file) {
                $result = $this->processFile($file, $folder);
                if ($result) {
                    $results[] = $result;
                }
            }

            return $results;
        } else {
            $file = $files;
            return $this->processFile($file, $folder);
        }
    }

    private function processFile($file, $folder)
    {
        if ($file) {
            if ($file->isValid() && $file->isFile()) {
                $extension        = $file->getClientOriginalExtension();
                $original_name    = $file->getClientOriginalName();
                $name             = Str::uuid()->toString() . '.' . $extension;
                $full_destination = storage_path('app/' . $folder);

                if ($file->move($full_destination, $name)) {
                    $url = $folder . '/' . $name;
                    if (! $extension || empty($extension) || $extension == '' || is_numeric($extension)) {
                        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
                        $mimeType = finfo_file($fileInfo, $url);
                        finfo_close($fileInfo);
                        $exte = '';
                        if ($mimeType == 'application/pdf') {
                            $exte = 'pdf';
                        } elseif ($mimeType == 'image/jpeg' || $mimeType == 'image/jpg') {
                            $exte = 'jpg';
                        } elseif ($mimeType == 'image/png') {
                            $exte = 'png';
                        } elseif ($mimeType == 'application/msword') {
                            $exte = 'doc';
                        } elseif ($mimeType == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                            $exte = 'docx';
                        } elseif ($mimeType == 'application/vnd.ms-excel') {
                            $exte = 'xls';
                        } elseif ($mimeType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                            $exte = 'xlsx';
                        } elseif ($mimeType == 'text/plain') {
                            $exte = 'txt';
                        } elseif ($mimeType == 'application/zip') {
                            $exte = 'zip';
                        } elseif ($mimeType == 'application/x-rar-compressed') {
                            $exte = 'rar';
                        } else {
                            // Ekstensi default jika tipe mime tidak dikenali
                            $exte = 'unknown';
                        }
                        if (is_numeric($extension)) {
                            rename($url, $url . '.' . $exte);
                            $url       = $url . '.' . $exte;
                            $extension = $exte;
                        } else {
                            rename($url, $url . $exte);
                            $url       = $url . $exte;
                            $extension = $exte;
                        }
                    }

                    $obj            = new \stdClass();
                    $obj->path      = $url;
                    $obj->size      = filesize(storage_path('app/' . $url));
                    $obj->extension = $extension;
                    $obj->name      = $original_name;
                    $obj->md5       = md5_file(storage_path('app/' . $url));

                    return $obj;
                }
            }
        }

        $obj            = new \stdClass();
        $obj->path      = null;
        $obj->size      = 0;
        $obj->extension = null;
        $obj->name      = null;
        $obj->md5       = null;

        return null;
    }

    private function registerValue($name = null)
    {
        foreach ($this->data as $value) {
            $fullName = $this->kode . '_' . $value->config->name;

            if ($name === null || $fullName === $this->kode . '_' . $name) {
                $source = $this->values[$fullName] ?? old($fullName, '');
                $value->value = $this->removeSpace($source);
            }
        }
    }


    private function getForm()
    {
        return $this->data;
    }

    private function removeSpace($data)
    {
        return preg_replace('/\s\s+/', ' ', $data);
    }
}
