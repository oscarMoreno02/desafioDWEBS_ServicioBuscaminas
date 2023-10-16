<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
require_once './FactoriaUsuario.php';

class ControladorMina
{

  public static function login($usuario, $password)
  {
    $p = md5($password);
   
    $user = Conexion::consultarUsuarioExiste($usuario, $p);

    if ($user['n'] == 1) {
     return  $v = ['usuario' => $user['id'], 'codigo' => 200];
    } else {
            header("HTTP/1.1 400 error de usuario");
    }
    
  }


  public static function nuevaPartida($tamaño, $minas, $user)
  {
    $partida = FactoriaPartida::generarPartida($tamaño, $minas, $user);
    $v = [];
    if (Conexion::insertar($partida) != null) {
      $v = ['codigo' => 200, 'partida' => $partida, 'mensaje' => 'Partida creada correctamente', 'usuario' => $user];
      header("HTTP/1.1 200 Partida creada correctamente");
      echo json_encode(['tablero' => $partida->oculto]);
    } else {
      header("HTTP/1.1 400 Error al crear la partida");
      
    }
   
  }

  public static function partidaPendienteExiste($user)
  {
    $n = Conexion::consultarTerminadas($user);

    if ($n['contador'] == 1) {

      return ['codigo' => 200, 'mensaje' => 'Ya existe una partida creada', 'usuario' => $user];
    
    } else {
      return ['codigo' => 400, 'mensaje'=>'No existe ninguna partida en curso','usuario' => $user];
    }
  }

  public static function mostrarPartida($p)
  {
    
    header("HTTP/1.1 200 Partida encontrada");
    echo json_encode(['tablero' => $p->oculto]);
    
  }
  public static function obtenerPartida($user){
    $p = Conexion::consultarPartida($user);
    return $p;
  }
  public static function juegaRonda($partida, $casilla, $user)
  {
    $v = ['mensaje' => 'error al jugar', 'codigo' => 400];
    if ($partida->abrirCasilla($casilla) == 0) {


      Conexion::actualizarFinalizada($partida);
      Conexion::actualizarTableros($partida);
      Conexion::actualizarRankingJugadas($user);
        header("HTTP/1.1 200 Has perdido la partida");
        echo json_encode(['partida' => $partida->tablero]);
    } else {
      if ($partida->comprobarGanada()) {

        Conexion::actualizarRankingJugadas($user);
        Conexion::actualizarRankingGanadas($user);
        Conexion::actualizarFinalizada($partida);
        Conexion::actualizarTableros($partida);
        header("HTTP/1.1 200 Has ganado la partida");
        echo json_encode(['partida' => $partida->tablero]);
      } else {
        Conexion::actualizarTableros($partida);
        header("HTTP/1.1 200 Continua la partida");
        echo json_encode(['partida' => $partida->oculto]);
      }
    }


  }
  public static function mostrarRanking()
  {

    $r = Conexion::devolverRanking();
    if($r!=null){
      $v=['codigo'=>200,'mensaje'=>'Ranking procesado','ranking'=>$r];
      header('HTTP/1.1 200 Ranking procesado');
      echo json_encode(['ranking'=>$r]);
    } else{
      header("HTTP/1.1 400 Error al procesar el ranking");
    }
    
    return $v;
  }

  public static function rendicion($user, $p)
  {
    Conexion::rendirse($p);
    Conexion::actualizarRankingJugadas($user);
    header("HTTP/1.1 200 Se ha rendido de la partida");
    echo json_encode(['partida' => $p->tablero]);
  }

  public static function validarAdmin($id)
  {
    $v = [];
    if (Conexion::consultarUsuarioAdministrador($id)) {

      return  ['mensaje' => "ok", 'codigo' => 200, 'admin' => true];
    } else {
      header("HTTP/1.1 403 No tienes permisos");
    }
 
  }


  public static function registrarUsuario($password, $nombre, $admin)
  {
    $p=md5($password);
    $usuario = FactoriaUsuario::generarNuevoUsuario($p, $nombre, $admin);
    $registro = Conexion::insertarUsuario($usuario);
    if ($registro['registrado']) {
    
      header("HTTP/1.1 201 Se ha registrado correctamente");
    } else {
      header("HTTP/1.1 400 Error en la inserccion");
    }
  }

  public static function listarUnUsuario($user)
  {
    $u = Conexion::consultarUsuarioPorNombre($user);
  
    if ($u['excepcion']) {
      header("HTTP/1.1 404 No encontrado");
    } else {
      header("HTTP/1.1 200 Usuario encontrado");
      echo json_encode(['usuario'=>$u['usuario']]);
    }
    
  }

  public static function listarTodosUsuarios()
  {
    $u = Conexion::consultarTodosUsuario();
    $v = [];
    if ($u['excepcion']) {
      header("HTTP/1.1 500 Error de servidor");
    } else {
      header("HTTP/1.1 200 Usuario encontrado");
      echo json_encode(['usuarios'=>$u['usuarios']]);
    }
   
  }

  public static function cambiarPassword($user, $password,$n)
  {
    $v = [];
    $p = md5($password);
    $u = Conexion::updatePassword($user, $p);
    if($n==0){
    if ($u['update']) {
       header("HTTP/1.1 200 Modificado correctamente");
    } else {
      header("HTTP/1.1 412 Error en la modificacion");
    }
  }else{
    return ['codigo'=>200];
  }
  
  }

  public static function eliminarUsuario($user)
  {
    $u = Conexion::deleteUsuario($user);
    $v = [];
    if ($u['delete' == true]) {
      $v = ['codigo' => 404, 'mensaje' => 'Error al eliminar', 'excepcion' => $u['excepcion']];
        header("HTTP/1.1 404 Error al eliminar ");
    } else {
       header("HTTP/1.1 200 Usuario eliminado");
  
    }
    return $v;
  }
  public static function recuperarCuenta($email, $user)
  {
    $newPassword = FactoriaUsuario::generarPasswordAleatoriamente();
    $v = self::cambiarPassword($user, $newPassword,1);
    if ($v['codigo'] == 200) {
      try {
        $mail = new PHPMailer();
        $mail->isSMTP();
        $mail->Host       = Constantes::$MAILHOST;
        $mail->SMTPAuth   = Constantes::$MAILAUTH;
        $mail->Username   = Constantes::$MAILUSERNAME;
        $mail->Password   = Constantes::$MAILPASSWORD;
        $mail->SMTPSecure = Constantes::$MAILSECURE;
        $mail->Port       = Constantes::$MAILPORT;

        $mail->setFrom(Constantes::$MAILFROMMAIL, Constantes::$MAILFROMNAME);

        $mail->addAddress($email, $user);

        $mail->isHTML(true);
        $mail->Subject = 'Recuperacion de cuenta';
        $mail->Body    = 'Usuario: ' . $user . ' su nueva contraseña es: <b>' . $newPassword . '</b>';
        $mail->AltBody = 'Usuario: ' . $user . ' su nueva contraseña es:' . $newPassword;

        $mail->send();
        header("HTTP/1.1 200 El mensaje ha sido enviado");
    
      } catch (Exception $e) {
       
        header("HTTP/1.1 400 No se pudo enviar el mensaje");
      }
    } else {
   
      header("HTTP/1.1 404 Error al procesar la solicitud");
      
    }
    return $v;
  }
}
