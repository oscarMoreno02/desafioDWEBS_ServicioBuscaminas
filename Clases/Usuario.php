<?php

 class Usuario{
    
    public $id;
    public $password;
    public $nombre;
    public $admin;
    public $partidasJugadas;
    public $partidasGanadas;

    public function __construct($i,$p,$n,$a,$pj,$pg){
        $this->id=$i;
        $this->password=$p;
        $this->nombre=$n;
        $this->admin=$a;
        $this->partidasGanadas=$pg;
        $this->partidasJugadas=$pj;
    }


 }