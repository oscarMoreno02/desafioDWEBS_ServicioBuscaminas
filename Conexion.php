<?php




class Conexion
{
    public $conexion;
    static $DIRECCION = 'localhost';
    static $USER = 'oscar';
    static $PSWD = '123';
    static $BDNAME = 'minas';

    public static function consultarUsuarioExiste($user,$password){
        $conexion = mysqli_connect(self::$DIRECCION, self::$USER, self::$PSWD, self::$BDNAME);
        
        $consulta =
         "SELECT COUNT(*), id FROM usuario WHERE nombre = '".$user."' AND password = '".$password."' GROUP BY id";

        $stmt = mysqli_prepare($conexion, $consulta);
        mysqli_stmt_execute($stmt);
        $resultados = mysqli_stmt_get_result($stmt);
        
        if($fila = mysqli_fetch_array($resultados)){
            $user=['n'=>$fila[0],'id'=> $fila[1]];
        }else{
            $user=['n'=>0];
        }
        
        return $user;
    }
   public static function consultarPartida($user)
    {
        $conexion = mysqli_connect(self::$DIRECCION, self::$USER, self::$PSWD, self::$BDNAME);
            $consulta = "SELECT * FROM partida WHERE idUsuario = ? and finalizada = 0";
            $stmt = mysqli_prepare($conexion, $consulta);
            mysqli_stmt_bind_param($stmt, "i", $user);
            mysqli_stmt_execute($stmt);
            $resultados = mysqli_stmt_get_result($stmt);
            $fila = mysqli_fetch_array($resultados);

                $p = new Partida($fila[0],$fila[1],$fila[2],$fila[3],$fila[4]);
                
            
        

        mysqli_close($conexion);
        return $p;
    }



    public static function insertar($p)
    {
        $m = '';
        $conexion = mysqli_connect(self::$DIRECCION, self::$USER, self::$PSWD, self::$BDNAME);
        $query = "INSERT INTO partida (idUsuario,tablero,oculto,finalizada) VALUES (?,?,?,?)";
        
        $stmt = mysqli_prepare($conexion, $query);

        mysqli_stmt_bind_param($stmt, "issi",$p->idUsuario,$p->tablero, $p->oculto, $p->terminado);

        try {
            $m = mysqli_stmt_execute($stmt);
        } catch (Exception $e) {
            $m = null;
        }

        mysqli_close($conexion);
        return $m;
    }

   public static function actualizarFinalizada($p){
      
        $conexion = mysqli_connect(self::$DIRECCION, self::$USER, self::$PSWD, self::$BDNAME);
        $t=$p->terminado;
        $query = "UPDATE partida SET finalizada = ? WHERE id = ?;";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "ii", $t, $p->id);
        mysqli_stmt_execute($stmt);
        mysqli_close($conexion);

    }





    public static function actualizarTableros($p){

        $conexion = mysqli_connect(self::$DIRECCION, self::$USER, self::$PSWD, self::$BDNAME);
        $query = "UPDATE partida SET oculto ='".$p->oculto."', tablero = '".$p->tablero."' WHERE id= ". $p->id.";";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_execute($stmt);
        mysqli_close($conexion);

    }



    public static function consultarTerminadas($user){
        $conexion = mysqli_connect(self::$DIRECCION, self::$USER, self::$PSWD, self::$BDNAME);
        $query = "SELECT COUNT(*) FROM partida WHERE finalizada = 0 and idUsuario= '".$user."'";
        $stmt = mysqli_prepare($conexion, $query);

        mysqli_stmt_execute($stmt);
        $resultados = mysqli_stmt_get_result($stmt);
        $fila=mysqli_fetch_array($resultados);
        if($fila[0]==1){
            return ['contador'=>$fila[0]];
        }else{
            return ['contador'=>$fila[0]];
        }
        
    }



    public static function actualizarRankingGanadas($u){
        
        $conexion = mysqli_connect(self::$DIRECCION, self::$USER, self::$PSWD, self::$BDNAME);
        $query = "UPDATE usuario SET partidasGanadas = partidasGanadas + 1 WHERE id= ? ;";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "i", $u);
        mysqli_stmt_execute($stmt);
        mysqli_close($conexion);

    }
    public static function actualizarRankingJugadas($u){
        
        $conexion = mysqli_connect(self::$DIRECCION, self::$USER, self::$PSWD, self::$BDNAME);
        $query = "UPDATE usuario SET partidasJugadas = partidasJugadas +1 WHERE id= ? ;";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_bind_param($stmt, "i", $u);
        mysqli_stmt_execute($stmt);
        mysqli_close($conexion);

    }
    public static function devolverRanking(){
        $v=[];
        $conexion = mysqli_connect(self::$DIRECCION, self::$USER, self::$PSWD, self::$BDNAME);
        $query = "SELECT nombre, partidasGanadas, partidasJugadas FROM usuario ORDER BY partidasGanadas DESC;";
        $stmt = mysqli_prepare($conexion, $query);
        mysqli_stmt_execute($stmt);
        $resultados = mysqli_stmt_get_result($stmt);

        while($fila=mysqli_fetch_array($resultados)){
            $v[]=['usuario'=>$fila[0],'ganadas'=>$fila[1],'jugadas'=>$fila[2]];
        }


        mysqli_close($conexion);
        return $v;
    }
   
}

