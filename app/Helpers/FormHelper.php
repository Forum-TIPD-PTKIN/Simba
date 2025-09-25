<?php

namespace App\Helpers;

use App\Models\FormData;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class FormHelper
{
    private $values = [];
    private $data   = null;
    private $rule   = null;
    private $jenis;
    private $beasiswa;
    private $tahun_kegiatan;
    private $disabledVue = true;

    public function __construct($jenis, $beasiswa, $tahun_kegiatan)
    {
        $this->jenis = $jenis;
        $this->beasiswa = $beasiswa;
        $this->tahun_kegiatan = $tahun_kegiatan;
        $this->data  = collect(
            FormData::where('jenis', $this->jenis)
                ->where('beasiswa_id', $this->beasiswa)
                ->where('tahun_kegiatan_id', $this->tahun_kegiatan)
                ->orderBy('indexed', 'asc')
                ->get(),
        )->map(function ($item) {
            $config = $item->config ? json_decode($item->config) : null;
            if (! isset($config->validator)) {
                $config->validator = (object) [];
            }

            if (! isset($config->option)) {
                $config->option = [];
            }

            $item->config = $config;
            return $item;
        });
        $this->registerValue();
        $this->rule = $this->getValidator();
    }

    public function execValidator($name = null)
    {
        if ($name) {
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
            $rules[$value->config->name] = $mapValidator($value->config->validator);
        }
        foreach ($form as $key => $value) {
            $key_validator = [];
            foreach ($value->config->validator as $key_ => $value_) {
                $rules_message[$value->config->name . '.' . $normalizerValidator($key_)] = $value_;
            }
        }
        $rule                = new \stdClass();
        $rule->rules         = $rules;
        $rule->rules_message = $rules_message;
        return $rule;
    }

    public function setValidator($name, $validator, $message = '')
    {
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
        foreach ($this->data as $key => $value) {
            if ($value->config->name === $name) {
                $value->value = $data;
            }
        }
        $this->values[$name] = $data;
        session()->flash('_old_input.' . $name, $data);
    }

    public function getValue($name = '')
    {
        $values = new \stdClass();
        $names  = $this->getNames();
        foreach ($names as $key => $value) {
            $values->{$value} = isset($this->values[$value]) ? $this->values[$value] : $this->removeSpace(request()->get($value));
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
            if ($value->config->name === $name) {
                $value->config->option = (object) $options;
            }
        }
    }

    public function addOption($name, $options = [])
    {
        foreach ($this->data as $key => $value) {
            if ($value->config->name === $name) {
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
            if ($value->config->name === $name) {
                $value->config->disabled = true;
            }
        }
    }

    public function setReadOnly($name)
    {
        foreach ($this->data as $key => $value) {
            if ($value->config->name === $name) {
                $value->config->readonly = true;
            }
        }
    }

    public function setType($name, $type = 'text')
    {
        foreach ($this->data as $key => $value) {
            if ($value->config->name === $name) {
                $value->config->type = $type;
            }
        }
    }

    public function setLabel($name, $data)
    {
        foreach ($this->data as $key => $value) {
            if ($value->config->name === $name) {
                $value->judul = $data;
            }
        }
    }

    public function setDescription($name, $data)
    {
        foreach ($this->data as $key => $value) {
            if ($value->config->name === $name) {
                $value->deskripsi = $data;
            }
        }
    }

    public function setNewName($name, $new_name)
    {
        foreach ($this->data as $key => $value) {
            if ($value->config->name === $name) {
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
        $files = request()->file($name);
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
                    $obj->size      = Storage::size($url);
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

    private function registerValue()
    {
        foreach ($this->data as $key => $value) {
            if (isset($this->values[$value->config->name])) {
                $value->value = $this->removeSpace($this->values[$value->config->name]);
            } else {
                $value->value = $this->removeSpace(old($value->config->name, ''));
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