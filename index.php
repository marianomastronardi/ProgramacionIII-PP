<?php

require_once './usuario.php';
require_once './token.php';
require_once './autos.php';

$method = $_SERVER['REQUEST_METHOD'];
if (isset($_SERVER['PATH_INFO'])) {
    $pathInfo = explode('/', $_SERVER['PATH_INFO']);
    $path = $pathInfo[1];
    if (count($pathInfo) > 2) $param = $pathInfo[2];
}
$token = $_SERVER['HTTP_TOKEN'] ?? '';

switch ($method) {
    case 'POST';
        switch ($path) {
            case 'registro':

                if (isset($param)) {
                    if (strlen($token) > 0) {
                        if (iToken::decodeUserToken($token)) {
                            $foto = $_FILES;
                            if (isset($_FILES)) {
                                Usuario::changePhoto('users.json', $param, $foto);
                            }
                        }
                    } else {
                        echo json_encode(array('message' => 'Usuario no autenticado'));
                    }
                } else {
                    $email = $_POST['email'] ?? '';
                    $tipo = $_POST['tipo'] ?? '';
                    $clave = $_POST['password'] ?? '';
                    $foto = $_FILES ?? false;
                    if (strlen($email) > 0 && strlen($tipo) > 0  && strlen($clave) > 0/* && isset($_FILES)*/) {
                        $usuario = new Usuario($email, $tipo, $clave, $foto);

                        $usuario->SaveUsuarioAsJSON('users.json');
                    }
                }


                break;
            case 'login':
                $email = $_POST['email'] ?? '';
                $clave = $_POST['password'] ?? '';
                if (strlen($email) > 0 && strlen($clave) > 0) {
                    echo Usuario::LogIn('users.json', $email, $clave);
                }
                break;
            case 'ingreso':

                $patente = $_POST['patente'] ?? '';
                $fecha = new DateTime();

                if (strlen($token) > 0) {
                     if (iToken::decodeUserToken($token)) {
                        if (strlen($patente) > 0) {
                           $email =  Usuario::getEmail($token);
                           switch(Usuario::esAdmin('users.json', $email))
                           {
                               case 'admin':
                                echo json_encode(array('message' => 'Solo se permiten users'));
                               break;
                               case 'user' :
                            $auto = new Autos($patente, $fecha->format('Y-m-d H:i:s'), $email);
                            $auto->SaveAutoAsJSON('autos.json');
                               break;
                               default:
                               echo json_encode(array('message' => 'No existe el usuario'));
                            break;
                           }
             
                        }
                    }
                }

                break;
          
            case 'users':
                
                if (isset($param)) {
                    if (strlen($token) > 0) {
                        if (iToken::decodeUserToken($token)) {
                            $foto = $_FILES;
                            if (isset($_FILES)) {
                                Usuario::changePhoto('users.json', $param, $foto);
                            }
                        }
                    } else {
                        echo json_encode(array('message' => 'Usuario no autenticado'));
                    }
                } else {
                    $email = $_POST['email'] ?? '';
                    $tipo = $_POST['tipo'] ?? '';
                    $clave = $_POST['password'] ?? '';
                    $foto = $_FILES ?? false;
                    if (strlen($email) > 0 && strlen($tipo) > 0  && strlen($clave) > 0/* && isset($_FILES)*/) {
                        $usuario = new Usuario($email, $tipo, $clave, $foto);

                        $usuario->SaveUsuarioAsJSON('users.json');
                    }
                }


                break;
            break;
            default:
                echo "Path incorecto";
                break;
        }
        break;

    case 'GET':
        switch ($path) {
                case 'retiro':

                    if (isset($param)) {
                        if (strlen($token) > 0) {
                            if (iToken::decodeUserToken($token)) {
                                $email =  Usuario::getEmail($token);
                                switch(Usuario::esAdmin('users.json', $email))
                                {
                                    case 'admin':
                                     echo json_encode(array('message' => 'Solo se permiten users'));
                                    break;
                                    case 'user' :
                                        $fecha = new DateTime();
                                          Autos::SalidaAuto('autos.json', $param, $fecha);
                                    break;
                                    default:
                                    echo json_encode(array('message' => 'No existe el usuario'));
                                 break;
                                }
                            }
                        } else {
                            echo json_encode(array('message' => 'Usuario no autenticado'));
                        }
                    }
                break;

            case 'ingreso':

                $param = $_GET['patente']??'';
                if (isset($param) && strlen($param) > 0) {
                    if (strlen($token) > 0) {
                        if (iToken::decodeUserToken($token)) {
                           Autos::getAutoPatente('autos.json', $param);
                        break;
                        }
                    } else {
                        echo json_encode(array('message' => 'Usuario no autenticado'));
                    }
                } else {

                    if (strlen($token) > 0) {
                        if (iToken::decodeUserToken($token)) {
                            $ruta = 'autos.json';

                            Autos::getAutos($ruta);
                            break;
                    } else {
                        echo json_encode(array('message' => 'Usuario no autenticado'));
                    }
                    
                }
            }

          
            default:
                echo "Path incorecto";
                break;
        }
        break;

    default:
        echo "Metodo incorrecto";
        break;
}
