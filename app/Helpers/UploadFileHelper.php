<?php

namespace App\Helpers;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Image;

class UploadFileHelper
{

    public static function upload(
        $file,
        $destination,
        $detail = false,
        $filename = null,
        $thumbnail = false
    ) {
        $url = null;
        if ($file) {
            $extension     = $file->getClientOriginalExtension();
            $original_name = $file->getClientOriginalName();
            $name          = $filename
                ? $filename
                : Str::uuid()->toString() . "." . $extension;
            $full_destination = storage_path("app/" . $destination);
            if ($thumbnail && in_array($extension, ['jpg', 'png', 'jpeg'])) {
                $img = Image::make($file);

                $img->resize(316, 223);

                if ($img->save($full_destination . '/' . $name)) {
                    $url = $destination . '/' . $name;
                }
            } else {
                if ($file->move($full_destination, $name)) {
                    $url = $destination . "/" . $name;
                }
            }
        }

        if (! $extension || empty($extension) || $extension == '' || is_numeric($extension)) {
            $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($fileInfo, $url);
            finfo_close($fileInfo);
            $exte = '';
            if ($mimeType == 'application/pdf') {
                $exte = 'pdf';
            } else if ($mimeType == 'image/jpeg' || $mimeType == 'image/jpg') {
                $exte = 'jpg';
            } else if ($mimeType == 'image/png') {
                $exte = 'png';
            } else if ($mimeType == 'application/msword') {
                $exte = 'doc';
            } else if ($mimeType == 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                $exte = 'docx';
            } else if ($mimeType == 'application/vnd.ms-excel') {
                $exte = 'xls';
            } else if ($mimeType == 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet') {
                $exte = 'xlsx';
            } else if ($mimeType == 'text/plain') {
                $exte = 'txt';
            } else if ($mimeType == 'application/zip') {
                $exte = 'zip';
            } else if ($mimeType == 'application/x-rar-compressed') {
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

        if (! $detail) {
            return $url;
        } else {
            $obj = new \stdClass();
            if (! $url) {
                $obj->url       = null;
                $obj->size      = 0;
                $obj->extension = null;
                $obj->name      = null;
                $obj->md5       = null;
            } else {
                $obj->url       = $url;
                $obj->size      = Storage::size($url);
                $obj->extension = $extension;
                $obj->name      = $original_name;
                $obj->md5       = md5_file(storage_path("app/" . $url));
            }
            return $obj;
        }
    }

    public static function delete_file($source)
    {
        try {
            if (Storage::exists($source)) {
                Storage::delete($source);
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    public static function copy_file($source, $destination)
    {
        $path = null;
        try {
            if (Storage::exists($source)) {
                Storage::copy($source, $destination);
                $path = $destination;
            }
        } catch (\Throwable $th) {
            //throw $th;
        }
        return $path;
    }

    public static function getStringFromTxtUpload($field)
    {
        try {
            if (! $_FILES[$field]) {
                return null;
            }

            return file_get_contents($_FILES[$field]["tmp_name"]);
        } catch (\Throwable $th) {
            //throw $th;
            return null;
        }
    }

    public static function url2path($url)
    {
        $path = str_replace(
            "/",
            ".",
            str_replace(url("attachment") . "/", "", $url)
        );
        $paths = explode(".", $path);
        return $paths[0] .
            "/" .
            $paths[1] .
            (isset($paths[2]) ? "." . $paths[2] : "");
    }
}
