<?php
class Partida{
    public $id;
    public $tablero;
    public $oculto;
    public $terminado;
	public $idUsuario;
    
    function __construct($i,$iu,$ta,$o,$te)
    {
        $this->id=$i;
		$this->idUsuario=$iu;
        $this->tablero=$ta;
        $this->oculto=$o;
        $this->terminado=$te;
    }

	public function getId() {
		return $this->id;
	}

	public function setId($value) {
		$this->id = $value;
	}

	public function getTablero() {
		return $this->tablero;
	}

	public function setTablero($value) {
		$this->tablero = $value;
	}

	public function getOculto() {
		return $this->oculto;
	}

	public function setOculto($value) {
		$this->oculto = $value;
	}

	public function getTerminado() {
		return $this->terminado;
	}

	public function setTerminado($value) {
		$this->terminado = $value;
	}

	public function abrirCasilla($casilla){
		if($this->tablero[$casilla]==9){
			$finalizada=-1;
			return 0;
		}else{
			
			$this->revelarPistas($casilla);
			return 1;
		}
	}
	
	 public function revelarPistas($casilla){
		$cercanas=0;
		if(explode('',$this->tablero[$casilla-1])==9){
			$cercanas++;
		}
		if(explode('',$this->tablero[$casilla+1])==9){
			$cercanas++;
		}
		$o=explode('',$this->oculto);
		$o[$casilla]=$cercanas;
		$this->oculto=implode('',$o);
	 }
	 function comprobarGanada(){
		$i=0;
		$o=explode('',$this->oculto);
		$ganada=true;
		while($ganada && $i<count($o)){
			if($o[$i]=='*'){
				$ganada=false;
			}
			$i++;
		}
		if ($ganada){
			$this->terminado=1;
		}
		return $ganada;
	 }
}