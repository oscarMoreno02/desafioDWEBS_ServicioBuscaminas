<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require_once 'phpmailer/src/Exception.php';
require_once 'phpmailer/src/PHPMailer.php';
require_once 'phpmailer/src/SMTP.php';

class ControladorUsuario{
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
    
    }
  

    public static function recuperarCuenta($email, $user)
    {
      $newPassword = FactoriaUsuario::generarPasswordAleatoriamente();
      $v = ControladorAdmin::cambiarPassword($user, $newPassword,1);
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
  
    }

}