<?php

require_once './Constantes.php';


class Conexion
{


    public static function consultarUsuarioAdministrador($id)
    {
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $admin = true;
        $consulta = "SELECT admin FROM " .Constantes::$TABLEUSER. " WHERE id = ? ";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultados = $stmt->get_result();
        $fila = $resultados->fetch_array();
        if ($fila[0] != 1) {
            $admin = false;
        }
        $resultados->free_result();
        $conexion->close();
        return $admin;
    }


    public static function consultarUsuarioExiste($user, $password)
    {
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);

        $consulta =
            "SELECT COUNT(*), id FROM " .Constantes::$TABLEUSER. " WHERE nombre = ? AND password = ? GROUP BY id";

        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("ss", $user, $password);
        $stmt->execute();
        $resultados = $stmt->get_result();

        if ($fila = $resultados->fetch_array()) {
            $user = ['n' => $fila[0], 'id' => $fila[1]];
        } else {
            $user = ['n' => 0];
        }
        $resultados->free_result();
        $conexion->close();
        return $user;
    }
    public static function consultarPartida($user)
    {
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $consulta = "SELECT * FROM " .Constantes::$TABLEMATCH. " WHERE idUsuario = ? and finalizada = 0";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("i", $user);
        $stmt->execute();
        $resultados = $stmt->get_result();
        $fila = $resultados->fetch_array();

        $p = new Partida($fila[0], $fila[1], $fila[2], $fila[3], $fila[4]);

        $resultados->free_result();
        $conexion->close();
        return $p;
    }



    public static function insertar($p)
    {
        $m = '';
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);

        $query = "INSERT INTO  " .Constantes::$TABLEMATCH. "  (idUsuario,tablero,oculto,finalizada) VALUES (?,?,?,?)";

        $stmt = $conexion->prepare($query);

        $stmt->bind_param("issi", $p->idUsuario, $p->tablero, $p->oculto, $p->terminado);

        try {
            $m = $stmt->execute();
        } catch (Exception $e) {
            $m = null;
        }


        $conexion->close();
        return $m;
    }

    public static function actualizarFinalizada($p)
    {

      $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $t = $p->terminado;
        $query = "UPDATE  " .Constantes::$TABLEMATCH. "  SET finalizada = ? WHERE id = ?;";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ii", $t, $p->id);
        $stmt->execute();

        $conexion->close();
    }





    public static function actualizarTableros($p)
    {

        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $query = "UPDATE  " .Constantes::$TABLEMATCH. "  SET oculto = ?, tablero = ? WHERE id= ?;";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ssi", $p->oculto, $p->tablero, $p->id);
        $stmt->execute();

        $conexion->close();
    }



    public static function consultarTerminadas($user)
    {
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $v = [];
        $query = "SELECT COUNT(*) FROM  " .Constantes::$TABLEMATCH. "  WHERE finalizada = 0 and idUsuario= ?";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param('i',$user);
        $stmt->execute();
        $resultados = $stmt->get_result();
        $fila = $resultados->fetch_array();

        if ($fila[0] == 1) {
            $v = ['contador' => $fila[0]];
        } else {
            $v = ['contador' => $fila[0]];
        }
        $resultados->free_result();
        $conexion->close();
        return $v;
    }



    public static function actualizarRankingGanadas($u)
    {

        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $query = "UPDATE " .Constantes::$TABLEUSER. " SET partidasGanadas = partidasGanadas + 1 WHERE id= ? ;";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $u);
        $stmt->execute();

        $conexion->close();
    }
    public static function actualizarRankingJugadas($u)
    {

        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $query = "UPDATE " .Constantes::$TABLEUSER. " SET partidasJugadas = partidasJugadas +1 WHERE id= ? ;";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $u);
        $stmt->execute();

        $conexion->close();
    }
    public static function devolverRanking()
    {
        $v = [];
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $query = "SELECT nombre, partidasGanadas, partidasJugadas FROM " .Constantes::$TABLEUSER. " ORDER BY partidasGanadas DESC;";
        $stmt = $conexion->prepare($query);
        $stmt->execute();
        $resultados = $stmt->get_result();

        while ($fila = $resultados->fetch_array()) {
            $v[] = ['usuario' => $fila[0], 'ganadas' => $fila[1], 'jugadas' => $fila[2]];
        }


        $conexion->close();
        return $v;
    }
    public static function rendirse($p)
    {
        $id = $p->id;
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $query = "UPDATE  " .Constantes::$TABLEMATCH. "  SET finalizada =  -1 WHERE id= ? ;";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $conexion->close();
    }

    public static function insertarUsuario($u)
    {

        $m = '';
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $query = "INSERT INTO " .Constantes::$TABLEUSER. " (password, nombre, admin) VALUES (?,?,?)";

        $stmt = $conexion->prepare($query);

        $stmt->bind_param("ssi", $u->password, $u->nombre, $u->admin);

        try {
            $m = $stmt->execute();
            $m = ['registrado' => true];
        } catch (Exception $e) {
            $m = ['registrado' => false, 'excepcion' => $e->getMessage()];
        }

        $conexion->close();
        return $m;
    }
    static function consultarUsuarioPorNombre($user)
    {
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $consulta = "SELECT * FROM " .Constantes::$TABLEUSER. " WHERE nombre= ? ";
        $stmt = $conexion->prepare($consulta);
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $resultados = $stmt->get_result();

        $v = [];
        try {
            $fila = $resultados->fetch_array();
            $u =  new Usuario($fila[0], $fila[1], $fila[2], $fila[5], $fila[3], $fila[4]);
            $v = ['usuario' => $u];
        } catch (Exception $e) {
            $v['excepcion'] = $e->getMessage();
        }
        $resultados->free_result();
        $conexion->close();
        return $v;
    }
    static function consultarTodosUsuario()
    {
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $consulta = "SELECT * FROM " .Constantes::$TABLEUSER. "";
        $stmt = $conexion->prepare($consulta);

        $stmt->execute();
        $resultados = $stmt->get_result();
        $v = [];
        try {
            while ($fila = $resultados->fetch_array()) {

                $u = new Usuario($fila[0], $fila[1], $fila[2], $fila[5], $fila[3], $fila[4]);
                $usuarios[] = $u;
            }
            $v = ['usuarios' => $usuarios];
        } catch (Exception $e) {
            $v = ['excepcion' => $e->getMessage()];
        }
        $resultados->free_result();
        $conexion->close();
        return $v;
    }
    public static function updatePassword($user, $password)
    {
        $v = [];
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $query = "UPDATE " .Constantes::$TABLEUSER. " SET password =  ? WHERE nombre = ? ;";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("ss", $password, $user);
        $stmt->execute();

        try {
            $stmt->execute();
            $v = ['update' => true];
        } catch (Exception $e) {
            $v = ['update' => false, 'excepcion' => $e->getMessage()];
        }
        $conexion->close();

        return $v;
    }
    public static function deleteUsuario($user)
    {
        $v = [];
        $conexion = new mysqli(Constantes::$DIRECCION, Constantes::$USER, Constantes::$PSWD, Constantes::$BDNAME);
        $query = "DELETE FROM " .Constantes::$TABLEUSER. "  WHERE nombre = ? ;";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param("s", $user);
        try {
            $stmt->execute();
            $v = ['delete' => true];
        } catch (Exception $e) {
            $v = ['delete' => false, 'excepcion' => $e->getMessage()];
        }
        $conexion->close();
        return $v;
    }
}
