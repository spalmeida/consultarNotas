<?php

namespace Connect;

use Connect\Connect;
use PDO;



class DB extends PDO{



	private $Connect;



	public function __construct(){

		$this->Connect = new Connect;

	}



	public function listar($table, $where =""){



		$conn = $this->Connect->conn();

		$sth  = $conn->prepare("SELECT * FROM $table");

		$sth->execute();



		$result = $sth->fetchall(PDO::FETCH_ASSOC);



		return $result;

	}



}





?>