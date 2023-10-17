# Endpoints API REST

## GET

### Ranking
- **Ruta**: `http:://127.0.0.1:9090/ranking`
- **JSON**:
    ```json
    {
        "usuario": "xxxx",
        "password": "xxxx"
    }
    ```
- **Resolución**: Devolverá el ranking con nombre de usuario, victorias y partidas jugadas ordenadas por las victorias si el usuario es correcto.

### Partida en Curso o Generar Nueva Partida
- **Ruta**: `http:://127.0.0.1:9090`
- **JSON**:
    ```json
    {
        "usuario": "xxxx",
        "password": "xxxx"
    }
    ```
- **Resolución**: Devolverá la partida en curso o generará y devolverá una partida con tablero y minas si el usuario es correcto.

### Partida en Curso o Generar Nueva Partida con Tamaño de Tablero y Número de Minas
- **Ruta**: `http:://127.0.0.1:9090/{tamTablero}/{numMinas}`
- **JSON**:
    ```json
    {
        "usuario": "xxxx",
        "password": "xxxx"
    }
    ```
- **Resolución**: Devolverá la partida en curso o generará y devolverá una partida con las casillas y minas pasadas por URL si el usuario es correcto.

### Consultar Usuarios (Solo Administradores)
- **Ruta**: `http:://127.0.0.1:9090/consultar`
- **JSON**:
    ```json
    {
        "usuario": "xxxx",
        "password": "xxxx"
    }
    ```
- **Resolución**: Devolverá los datos de todos los usuarios de la base de datos si el usuario introducido es administrador.

### Consultar Usuario por Nombre (Solo Administradores)
- **Ruta**: `http:://127.0.0.1:9090/consultar/{nombreUsuarioConsultar}`
- **JSON**:
    ```json
    {
        "usuario": "xxxx",
        "password": "xxxx"
    }
    ```
- **Resolución**: Devolverá los datos de un usuario de la base de datos si el usuario introducido es administrador y el nombre del usuario a consultar existe.

## POST

### Abrir Casilla del Tablero
- **Ruta**: `http:://127.0.0.1:9090`
- **JSON**:
    ```json
    {
        "usuario": "xxxx",
        "password": "xxxx",
        "casilla": "xxxxx"
    }
    ```
- **Resolución**: Si el usuario y la contraseña son correctos, abrirá una casilla del tablero oculto y mostrará de nuevo el tablero.

### Rendición en la Partida
- **Ruta**: `http:://127.0.0.1:9090/rendicion`
- **JSON**:
    ```json
    {
        "usuario": "xxxx",
        "password": "xxxx"
    }
    ```
- **Resolución**: Si el usuario y la contraseña son correctos, el usuario se rendirá en esa partida.

### Crear Nuevo Usuario (Solo Administradores)
- **Ruta**: `http:://127.0.0.1:9090/nuevo`
- **JSON**:
    ```json
    {
        "usuario": "xxxx",
        "password": "xxxx",
        "nuevoNombre": "xxxxx",
        "nuevoPassword": "xxxxx",
        "adm": "xxxxx"
    }
    ```
- **Resolución**: Si el usuario y la contraseña son correctos y es administrador, insertará los campos del nuevo usuario en la base de datos.

## PUT

### Recuperación de Contraseña
- **Ruta**: `http:://127.0.0.1:9090/recuperacion`
- **JSON**:
    ```json
    {
        "usuario": "xxxxx",
        "email": "xxxxx"
    }
    ```
- **Resolución**: Enviará al correo electrónico una contraseña nueva para el usuario introducido.

### Cambio de Contraseña de Usuario (Solo Administradores)
- **Ruta**: `http:://127.0.0.1:9090`
- **JSON**:
    ```json
    {
        "usuario": "xxxxx",
        "password": "xxxxx",
        "usuarioUpdate": "xxxxx",
        "passwordUpdate": "xxxxx"
    }
    ```
- **Resolución**: Si el usuario es correcto y administrador, cambiará la contraseña al usuario indicado.

## DELETE

### Eliminación de Usuario (Solo Administradores)
- **Ruta**: `http:://127.0.0.1:9090`
- **JSON**:
    ```json
    {
        "usuario": "xxxxx",
        "password": "xxxxx",
        "usuarioDelete": "xxxxx"
    }
    ```
- **Resolución**: Si el usuario es correcto y administrador, eliminará de la base de datos al usuario indicado.
