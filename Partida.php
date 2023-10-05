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
			$this->terminado=-1;
			$this->oculto=$this->tablero;
			return 0;
		}else{
			
			$this->revelarPistas($casilla);
			return 1;
		}
	}
	
	 public function revelarPistas($casilla){
		$cercanas=0;
		$vector=str_split($this->tablero);
		if($vector[$casilla-1]==9){
			$cercanas++;
		}
		if($vector[$casilla+1]==9){
			$cercanas++;
		}
		$o=str_split($this->oculto);
		$vector[$casilla]=$cercanas;
		$o[$casilla]=$cercanas;
		$this->tablero=implode('',$vector);
		$this->oculto=implode('',$o);
		
	 }
	 function comprobarGanada(){
		$i=0;
		$o=str_split($this->oculto);
		$tablero=str_split($this->tablero);
		$ganada=false;
		$c1=0;
		$c2=0;
		
		while($i<count($o)){
			if($o[$i]=='*'){
				$c1++;
			}
			if($tablero[$i]=='9'){
				$c2++;
			}
			$i++;
		}

		if ($c1==$c2){
			$ganada=true;
			$this->terminado=1;
		}
		return $ganada;
	 }
}