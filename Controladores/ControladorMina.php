<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';
require_once './Factorias/FactoriaUsuario.php';
require_once './Factorias/FactoriaPartida.php';
class ControladorMina
{
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
  
  public static function rendicion($user, $p)
  {
    Conexion::rendirse($p);
    Conexion::actualizarRankingJugadas($user);
    header("HTTP/1.1 200 Se ha rendido de la partida");
    echo json_encode(['partida' => $p->tablero]);
  }

 
 
}
