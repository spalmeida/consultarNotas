<?php 

namespace Connect;
use Connect\Connect;

class System extends Connect{

	private $busca;
	private $setor;
	private $user;
	private $company_active;
	private $hoje;


	public function start(){

		$this->busca = new Connect;

		$user_id = $_SESSION['user_id'];
		$this->user = $this->busca->select('system_users', "id = $user_id");

		$company_active_id = $this->user[0]['company_activation'];
		$this->company_active = $this->busca->select('system_companys', "id = $company_active_id");

		$setor = $this->user[0]['user_sector'];
		$this->hoje = date('d');


		return System::createArray();

	}

	public function createArray(){
		return array(
			'user_id' => $this->user[0],
			'company_active' =>$this->company_active[0]
		);
	}

}