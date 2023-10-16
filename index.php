<?php
require_once './Conexion.php';
require_once './Clases/Partida.php';
require_once './Clases/Partida.php';
require_once './Controladores/ControladorMina.php';
require_once './Factorias/FactoriaUsuario.php';
require_once './Clases/Usuario.php';
require_once './Constantes.php';
require_once './Controladores/ControladorAdmin.php';
require_once './Controladores/ControladorUsuario.php';
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
    $accion = ControladorUsuario::Login($u, $p);
    if ($argus[1] == 'ranking' && $accion['codigo']==200) {
        $accion = ControladorUsuario::mostrarRanking();

    } else {
        if ($accion['codigo'] != 200) {

        } else {
            $user = $accion['usuario'];
            if ($argus[1] == 'consultar') {

                $accion = ControladorAdmin::validarAdmin($user);

                if ($accion['admin']) {
                    if (count($argus) > 1) {
                        $accion = ControladorAdmin::listarUnUsuario($argus[2]);
                    } else {
                        $accion = ControladorAdmin::listarTodosUsuarios();
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
    $accion = ControladorUsuario::Login($datos->usuario, $datos->password);

    if ($accion['codigo'] != 200) {

    } else {
        $user = $accion['usuario'];

        if ($argus[1] == 'nuevo') {

            $accion = ControladorAdmin::validarAdmin($user);

            if ($accion['admin']) {

                $accion = ControladorAdmin::registrarUsuario($datos->nuevoPassword, $datos->nuevoNombre, $datos->adm);
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
        $accion = ControladorUsuario::recuperarCuenta($email, $u);
    } else {
        $accion = ControladorUsuario::Login($datos->usuario, $datos->password);

        if ($accion['codigo'] != 200) {
            
        } else {
            $user = $accion['usuario'];
            $accion = ControladorAdmin::validarAdmin($user);
            if ($accion['admin']) {
                $u = $datos->usuarioUpdate;
                $p = $datos->passwordUpdate;
                $accion = ControladorAdmin::cambiarPassword($u, $p,0);
            }
        }
    }
}

if ($requestMethod == 'DELETE') {
    $accion = ControladorUsuario::Login($datos->usuario, $datos->password);

    if ($accion['codigo'] != 200) {
       
    } else {
        $user = $accion['usuario'];
        $accion = ControladorAdmin::validarAdmin($user);
        if ($accion['admin']) {
            $u = $datos->usuarioDelete;
            $accion = ControladorAdmin::eliminarUsuario($u);
        }
    }
}
