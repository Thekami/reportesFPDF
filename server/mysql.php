<?php 

class Mysql{

var $dbCon;

	function __construct(){
		$this->conect();
	}

	public function conect(){
		$this->dbCon = new mysqli('localhost', 'root', 'toortoor', 'test');
		//$this->dbCon = new mysqli('localhost', 'root', 'toortoor', 'rutas');
		$this->dbCon->set_charset("utf8");
		//$this->dbCon = new mysqli('localhost', 'root', 'toor', 'rutas');
		if(!$this->dbCon)
			echo $this->show_error();
	}

	public function query($consult){
		$query = $this->dbCon->query($consult);
		if(!$query){
			$this->show_error();
		}
		else{
			return $query;
		}
	}

	private function show_error(){
		return $this->dbCon->connect_error;
	}

	public function query_assoc($consult){
		$vec = array();
		if($result = $this->query($consult)){
			while($fila = $result->fetch_assoc()){ $vec[] = $fila; }
		}
		return $vec;
	}

	public function exit_conect(){
		mysqli_close($this->dbCon);
	}

	public function destroy(){
		session_destroy();
		header("Location: /rutas");
		header("Location: /angel/rutas");
	}

	public function obtenerId(){
		return $this->dbCon->insert_id;
	}

	public function getUnidades(){
		return $this->query_assoc('SELECT * FROM unidades WHERE id_chofer != 1 AND status = 1');
	}

	public function getChoferes(){
		return $this->query_assoc('SELECT * FROM choferes WHERE asignado = 0 OR id = 1');
	    //return $this->query_assoc('SELECT * FROM getChoferes');
	}

	public function getCostoTurno(){
		$res = $this->query_assoc("SELECT costo_turno FROM configuraciones WHERE id = 1");
		return $res[0]["costo_turno"];
	}

	public function getCostoPasaje(){
		$res = $this->query_assoc("SELECT costo_pasaje FROM configuraciones WHERE id = 1");
		return $res[0]["costo_pasaje"];
	}

	public function DateTime(){
		$time = date('H:i:s', time());
		$date = date('d-m-Y');
		return array($time, $date);
	}

 
}
?>