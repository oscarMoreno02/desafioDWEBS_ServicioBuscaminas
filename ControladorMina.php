<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';

class ControladorMina
{

  public static function login($usuario, $password)
  {
    $p = md5($password);
    $user = Conexion::consultarUsuarioExiste($usuario, $p);

    if ($user['n'] == 1) {
      $v = ['usuario' => $user['n'], 'codigo' => 200];
    } else {
      $v = ['mensaje' => 'Error de usuario', 'codigo' => 400];
    }
    return $v;
  }


  public static function nuevaPartida($tamaño, $minas, $user)
  {
    $partida = FactoriaPartida::generarPartida($tamaño, $minas, $user);
    $v = [];
    if (Conexion::insertar($partida) != null) {
      $v = ['codigo' => 200, 'partida' => $partida, 'mensaje' => 'Partida creada correctamente', 'usuario' => $user];
    } else {
      $v = ['codigo' => 400, 'mensaje' => 'Error al crear la partida'];
    }
    return $v;
  }

  public static function partidaPendienteExiste($user)
  {
    $n = Conexion::consultarTerminadas($user);

    if ($n['contador'] == 1) {

      return ['codigo' => 201, 'mensaje' => 'Ya existe una partida creada', 'usuario' => $user];
    } else {
      return ['codigo' => 200, 'usuario' => $user];
    }
  }

  public static function obtenerPartida($user)
  {

    $p = Conexion::consultarPartida($user);

    return $p;
  }
  public static function juegaRonda($partida, $casilla, $user)
  {
    $v = ['mensaje' => 'error al jugar', 'codigo' => 400];
    if ($partida->abrirCasilla($casilla) == 0) {


      Conexion::actualizarFinalizada($partida);
      Conexion::actualizarTableros($partida);
      $v = ['mensaje' => 'Has perdido la partida', 'codigo' => 200, 'partida' => $partida->tablero];
      Conexion::actualizarRankingJugadas($user);
    } else {
      if ($partida->comprobarGanada()) {

        $v = ['mensaje' => 'Has ganado la partida ', 'codigo' => 200, 'partida' => $partida->tablero];
        Conexion::actualizarRankingJugadas($user);
        Conexion::actualizarRankingGanadas($user);
        Conexion::actualizarFinalizada($partida);
        Conexion::actualizarTableros($partida);
      } else {
        $v = ['mensaje' => 'Continua la partida', 'codigo' => 200, 'partida' => $partida->oculto];

        Conexion::actualizarTableros($partida);
      }
    }

    return $v;
  }
  public static function mostrarRanking()
  {
    $v = Conexion::devolverRanking();
    return $v;
  }

  public static function rendicion($user, $p)
  {
    Conexion::rendirse($p);
    Conexion::actualizarRankingJugadas($user);
    return ['mensaje' => 'Se ha rendido de la partida numero  ' . $p->id, 'codigo' => 200, 'partida' => $p->tablero];
  }

  public static function validarAdmin($id)
  {
    $v = [];
    if (Conexion::consultarUsuarioAdministrador($id)) {

      $v = ['mensaje' => "ok", 'codigo' => 200, 'admin' => true];
    } else {
      $v = ['mensaje' => 'No tienes permisos', 'codigo' => 403, 'admin' => false];
    }
    return $v;
  }


  public static function registrarUsuario($password, $nombre, $admin)
  {

    $usuario = FactoriaUsuario::generarNuevoUsuario($password, $nombre, $admin);
    $registro = Conexion::insertarUsuario($usuario);
    if ($registro['registrado']) {
      $v = ['mensaje' => 'Se ha registrado correctamente', 'codigo' => 201];
    } else {
      $v = ['mensaje' => 'No se pudo completar la inserccion', 'codigo' => 400, 'excepcion' => $registro['excepcion']];
    }
    return $v;
  }

  public static function listarUnUsuario($user)
  {
    $u = Conexion::consultarUsuarioPorNombre($user);
    $v = [];
    if ($u['excepcion']) {
      $v = ['codigo' => 404, 'mensaje' => 'no encontrado', 'excepcion' => $u['excepcion']];
    } else {
      $v = ['codigo' => 200, 'mensaje' => 'Encontrado', 'usuario' => $u['usuario']];
    }
    return $v;
  }

  public static function listarTodosUsuarios()
  {
    $u = Conexion::consultarTodosUsuario();
    $v = [];
    if ($u['excepcion']) {
      $v = ['codigo' => 500, 'mensaje' => 'Error de servidor', 'excepcion' => $u['excepcion']];
    } else {
      $v = ['codigo' => 200, 'mensaje' => 'Encontrado correctamente', 'usuarios' => $u['usuarios']];
    }
    return $v;
  }

  public static function cambiarPassword($user, $password)
  {
    $v = [];
    $p = md5($password);
    $u = Conexion::updatePassword($user, $p);
    if ($u['update']) {
      $u = ['mensaje' => 'Modificado correctamente', 'codigo' => 200];
    } else {

      $u = ['codigo' => 412, 'mensaje' => 'Error en la modificacion', 'excepcion' => $u['excepcion']];
    }
    return $u;
  }
  public static function eliminarUsuario($user)
  {
    $u = Conexion::deleteUsuario($user);
    $v = [];
    if ($u['delete' == true]) {
      $v = ['codigo' => 404, 'mensaje' => 'Error al eliminar', 'excepcion' => $u['excepcion']];
    } else {
      $v = ['codigo' => 200, 'mensaje' => 'Usuario Eliminado', 'usuario' => $u['usuario']];
    }
    return $v;
  }
  public static function recuperarCuenta($email, $user)
  {
    $newPassword = FactoriaUsuario::generarPasswordAleatoriamente();
    $v = self::cambiarPassword($user, $newPassword);
    if ($v['codigo']==200) {
      try {
        $mail = new PHPMailer();


        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'auxiliardaw2@gmail.com';
        $mail->Password   = 'gaxrwhgytqclfiyd';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port       = 465;

        $mail->setFrom('auxiliardaw2@gmail.com', 'Fernando Aranzabe');

        $mail->addAddress($email, $user);

        $mail->isHTML(true);
        $mail->Subject = 'Recuperacion de cuenta';
        $mail->Body    = 'Usuario: ' . $user . ' su nueva contraseña es: <b>' . $newPassword . '</b>';
        $mail->AltBody = 'Usuario: ' . $user . ' su nueva contraseña es:' . $newPassword ;

        $mail->send();
        $v = ['mensaje' => 'El mensaje ha sido enviado', 'codigo' => 200];
      } catch (Exception $e) {
        $v = ['mensaje' => 'No se pudo enviar el mensaje. Error de correo: {$mail->ErrorInfo}', 'codigo' => 404];
      }
    }else{
      $v=['mensaje'=>'Error al procesar la solicitud','codigo'=>404];
    }
    return $v;
  }
}
