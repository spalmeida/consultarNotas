<?php

namespace Utility;
use Connect\Connect;

class Notifications extends Connect{

	private $busca;
	private $user;
	private $setor;
	private $hoje;


	public function start($user_id){

		$this->busca = new Connect;
		$this->user = $this->busca->select('system_users', "id = $user_id");
		$setor = $this->user[0]['user_sector'];
		$this->hoje = date('d');

	}

}





?>


