<?php
class FactoriaPartida
{
     public static function generarPartida($tamaño, $minas,$user)
     {
          $tablero = array_fill(0, $tamaño - 1, '*');
          $restantes = 0;
          while ($restantes < $minas) {
               $posicionAleatoria = rand(0, $tamaño - 1);
               if ($tablero[$posicionAleatoria] != 9) {
                    $tablero[$posicionAleatoria] = 9;
                    $restantes++;
               }
          }
          
          $tableroOculto=array_fill(0,$tamaño-1,'*');

          return new Partida(0,$user,implode('',$tablero),implode('',$tableroOculto),0);
     }



}
