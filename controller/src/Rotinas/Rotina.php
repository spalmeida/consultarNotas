<?php

namespace Rotina;
use Connect\Connect;

class Rotina extends Connect{


	private $busca;
	private $user;
	private $rotinas;
	private $empresas;
	private $hoje;
	private $rotina_realiza;


	public function start($user_id){

		$this->busca = new Connect;
		$this->user = $this->busca->select('system_users', "id = $user_id");

		$setor = $this->user[0]['user_sector'];
		$this->rotinas = $this->busca->select("rotinas", "setor = '$setor'");
		$this->empresas = $this->busca->select("system_companys", "company_cnpj != '' ORDER BY company_number ASC");
		$this->hoje = date('d');
		$this->rotina_realiza = $this->busca->select("rotina_realiza", "data_inicio = '".date('Y-m-d')."' and user_sector = '$setor'");

	}

	public function listarRotinas(){

		return $this->rotinas;

	}

	public function listarEmpresas(){

		return $this->empresas;

	}

	public function unique_multidim_array($array, $key) {
		$temp_array = array();
		$i = 0;
		$key_array = array();

		foreach($array as $val) {
			if (!in_array($val[$key], $key_array)) {
				$key_array[$i] = $val[$key];
				$temp_array[$i] = $val;
			}
			$i++;
		}
		return $temp_array;
	}

	public function realizarRotina(){

		foreach (Rotina::listarEmpresas() as $empresa) {

			foreach (Rotina::rotinasDoDIa() as $rotina) {

				if(in_array($empresa['id'],explode(',', $rotina['company_id'])) || 
					in_array('geral',explode(',', $rotina['company_id']))){
					$info[] = $empresa;
			}
		}

	}

	return Rotina::unique_multidim_array($info,'id');

}


public function insertRotina($input){

	if(isset($input['rotina_nome']) and isset($input['rotina_desc']) and isset($input['setor'])){
		$company_id = "";
		$rotina_dia = "";

		if(empty($input['dia']) || $input['dia'] == 'diario'){
			$rotina_dia = 'diario';
		}else{
			$dias = explode(',', $input['dia']);

			foreach ($dias as $key => $dia) {

				if($dia >= 1 && $dia <= 31){

					$rotina_dia .= $dia.',';
				}
			}
		}

		if(empty($input['company_id'])){

			$company = $this->empresas;

			foreach ($company  as $key =>  $value) {

				$count = count($company);

				if($key+1 >= $count){
					$company_id .= $value['id'];
				}else{
					$company_id .= $value['id'].',';
				}
			}

		}else{
			foreach ($input['company_id']  as $key =>  $value) {

				$count = count($input['company_id']);

				if($key+1 >= $count){
					$company_id .= $value;
				}else{
					$company_id .= $value.',';
				}
			}
		}

		$array = array(
			"rotina_nome" => $input['rotina_nome'],
			"rotina_desc" => $input['rotina_desc'],
			"setor" => $input['setor'],
			"dia" => empty($rotina_dia)?$rotina_dia = 'diario':$rotina_dia,
			"company_id" => $company_id
		);

		$this->busca->Query('rotinas', $array, 'insert', '');
		return header("location: /rotinas/listar_rotinas");

	}

}

public function updateRotina($input){

	if(isset($input['rotina_nome']) and isset($input['rotina_desc']) and isset($input['setor']) and isset($input['id'])){

		$rotina_id 	= $input['id'];
		$company_id = "";
		$rotina_dia = "";

		if(empty($input['dia']) or $input['dia'] == 'diario'){
			$rotina_dia = 'diario';
		}else{
			$dias = explode(',', $input['dia']);

			foreach ($dias as $key => $dia) {

				if($dia >= 1 && $dia <= 31){

					$rotina_dia .= $dia.',';
				}
			}
		}

		if(empty($input['company_id'])){
			$company_id = 'geral';
		}else{
			foreach ($input['company_id']  as $key =>  $value) {

				$count = count($input['company_id']);

				if($key+1 >= $count){
					$company_id .= $value;
				}else{
					$company_id .= $value.',';
				}
			}
		}

		$array = array(
			"rotina_nome" => $input['rotina_nome'],
			"rotina_desc" => $input['rotina_desc'],
			"setor" => $input['setor'],
			"dia" => empty($rotina_dia)?$rotina_dia = 'diario':$rotina_dia,
			"company_id" => $company_id
		);

		$this->busca->Query('rotinas', $array, 'update', "id = '$rotina_id'");
		return header("location: /rotinas/listar_rotinas");

	}

}

public function removeRotina($id){
	$this->busca->Delete("rotinas",$id);
}



public function rotinasDoDIa(){

	$count = count($this->rotinas);

	for ($i=0; $i <$count ; $i++) {

		$dias = explode(',', $this->rotinas[$i]['dia']);
		$companys = explode(',', $this->rotinas[$i]['company_id']);

		if(in_array($this->hoje,$dias) || in_array('diario',$dias)){
			$rotinas[] = $this->rotinas[$i];
		}

	}

	return $rotinas;
}

public function realizaRotinasDoDIa($company_id){

	$count = count($this->rotinas);

	for ($i=0; $i <$count ; $i++) {

		$dias = explode(',', $this->rotinas[$i]['dia']);
		$companys = explode(',', $this->rotinas[$i]['company_id']);

		if(in_array($this->hoje,$dias) || in_array('diario',$dias)){
			if(in_array($company_id,$companys)){
			$rotinas[] = $this->rotinas[$i];
			}
		}

	}

	return $rotinas;
}

public function rotinasDoDIaExec($company_cnpj){

	$data_inicio = date('Y-m-d');

	$rotina_dados = $this->busca->select("rotina_realiza", "empresa_cnpj = '$company_cnpj' and data_inicio = '$data_inicio'");

	return $rotina_dados;
}


}