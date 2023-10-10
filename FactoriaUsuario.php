<?php
     require_once './Usuario.php';
class FactoriaUsuario
{
     public static function generarNuevoUsuario($p, $n, $a)
     {

          $user = new Usuario(0, $p, $n, $a, 0, 0);
          return $user;
     }

     public static function crearObjetoUsuarioCompleto($i, $p, $n, $a, $pj, $pg)
     {

          return new Usuario($i, $p, $n, $a, $pj, $pg);
     }

     public static function generarPasswordAleatoriamente()
     {
          $letras = ['a', 'e', 'i', 'o', 'u'];
          $longitud = rand(Constantes::$MINPASSWORD, Constantes::$MAXPASSWORD);
          $password = [];
          for ($i = 0; $i <= $longitud; $i++) {
               $n = rand(0, 100);
               if ($n % 2 == 0) {
                    $password[] = rand(0, 9);
               } else {
                    $password[] = $letras[rand(0, count($letras) - 1)];
               }
          }

          return implode('', $password);
     }
}
