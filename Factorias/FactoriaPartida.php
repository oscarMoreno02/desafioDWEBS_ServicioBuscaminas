<?php
class FactoriaPartida
{
     public static function generarPartida($tamaño, $minas,$user)
     {
          $tablero = array_fill(0,$tamaño , '*');
          $restantes = 0;
          while ($restantes < $minas) {
               $posicionAleatoria = rand(0, $tamaño - 1);
               if ($tablero[$posicionAleatoria] != Constantes::$MINA) {
                    $tablero[$posicionAleatoria] = Constantes::$MINA;
                    $restantes++;
               }
          }
          
          $tableroOculto=array_fill(0,$tamaño,'*');

          return new Partida(0,$user,implode('',$tablero),implode('',$tableroOculto),0);
     }



}
