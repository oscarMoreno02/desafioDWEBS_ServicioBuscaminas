<?php
require_once './Conexion.php';
require_once './Partida.php';
require_once './FactoriaPartida.php';
require_once './ControladorMina.php';


header("Content-Type:application/json");
$requestMethod = $_SERVER["REQUEST_METHOD"];
$paths = $_SERVER['REQUEST_URI'];
$argus = explode('/', $paths);
$cod;

unset($argus[0]);
$json=file_get_contents("php://input");
$datos=json_decode($json);
$accion;

if ($requestMethod=='GET'){
    $accion= ControladorMina::Login($datos->usuario,$datos->password);
// añadir mensaje y codigo alternativo para cuando se crea una partida con parametros por url
//añadir mensaje y codigo de error cuando el usuario no es correcto
    if($accion['codigo']==400){

        $cod=$accion['codigo'];
        $mensaje=$accion['mensaje'];

    }else{
        $accion=ControladorMina::partidaPendienteExiste($accion['usuario']);
        if($accion['codigo']==200){
            if (count($argus)==1){
             
                $accion=ControladorMina::nuevaPartida(10,2,$accion['usuario']);
                $cod=$accion['codigo'];
                $mensaje=$accion['mensaje'];
                header("HTTP/1.1 " . $cod.' '.$mensaje);
                echo json_encode(['tablero'=>$accion['partida']->oculto]);

            }else{
                $accion=ControladorMina::nuevaPartida((int)$argus[1],(int)$argus[2],$accion['usuario']);
                header("HTTP/1.1 " . $cod.' '.$mensaje);
                echo json_encode(['tablero'=>$accion['partida']->oculto]);
            }

        }else{
            $p=ControladorMina::obtenerPartida($accion['usuario']);
            $cod=$accion['codigo'];
            $mensaje=$accion['mensaje'];
           
            header("HTTP/1.1 " . $cod.' '.$mensaje);
            echo json_encode(['tablero'=>$p->oculto]);
        }

    }

}
if ($requestMethod=='POST'){
    $accion= ControladorMina::Login($datos->usuario,$datos->password);

    if($accion['codigo']==400){

        $cod=$accion['codigo'];
        $mensaje=$accion['mensaje'];

    }else{
        $accion=ControladorMina::partidaPendienteExiste($accion['usuario']);

            if($accion['codigo']==201){
                
                $p=ControladorMina::obtenerPartida($accion['usuario']);
                $accion=ControladorMina::juegaRonda($p,$datos->casilla);

            }else{
                $accion=['codigo'=>404,'mensaje'=>'No se ha encontrado ningun partida'];
            }


            $cod=$accion['codigo'];
            $mensaje=$accion['mensaje'];
            header("HTTP/1.1 " . $cod.' '.$mensaje);
            echo json_encode(['tablero'=>$accion['partida']]);
        
    }

}










