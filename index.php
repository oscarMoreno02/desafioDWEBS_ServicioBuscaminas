<?php
require_once './Conexion.php';
require_once './Partida.php';
require_once './FactoriaPartida.php';
require_once './ControladorMina.php';

// print_r(ControladorMina::mostrarRanking());
header("Content-Type:application/json");
$requestMethod = $_SERVER["REQUEST_METHOD"];
$paths = $_SERVER['REQUEST_URI'];
$argus = explode('/', $paths);
$cod;

unset($argus[0]);
$json=file_get_contents("php://input");
$datos=json_decode($json);
$accion;
$mostrar=[];
if ($requestMethod=='GET'){
    $accion= ControladorMina::Login($datos->usuario,$datos->password);

    if($accion['codigo']==400){

        $cod=$accion['codigo'];
        $mensaje=$accion['mensaje'];
        header("HTTP/1.1 " . $cod.' '.$mensaje);
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
                $cod=$accion['codigo'];
                $mensaje=$accion['mensaje'];
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
        $user=$accion['usuario'];
        $accion=ControladorMina::partidaPendienteExiste($accion['usuario']);

            if($accion['codigo']==201){
                
                $p=ControladorMina::obtenerPartida($accion['usuario']);
                if($datos->rendirse!=null){
                   $accion= ControladorMina::rendicion($user,$p);
                    
                }else{
                    $accion=ControladorMina::juegaRonda($p,$datos->casilla,$user);
                }
                

            }else{
                $accion=['codigo'=>404,'mensaje'=>'No se ha encontrado ningun partida'];
            }


            $cod=$accion['codigo'];
            $mensaje=$accion['mensaje'];
            header("HTTP/1.1 " . $cod.' '.$mensaje);
            echo json_encode(['tablero'=>$accion['partida']]);
        
    }

}

if ($requestMethod=='PUT'){
    $accion= ControladorMina::Login($datos->usuario,$datos->password);

    if($accion['codigo']==400){
        $cod=$accion['codigo'];
        $mensaje=$accion['mensaje'];
    }else{
        $user=$accion['usuario'];
        $accion=ControladorMina::validarAdmin($user);
        if($accion['admin']){
            //provisional para comprobar si es correcto
            print_r('Es administrador');
        }
    }
            // header("HTTP/1.1 " . $cod.' '.$mensaje);
            // echo json_encode(['tablero'=>$accion['partida']]);

}








