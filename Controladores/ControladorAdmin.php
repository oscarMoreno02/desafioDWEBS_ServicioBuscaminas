<?php
class ControladorAdmin{
    public static function validarAdmin($id)
    {
      $v = [];
      if (Conexion::consultarUsuarioAdministrador($id)) {
  
        return  ['mensaje' => "ok", 'codigo' => 200, 'admin' => true];
      } else {
        header("HTTP/1.1 403 No tienes permisos");
      }
   
    }
  
  
    public static function registrarUsuario($password, $nombre, $admin)
    {
      $p=md5($password);
      $usuario = FactoriaUsuario::generarNuevoUsuario($p, $nombre, $admin);
      $registro = Conexion::insertarUsuario($usuario);
      if ($registro['registrado']) {
      
        header("HTTP/1.1 201 Se ha registrado correctamente");
      } else {
        header("HTTP/1.1 400 Error en la inserccion");
      }
    }
  
    public static function listarUnUsuario($user)
    {
      $u = Conexion::consultarUsuarioPorNombre($user);
    
      if ($u['excepcion']) {
        header("HTTP/1.1 404 No encontrado");
      } else {
        header("HTTP/1.1 200 Usuario encontrado");
        echo json_encode(['usuario'=>$u['usuario']]);
      }
      
    }
  
    public static function listarTodosUsuarios()
    {
      $u = Conexion::consultarTodosUsuario();
      $v = [];
      if ($u['excepcion']) {
        header("HTTP/1.1 500 Error de servidor");
      } else {
        header("HTTP/1.1 200 Usuario encontrado");
        echo json_encode(['usuarios'=>$u['usuarios']]);
      }
     
    }
  
    public static function cambiarPassword($user, $password,$n)
    {
      $v = [];
      $p = md5($password);
      $u = Conexion::updatePassword($user, $p);
      if($n==0){
      if ($u['update']) {
         header("HTTP/1.1 200 Modificado correctamente");
      } else {
        header("HTTP/1.1 412 Error en la modificacion");
      }
    }else{
      return ['codigo'=>200];
    }
    
    }
  
    public static function eliminarUsuario($user)
    {
      $u = Conexion::deleteUsuario($user);
      $v = [];
      if ($u['delete' == true]) {
        $v = ['codigo' => 404, 'mensaje' => 'Error al eliminar', 'excepcion' => $u['excepcion']];
          header("HTTP/1.1 404 Error al eliminar ");
      } else {
         header("HTTP/1.1 200 Usuario eliminado");
    
      }
      return $v;
    }
}