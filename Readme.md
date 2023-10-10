                    GET
                    
<------------------------>
Ruta: http:://127.0.0.1:9090/
JSon:
{
    "usuario":"xxxx",
    "password":"xxxx"
}
Resolucion: Devolvera la partida en curso o genera y devuelve una partida con tablero y minas si el usuario es correcto.
<------------------------>


<------------------------>
Ruta: http:://127.0.0.1:9090/{tamTablero}/{numMinas}
JSon:
{
    "usuario":"xxxx",
    "password":"xxxx"
}
Resolucion:  Devolvera la partida en curso o genera y devuelve una partida con las casillas y minas pasadas por url  si el usuario es correcto.
<------------------------>


<------------------------>
Ruta: http:://127.0.0.1:9090/consultar
JSon:
{
    "usuario":"xxxx",
    "password":"xxxx"
}
Resolucion: Devolvera los datos de todos los usuarios de la base de datos si el usuario introducido es administrador
<------------------------>


<------------------------>
Ruta: http:://127.0.0.1:9090/consultar/{nombreUsuarioConsultar}
JSon:
{
    "usuario":"xxxx",
    "password":"xxxx"
}
Resolucion: Devolvera los datos de un usuario de la base de datos si el usuario introducido es administrador y el nombre del usuario a consultar existe.
<------------------------>