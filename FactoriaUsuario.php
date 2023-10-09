<?php
class FactoriaUsuario
{
     public static function generarNuevoUsuario($p,$n,$a)
     {
    
        $user=new Usuario(0,$p,$n,$a,0,0);
         return $user;
     }

     public static function crearObjetoUsuarioCompleto($i,$p,$n,$a,$pj,$pg)
     {
        
          return new Usuario($i,$p,$n,$a,$pj,$pg);
     }



}
