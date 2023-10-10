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

// print_r(ControladorMina::mostrarRanking());
header("Content-Type:application/json");
$requestMethod = $_SERVER["REQUEST_METHOD"];
$paths = $_SERVER['REQUEST_URI'];
$argus = explode('/', $paths);
$cod;

unset($argus[0]);
$json = file_get_contents("php://input");
$datos = json_decode($json);
$accion;
$mostrar = [];
if ($requestMethod == 'GET') {
    $accion = ControladorMina::Login($datos->usuario, $datos->password);

    if ($accion['codigo'] == 400) {

        $cod = $accion['codigo'];
        $mensaje = $accion['mensaje'];
        header("HTTP/1.1 " . $cod . ' ' . $mensaje);
    } else {
        $user = $accion['usuario'];
        if ($argus[1] == 'consultar') {

            $accion = ControladorMina::validarAdmin($user);

            if ($accion['admin']) {
                if (count($argus) > 1) {
                    $accion = ControladorMina::listarUnUsuario($argus[2]);
                    if ($accion['excepcion']) {
                        $cod = $accion['codigo'];
                        $mensaje = $accion['mensaje'];
                        header("HTTP/1.1 " . $cod . ' ' . $mensaje);
                        echo json_encode(['excepcion' => $accion['excepcion']]);
                    } else {
                        $cod = $accion['codigo'];
                        $mensaje = $accion['mensaje'];
                        header("HTTP/1.1 " . $cod . ' ' . $mensaje);
                        echo json_encode(['excepcion' => $accion['usuario']]);
                    }
                } else {
                    $accion = ControladorMina::listarTodosUsuarios();
                    if ($accion['excepcion']) {
                        $cod = $accion['codigo'];
                        $mensaje = $accion['mensaje'];
                        header("HTTP/1.1 " . $cod . ' ' . $mensaje);
                        echo json_encode(['excepcion' => $accion['excepcion']]);
                    } else {
                        $cod = $accion['codigo'];
                        $mensaje = $accion['mensaje'];
                        header("HTTP/1.1 " . $cod . ' ' . $mensaje);
                        echo json_encode(['excepcion' => $accion['usuarios']]);
                    }
                }
            }
        } else {
            $accion = ControladorMina::partidaPendienteExiste($accion['usuario']);
            if ($accion['codigo'] == 200) {
                if (count($argus) == 1) {

                    $accion = ControladorMina::nuevaPartida(Constantes::$TABLERODEFAULT, Constantes::$MINASDEFAULT, $accion['usuario']);
                    $cod = $accion['codigo'];
                    $mensaje = $accion['mensaje'];
                    header("HTTP/1.1 " . $cod . ' ' . $mensaje);
                    echo json_encode(['tablero' => $accion['partida']->oculto]);
                } else {
                    $accion = ControladorMina::nuevaPartida((int)$argus[1], (int)$argus[2], $accion['usuario']);
                    $cod = $accion['codigo'];
                    $mensaje = $accion['mensaje'];
                    header("HTTP/1.1 " . $cod . ' ' . $mensaje);
                    echo json_encode(['tablero' => $accion['partida']->oculto]);
                }
            } else {
                $p = ControladorMina::obtenerPartida($accion['usuario']);
                $cod = $accion['codigo'];
                $mensaje = $accion['mensaje'];
                header("HTTP/1.1 " . $cod . ' ' . $mensaje);
                echo json_encode(['tablero' => $p->oculto]);
            }
        }
    }
}
if ($requestMethod == 'POST') {
    $accion = ControladorMina::Login($datos->usuario, $datos->password);

    if ($accion['codigo'] == 400) {

        $cod = $accion['codigo'];
        $mensaje = $accion['mensaje'];
    } else {
        $user = $accion['usuario'];

        if ($argus[1] == 'nuevo') {

            $accion = ControladorMina::validarAdmin($user);

            if ($accion['admin']) {

                $accion = ControladorMina::registrarUsuario($datos->nuevoPassword, $datos->nuevoNombre, $datos->adm);
                $cod = $accion['codigo'];
                $mensaje = $accion['mensaje'];
                header("HTTP/1.1 " . $cod . ' ' . $mensaje);
                if ($accion['excepcion']) {
                    echo json_encode(['excepcion' => $accion['excepcion']]);
                }
            }
        } else {
            $accion = ControladorMina::partidaPendienteExiste($accion['usuario']);

            if ($accion['codigo'] == 201) {

                $p = ControladorMina::obtenerPartida($accion['usuario']);
                if ($datos->rendirse != null) {
                    $accion = ControladorMina::rendicion($user, $p);
                } else {
                    $accion = ControladorMina::juegaRonda($p, $datos->casilla, $user);
                }
            } else {
                $accion = ['codigo' => 404, 'mensaje' => 'No se ha encontrado ningun partida'];
            }
        }
    }

    // echo json_encode(['tablero' => $accion['partida']]);


}

if ($requestMethod == 'PUT') {
    if ($argus[1] == 'recuperacion') {
        $email = $datos->email;
        $u = $datos->usuario;
        $accion = ControladorMina::recuperarCuenta($email, $u);
    } else {
        $accion = ControladorMina::Login($datos->usuario, $datos->password);

        if ($accion['codigo'] == 400) {
            $cod = $accion['codigo'];
            $mensaje = $accion['mensaje'];
        } else {
            $user = $accion['usuario'];
            $accion = ControladorMina::validarAdmin($user);
            if ($accion['admin']) {
                $u = $datos->usuarioUpdate;
                $p = $datos->passwordUpdate;

                $accion = ControladorMina::cambiarPassword($u, $p);
            }
        }
    }
    $cod = $accion['codigo'];
    $mensaje = $accion['mensaje'];
    header("HTTP/1.1 " . $cod . ' ' . $mensaje);
}

if ($requestMethod == 'DELETE') {
    $accion = ControladorMina::Login($datos->usuario, $datos->password);

    if ($accion['codigo'] == 400) {
        $cod = $accion['codigo'];
        $mensaje = $accion['mensaje'];
    } else {
        $user = $accion['usuario'];
        $accion = ControladorMina::validarAdmin($user);
        if ($accion['admin']) {
            $u = $datos->usuarioDelete;

            $accion = ControladorMina::eliminarUsuario($u);
        }

        $cod = $accion['codigo'];
        $mensaje = $accion['mensaje'];
        header("HTTP/1.1 " . $cod . ' ' . $mensaje);
    }
}
