<?php

class Archivo
{
    static $_imgdir = './img/';
    static $_bkpdir = './bkp/';
    static $_dir = './archivos/';

    //return lista/null
    static function getJSON($ruta)
    {
        Archivo::checkIsDir(Archivo::$_dir);
        if (file_exists(Archivo::$_dir . $ruta)) {
            $ar = fopen(Archivo::$_dir . $ruta, 'r');
            $lista = json_decode(fgets($ar));
            fclose($ar);
            if (isset($lista)) {
                return $lista;
            }/* else {
                echo json_encode(array("message" => "La lista esta vacia"));
            }
        } else {
            echo json_encode(array('message'=>'El archivo no existe'));*/
        }
    }

    //guardar en JSON
    static function SaveJson($filename, $obj)
    {
        $lista = Archivo::getJSON($filename);

        if (!isset($lista)) {
            $lista = array();
        }
        $ar = fopen(Archivo::$_dir . $filename, 'w');
        array_push($lista, $obj);
        fwrite($ar, json_encode($lista));
        fclose($ar);
    }

    static function changePhotoJson($filename, $email, $newPhoto)
    {
        $lista = Archivo::getJSON($filename);
        if (isset($lista)) {
            foreach ($lista as $value) {
                if($value->_email == $email){
                    if(strlen($value->_foto) > 0){
                        Archivo::deleteFile($value->_foto);
                    } 
                    $value->_foto = $newPhoto;
                break;
                }
            }

        $ar = fopen(Archivo::$_dir . $filename, 'w');
        
        fwrite($ar, json_encode($lista));
        fclose($ar);
        }
    }

    static function imageHandler($nombre, $obj)
    {
        Archivo::checkIsDir(Archivo::$_imgdir);
        Archivo::checkIsDir(Archivo::$_bkpdir);
        foreach ($obj as $value) {
            $ext = Archivo::getExtensionFile('/', $value['type']);
            $origen = $value['tmp_name'];
            $rand = date_timestamp_get(new DateTime());
            $destino = Archivo::$_imgdir . $nombre . $rand .'.'. $ext;
            //var_dump($value);
            if (Archivo::isImage($ext)) {
                if (Archivo::checkSizeInMB(3.5, $value['size'])) {
                    echo json_encode(array("message"=>"Archivo $nombre excede el tamaño permitido"));
                } else {
                    if (move_uploaded_file($origen, $destino)) {
                        echo json_encode(array("message"=>"Archivo $nombre subido correctamente"));
                        return $nombre . $rand .'.'. $ext;
                    } else {
                        echo json_encode(array("message"=>"Error al subir el archivo $nombre"));
                    }
                }
            } else {
                echo json_encode(array("message"=>"$nombre No es una imagen"));
            }
        }
        return false;
    }

    static function getExtensionFile($delimiter, $path)
    {
        $path = explode($delimiter, $path);
        return $path[count($path) - 1];
    }

    static function checkSizeInMB($maxSize, $size)
    {
        return $maxSize <= (int)$size / 1024 / 1024;
    }

    static function isImage($ext)
    {
        $ret = false;
        $imgExt = array('png' => 'image/png',
        'jpe' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'jpg' => 'image/jpeg',
        'gif' => 'image/gif',
        'bmp' => 'image/bmp',
        'ico' => 'image/vnd.microsoft.icon',
        'tiff' => 'image/tiff',
        'tif' => 'image/tiff',
        'svg' => 'image/svg+xml',
        'svgz' => 'image/svg+xml');
        
        //return ($ext === 'jpeg' || $ext === 'jpg');
        return array_key_exists($ext, $imgExt);
    }

    static function deleteFile($source)
    {
        Archivo::checkIsDir(Archivo::$_imgdir);
        Archivo::checkIsDir(Archivo::$_bkpdir);
        //echo 'paso1';
        if (Archivo::isImage(Archivo::getExtensionFile('.', $source))) {
          //  echo 'paso2';
            copy(Archivo::$_imgdir . $source, Archivo::$_bkpdir . $source);
            unlink(Archivo::$_imgdir . $source);
        }
    }

    static function checkIsDir($ruta){
        if(!is_dir($ruta)) mkdir($ruta);
    }
}
