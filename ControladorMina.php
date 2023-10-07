<?php
class ControladorMina{

   public static function login($usuario,$password){
      
      $user=Conexion::consultarUsuarioExiste($usuario,$password);
      
      if ($user['n']==1)
      {
           $v= ['usuario'=>$user['n'],'codigo'=>200];
      }else{
           $v=['mensaje'=>'Error de usuario','codigo'=>400];
      }
      return $v;
   }
   
   
  public static function nuevaPartida($tamaño,$minas,$user){
    $partida=FactoriaPartida::generarPartida($tamaño,$minas,$user);
    $v=[];
    if(Conexion::insertar($partida)!=null){
      $v= ['codigo'=>200,'partida'=>$partida,'mensaje'=>'Partida creada correctamente','usuario'=>$user];
    }else{
        $v= ['codigo'=>400,'mensaje'=>'Error al crear la partida'];
    }
      return $v;
   }

  public static function partidaPendienteExiste($user){
  $n= Conexion::consultarTerminadas($user);
    
    if($n['contador']==1){

      return ['codigo'=>201,'mensaje'=>'Ya existe una partida creada','usuario'=>$user];
    }else{
      return ['codigo'=>200,'usuario'=>$user] ;
    }
   }
  
  public static function obtenerPartida($user){
    
     $p=Conexion::consultarPartida($user);
    
     return $p;
  }
  public static function juegaRonda($partida,$casilla,$user){
    $v=['mensaje'=> 'error al jugar','codigo'=>400];
        if($partida->abrirCasilla($casilla)==0){

    
      Conexion::actualizarFinalizada($partida);
      Conexion::actualizarTableros($partida);
        $v= ['mensaje'=>'Has perdido la partida','codigo'=>200,'partida'=>$partida->tablero];
        Conexion::actualizarRankingJugadas($user);
  }else{
    if($partida->comprobarGanada()){
 
      $v= ['mensaje'=>'Has ganado la partida ','codigo'=>200,'partida'=> $partida->tablero];
      Conexion::actualizarRankingJugadas($user);
      Conexion::actualizarRankingGanadas($user);
      Conexion::actualizarFinalizada($partida);
      Conexion::actualizarTableros($partida);
    }else{
      $v= ['mensaje'=>'Continua la partida','codigo'=>200,'partida'=> $partida->oculto];
     
      Conexion::actualizarTableros($partida);
    }
  }
  
  return $v;

}
public static function mostrarRanking(){
  $v= Conexion::devolverRanking();
 return $v;
 }
}
 