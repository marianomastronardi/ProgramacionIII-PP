<?php

require_once './archivo.php';
require_once './usuario.php';

class Autos
{

    public $_patente;
    public $_fechaIngreso;
    public $_email;
    public $_fechaEgreso;
    public $_importe;
    function __construct($patente, $fechaIngreso, $email, $fechaEgreso = null, $importe = 0)
    {
        $this->_patente = $patente;
        $this->_fechaIngreso = $fechaIngreso;
        $this->_email = $email;
        if (isset($fechaEgreso)) $this->_fechaEgreso = $fechaEgreso;
        $this->_importe = $importe;
    }

    function __get($name)
    {
        return $this->$name;
    }

    function __set($name, $value)
    {
        $this->$name = $value;
    }

    function SaveAutoAsJSON($ruta)
    {
        Archivo::SaveJson($ruta, $this);
    }

    static function SalidaAuto($ruta, $patente, $fechaEgreso)
    {
        $lista = Archivo::getJSON($ruta);
        if (isset($lista)) {
            foreach ($lista as $value) {
                if ($value->_patente == $patente) {
                    $value->_fechaEgreso = $fechaEgreso->format('Y-m-d H:i:s');
                    $t1 = StrToTime($value->_fechaEgreso);
                    $t2 = StrToTime($value->_fechaIngreso);
                    $diff = $t1 - $t2;
                    $hours = $diff / (60 * 60);
                    if ($hours < 4) {
                        $value->_importe = $hours * 100;
                    } elseif ($hours >= 4 && $hours <= 12) {
                        $value->_importe = $hours * 60;
                    } else {
                        $value->_importe = $hours * 30;
                    }
                    echo json_encode(array('Importe' => $value->_importe, 'Patente' => $value->_patente, 'fechaIngreso' => $value->_fechaIngreso, 'fechaEgreso' => $value->_fechaEgreso));
                }
            }

            $ar = fopen(Archivo::$_dir . $ruta, 'w');

            fwrite($ar, json_encode($lista));
            fclose($ar);
        }
    }

    static function getAutos($ruta)
    {
        $lista = Archivo::getJSON($ruta);

        if (isset($lista)) {
            asort($lista);

            echo json_encode($lista);
        }
    }

    static function getAutoPatente($ruta, $patente)
    {
        $lista = Archivo::getJSON($ruta);
        if (isset($lista)) {
            foreach ($lista as $value) {
                if ($value->_patente == $patente) {
                    echo json_encode(array('Importe' => $value->_importe, 'Patente' => $value->_patente, 'fechaIngreso' => $value->_fechaIngreso, 'fechaEgreso' => $value->_fechaEgreso));
                    break;
                }   
            }
        }
    }
}
