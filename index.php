<?php
require_once './Conexion.php';
require_once './Partida.php';
require_once './FactoriaPartida.php';
require_once './ControladorMina.php';
require_once './FactoriaUsuario.php';
require_once './Usuario.php';
require_once './Constantes.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';

header("Content-Type:application/json");
$requestMethod = $_SERVER["REQUEST_METHOD"];
$paths = $_SERVER['REQUEST_URI'];
$argus = explode('/', $paths);
$cod;

unset($argus[0]);
$json = file_get_contents("php://input");
$datos = json_decode($json);
$accion;

if ($requestMethod == 'GET') {
    $u = $datos->usuario;
    $p = $datos->password;
    $accion = ControladorMina::Login($u, $p);
    if ($argus[1] == 'ranking' && $accion['codigo']==200) {
        $accion = ControladorMina::mostrarRanking();

    } else {
        if ($accion['codigo'] != 200) {

        } else {
            $user = $accion['usuario'];
            if ($argus[1] == 'consultar') {

                $accion = ControladorMina::validarAdmin($user);

                if ($accion['admin']) {
                    if (count($argus) > 1) {
                        $accion = ControladorMina::listarUnUsuario($argus[2]);
                    } else {
                        $accion = ControladorMina::listarTodosUsuarios();
                    }
                }
            } else {
                $accion = ControladorMina::partidaPendienteExiste($accion['usuario']);
                if ($accion['codigo'] != 200) {
                    if (count($argus) == 1) {

                        $accion = ControladorMina::nuevaPartida(Constantes::$TABLERODEFAULT, Constantes::$MINASDEFAULT, $accion['usuario']);
        
                    } else {
                        $accion = ControladorMina::nuevaPartida((int)$argus[1], (int)$argus[2], $accion['usuario']);

                    }
                } else {
                    $p = ControladorMina::obtenerPartida($user);
                     ControladorMina::mostrarPartida($p);
                }
            }
        }
    }
}
if ($requestMethod == 'POST') {
    $accion = ControladorMina::Login($datos->usuario, $datos->password);

    if ($accion['codigo'] != 200) {

    } else {
        $user = $accion['usuario'];

        if ($argus[1] == 'nuevo') {

            $accion = ControladorMina::validarAdmin($user);

            if ($accion['admin']) {

                $accion = ControladorMina::registrarUsuario($datos->nuevoPassword, $datos->nuevoNombre, $datos->adm);
            }
        } else {
            $accion = ControladorMina::partidaPendienteExiste($user);
    
            if ($accion['codigo'] == 200) {

                $p = ControladorMina::obtenerPartida($user);
                
                if ($argus[1] == 'rendicion') {
                    $accion = ControladorMina::rendicion($user, $p);
                  
                
                } else {
                    $accion = ControladorMina::juegaRonda($p, $datos->casilla, $user);
                    
                }
            } else {
                $cod = $accion['codigo'];
                $mensaje = $accion['mensaje'];
                header("HTTP/1.1 " . $cod . ' ' . $mensaje);
            }
        }
    }
            
}

if ($requestMethod == 'PUT') {
    if ($argus[1] == 'recuperacion') {
        $email = $datos->email;
        $u = $datos->usuario;
        $accion = ControladorMina::recuperarCuenta($email, $u);
    } else {
        $accion = ControladorMina::Login($datos->usuario, $datos->password);

        if ($accion['codigo'] != 200) {
            
        } else {
            $user = $accion['usuario'];
            $accion = ControladorMina::validarAdmin($user);
            if ($accion['admin']) {
                $u = $datos->usuarioUpdate;
                $p = $datos->passwordUpdate;

                $accion = ControladorMina::cambiarPassword($u, $p,0);
            }
        }
    }
}

if ($requestMethod == 'DELETE') {
    $accion = ControladorMina::Login($datos->usuario, $datos->password);

    if ($accion['codigo'] != 200) {
       
    } else {
        $user = $accion['usuario'];
        $accion = ControladorMina::validarAdmin($user);
        if ($accion['admin']) {
            $u = $datos->usuarioDelete;
            $accion = ControladorMina::eliminarUsuario($u);
        }
    }
}
