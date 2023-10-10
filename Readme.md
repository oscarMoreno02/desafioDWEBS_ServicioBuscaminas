<--------------------------------------------------------------------------------------------------------------------------->
                                                GET
<--------------------------------------------------------------------------------------------------------------------------->


<------------------------>
Ruta: http:://127.0.0.1:9090/ranking
JSon:
{
    "usuario": "xxxx",
    "password": "xxxx"
}

Resolucion: Devolvera el ranking con nombre de usuario, victorias y partidas jugadas ordenadas por las victorias si el usuario es correcto.
<------------------------>


<------------------------>
Ruta: http:://127.0.0.1:9090
JSon:
{
    "usuario": "xxxx",
    "password": "xxxx"
}

Resolucion: Devolvera la partida en curso o genera y devuelve una partida con tablero y minas si el usuario es correcto.
<------------------------>


<------------------------>
Ruta: http:://127.0.0.1:9090/{tamTablero}/{numMinas}
JSon:
{
    "usuario": "xxxx",
    "password": "xxxx"
}

Resolucion:  Devolvera la partida en curso o genera y devuelve una partida con las casillas y minas pasadas por url  si el usuario es correcto.
<------------------------>


<------------------------>
Ruta: http:://127.0.0.1:9090/consultar
JSon:
{
    "usuario": "xxxx",
    "password": "xxxx"
}

Resolucion: Devolvera los datos de todos los usuarios de la base de datos si el usuario introducido es administrador
<------------------------>


<------------------------>
Ruta: http:://127.0.0.1:9090/consultar/{nombreUsuarioConsultar}
JSon:
{
    "usuario": "xxxx",
    "password": "xxxx"
}

Resolucion: Devolvera los datos de un usuario de la base de datos si el usuario introducido es administrador y el nombre del usuario a consultar existe.
<------------------------>



<--------------------------------------------------------------------------------------------------------------------------->
                                                POST
<--------------------------------------------------------------------------------------------------------------------------->


<------------------------>
Ruta: http:://127.0.0.1:9090
JSon:
{
    "usuario": "xxxx",
    "password": "xxxx",
    "casilla": "xxxxx"
}

Resolucion: Si el usuario y la contraseña son correctos, abrira una casilla del tablero oculto y se le mostrara de nuevo el tablero.
<------------------------>


<------------------------>
Ruta: http:://127.0.0.1:9090/nuevo
JSon:
{
    "usuario": "xxxx",
    "password": "xxxx",
    "nuevoNombre": "xxxxx",
    "nuevoPassword": "xxxxx",
    "adm": "xxxxx"
}

Resolucion: Si el usuario y la contraseña son correctos y es administrador, insertara los campos del nuevo usuario en la base de datos.
<------------------------>



<--------------------------------------------------------------------------------------------------------------------------->
                                                PUT
<--------------------------------------------------------------------------------------------------------------------------->


<------------------------>
Ruta: http:://127.0.0.1:9090/recuperacion
JSon:

{
    "usuario": "xxxxx",
    "email": "xxxxx"
}

Resolucion: Enviara al correo electronico una contraseña nueva para el usuario introducido
<------------------------>


<------------------------>
Ruta: http:://127.0.0.1:9090
JSon:

{
    "usuario": "xxxxx",
    "password": "xxxxx",
    "usuarioUpdate": "xxxxx",
    "passwordUpdate": "xxxxx"
}

Resolucion: Si el usuario es correcto y administrador, cambiara la contraseña al usuario indicado
<------------------------>



<--------------------------------------------------------------------------------------------------------------------------->
                                                DELETE
<--------------------------------------------------------------------------------------------------------------------------->


<------------------------>
Ruta: http:://127.0.0.1:9090
JSon:

{
    "usuario": "xxxxx",
    "password": "xxxxx",
    "usuarioDelete": "xxxxx"
}

Resolucion: Si el usuario es correcto y administrador, eliminara de la base de datos al usuario indicado
<------------------------>