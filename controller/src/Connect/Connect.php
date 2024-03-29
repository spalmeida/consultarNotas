<?php


namespace Connect;
use Connect\Connection;
use PDO;




class Connect extends Connection{


 public	function Data($select,$type){



 	if($type == 'fetch'){

 		$select = $this->conn->prepare($select);

 		$select->execute();

 		return $select->fetch();



 	}else if($type == 'fetchAll'){

 		$select = $this->conn->prepare($select);

 		$select->execute();

 		return $select->fetchAll(PDO::FETCH_ASSOC);



 	}else if($type == 'insert'){

 		$select = $this->conn->prepare($select);

 		return $select->execute();



 	}else if($type == 'update'){

 		$select = $this->conn->prepare($select);

 		return $select->execute();

 	}

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * select

 *----------------------------------------------

 *

 * 	Responsável por trazer um resultado onde o usuário pode enviar uma condição

 * para a query em questão.

 *

 **/

 //==============================================================================

 public function select($table, $where){

 	$select = "SELECT * FROM $table WHERE $where";

 	return Connect::Data($select,"fetchAll");

 }

  public function defaultSelect($value){

 	return Connect::Data($value,"fetchAll");

 }





 public function selectFrom($column, $table, $where){

 	$select = "SELECT $column FROM $table WHERE $where";

 	return Connect::Data($select,"fetchAll");

 }



 public function SelectByDate($table, $column, $start, $end, $condition){

 	$select = "SELECT * FROM $table WHERE $column BETWEEN DATE('$start') AND DATE('$end') AND $condition";

 	return Connect::Data($select,"fetchAll");

 }



 public function select_fetch($table, $where){

 	$select = "SELECT * FROM $table WHERE $where ";

 	return Connect::Data($select,"fetch");

 }





 //==============================================================================

 /**

 *

 *----------------------------------------------

 * selectDefault

 *----------------------------------------------

 *

 * 	Diferente dos outros métodos esse é responsável por trazer quase que a query inteira,

 * funciona em alguns casos onde por exemplo é necessário dar uma informação muito especi

 * fica para a query.

 *

 **/

 //==============================================================================

 public function selectDefault($table,$column, $where){

 	$select = "SELECT $column FROM $table WHERE $where ";

 	return Connect::Data($select,"fetchAll");

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * FetchAll

 *----------------------------------------------

 *

 * 	Retorna todos os dados de uma tabela.

 *

 **/

 //==============================================================================

 public function FetchAll($value){



 	$select = "SELECT * FROM $value";

 	return Connect::Data($select,"fetchAll");

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * FetchWhere

 *----------------------------------------------

 *

 * 	Retorna todos os dados que estão dentro de uma condição.

 *

 **/

 //==============================================================================

 public function FetchWhere($value, $where){



 	$select = "SELECT * FROM $value WHERE $where";

 	return Connect::Data($select,"fetchAll");

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * FetchDefault

 *----------------------------------------------

 *

 * 	É POSSÍVEL COLOCAR UMA QUERY INTEIRA SEM DINÂMICA "não recomendado".

 *

 **/

 //==============================================================================

 public function FetchDefault($value){



 	$select = $value;

 	return Connect::Data($select,"insert");

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * magicFetch

 *----------------------------------------------

 *

 * 	Retorna todos resultados seguindo uma ordem ASC com base na data das colunas.

 *

 **/

 //==============================================================================

 public function magicFetch($value){



 	$select = "SELECT * FROM $value order by str_to_date(date, '%d/%m/%Y') ASC";

 	return Connect::Data($select,"fetchAll");

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * MagicSelect

 *----------------------------------------------

 *

 * 	Método de auxilio para o método Tables aqui que ele retorna o nome das colunas

 *

 **/

 //==============================================================================

 public function MagicSelect($table){



 	$select = "DESCRIBE $table";

 	return Connect::Data($select,"fetchAll");



 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * Tables

 *----------------------------------------------

 *

 * 	Faz uma consulta com base no valor recebido e retorna todas as colunas do banco

 *

 **/

 //==============================================================================

 public function Tables($table){



 	$search = Connect::MagicSelect($table);

 	$count  = count($search);



 	for ($i=0; $i < $count ; $i++) {



 		if($search[$i]['Field'] !== "id"){

 			$result[] = $search[$i]['Field'];

 		}

 	}



 	return $result;

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * @method Query

 *----------------------------------------------

 *

 * 	Esse de fato é um do métodos mais importantes de toda a classe é aqui que a

 * "mágica" acontece, muito cuidado caso queira alterar esse método pois pode

 * comprometer toda a classe.

 *

 *	ENTENDA AS VÁRIAVEIS

 *

 * @param $table 		= RECEBE O NOME DA TABELA

 * @param $array 		= RECEBE UM ARRAY COM OS DADOS A SEREM INSERIDOS

 * @param $type 		= TIPO DE EXECUÇÃO NO BANCO DE DADOS

 * @param $where 		= CONDIÇÃO CASO PRECISE DE UMA

 * @var   $tableInfo 	= TRAZ AS INFORMAÇÕES DA COLUNA NO MÉTODO Tables()

 * @var   $count 		= VERIFICA A QUANTIDADE DE ITENS DENTRO DO ARRAY $tableInfo

 *

 * O ARRAY PRECISA VIR COM O NOME DA COLUNA

 *

 * EXEMPLO:

 *

 * DB: TABELA = usuarios varchar user_name, user_email

 * array{

 * user_name => usuarioteste,

 * user_email => usuarioteste@teste.com

 * }

 *

 *	Esse método percorre e cria a query automaticamente com bindParam, sem a necessidade

 * de estar sempre digitando uma query inteira, com este você digita apenas uma linha de

 * código e ele reconhece e traz os dados, a forma de usar também é bem simples segue

 * alguns exemplos de uso:

 *

 * EXEMPLOS =========================== * * *

 *

 *	Para fazer um INSERT use:

 *

 * $search->Query("NOME_DA_TABELA",$ARRAY,"insert", "");

 *

 *	Para fazer um UPDATE use:

 *

 * $search->Query("NOME_DA_TABELA",$ARRAY,"update", "CONDIÇÃO");

 *

 **/

 //==============================================================================

 public function Query($table, $array, $type, $where){



 	$tableInfo 	= Connect::Tables($table);

 	$fieldA 	= "";

 	$fieldB 	= "";

 	$field  	= "";

 	$count 		= count($tableInfo);



 //CASO SEJA insert =========================== * * *

 	if($type == "insert"){



 		foreach ($tableInfo as $index => $value) {



 			if($index+1 < $count){



 				$fieldA .= $value . ", ";

 				$fieldB .= ":".strtoupper($value).", ";

 				$fieldAarray[] = $value;

 				$fieldBarray[] = ":".strtoupper($value);



 			}else{



 				$fieldA .= $value;

 				$fieldB .= ":".strtoupper($value);

 				$fieldAarray[] = $value;

 				$fieldBarray[] = ":".strtoupper($value);



 			}

 		}



 		$stmt = $this->conn->prepare("INSERT INTO $table ($fieldA) VALUES ($fieldB) $where");



 		for ($i=0; $i < count($fieldAarray) ; $i++) {



 			$stmt->bindParam($fieldBarray[$i], $array[$fieldAarray[$i]]);



 		}



 		$stmt->execute();



 //CASO SEJA update =========================== * * *

 	}else if($type == "update"){



 		foreach ($tableInfo as $index => $value) {



 			if($index+1 < $count){



 				$field .= $value . " = :".strtoupper($value).", ";

 				$fieldAarray[] = ":".strtoupper($value);

 				$fieldBarray[] = $value;



 			}else{



 				$field .= $value . " = :".strtoupper($value);

 				$fieldAarray[] = ":".strtoupper($value);

 				$fieldBarray[] = $value;

 			}

 		}



 		$stmt = $this->conn->prepare("UPDATE $table SET $field WHERE $where");



 		for ($i=0; $i < count($fieldAarray) ; $i++) {



 			$stmt->bindParam($fieldAarray[$i], $array[$fieldBarray[$i]]);

 		}



 		return $stmt->execute();





 	}



 	return;



 }



 public function Query_fix($type, $column, $table, $value, $id){



 	if($type == "update"){



 		$stmt = $this->conn->prepare("UPDATE $table SET $column = $value WHERE id = :ID");

 		$stmt->bindParam(":ID", $id);

 		$stmt->execute();

 		return;

 	}



 	



 }

 //==============================================================================

 /**

 *

 *----------------------------------------------

 * Delete

 *----------------------------------------------

 *

 * 	Esse método deleta uma coluna especifica do banco de dados

 *

 **/

 //==============================================================================

 public function Delete($table ,$id){



 	$stmt = $this->conn->prepare("DELETE FROM $table WHERE id = :ID");

 	$stmt->bindParam(":ID", $id);

 	$stmt->execute();



 	return;

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * FileUpload

 *----------------------------------------------

 *

 * 	Responsável por fazer o upload de arquivos para o sistema algumas de suas

 * informações precisam ser informadas dentro do método de acordo com cada sistema

 * e suas regras de uso e permissão de trabalho.

 *

 * $array 	= RECEBE UM ARRAY DO ARQUIVO

 * $format 	= TIPO DE ARQUIVO (imagem, video, zip...)

 * $type 	= RECEBE O NOME DO DIRETÓRIO PARA ONDE SERÁ ENVIADO O ARQUIVO

 * $sub 	= USADO PARA REFERENCIA DE SUBPASTA PARA ONDE O ARQUIVO SERÁ ENVIADO

 *

 **/

 //==============================================================================

 public function FileUpload($array, $format, $type, $sub){



 	$new_type_1[] = explode(".", $array['name']);

 	$new_type[] = end($new_type_1[0]);

 	$file_type = $new_type[0];



//FORMATOS PERMITIDOS PARA UPLOADS DE IMAGEM == * * *

 	if($format == "imagem"){

 		$format_type = array('jpg', 'png', 'jpeg');

 	}elseif($format == "task"){

 		$format_type = array('zip','pdf','gif','docx','pptx','xlsx','xls','jpg','txt','png','rar');

 	}elseif($format == "xml"){

 		$format_type = array('xml');

 	}



//GRAVA O ARRAY "$array" DENTRO DA VÁRIAVEL "$archive" == * * *

 	foreach ($array as $value){

 		$archive[] = $value;

 	}



//FAZ A VERIFICAÇÃO DA EXTENSÃO DO ARQUIVO == * * *

 	if(!empty($file_type)){

 		if (array_search($file_type, $format_type) === false){

 			echo 'O tipo de arquivo enviado é inválido!';



 		}else{



//VERIFICA SE HOUVE ALGUM ERRO NO ENVIO DO ARQUIVO == * * *

 			if($array["error"]){

 				echo " Ops ocorreu um erro :( ";

 			}

 		}



//DIRETÓRIO PARA ONDE IRÁ O ARQUIVO == * * *

 		$dirUploads = "..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."UPLOADS/";

 		$dirType = "..".DIRECTORY_SEPARATOR."..".DIRECTORY_SEPARATOR."UPLOADS".DIRECTORY_SEPARATOR.$type. DIRECTORY_SEPARATOR.$sub;



//VERIFICA SE O DIRETÓRIO EXISTE, CASO NÃO EXISTA ELE CRIA == * * *

 		if(!is_dir($dirUploads)){

 			mkdir($dirUploads, 0777, true);

 		}if(!is_dir($dirType)){

 			mkdir($dirType, 0777, true);

 		}



 		foreach ($archive as $value) { /* INICIO FOREACH */



//$extension FICA APENAS O VALOR FINAL DA EXTENSÃO == * * *

 			$extension = ".".$file_type;



//CRIA UM NOME PARA O ARQUIVO USANDO O MÉTODO "Rand()" == * * *

 			$rand = Connect::Rand(5);



//DETERMINA O NOME DO ARQUIVO E PARA ONDE SERÁ ENVIADO == * * *

 			$file_name = $type."_".$rand.$extension;

 			$path = $dirType . DIRECTORY_SEPARATOR . $file_name;



//VERIFICA SE EXISTE ALGUM ARQUIVO COM O MESMO NOME	== * * *

 			if(file_exists($path)) {

 				$path = $dirType . DIRECTORY_SEPARATOR . $file_name;

 			}else{

 				$path = $dirType . DIRECTORY_SEPARATOR . $file_name;

 			}



 			/* INICIO DO IF "move_uploaded_file" */



//ENVIA O ARQUIVO PARA O LOCAL INFORMADO == * * *

 			if(move_uploaded_file($array["tmp_name"], $path)){



//ENVIA O ARQUIVO PARA O BANCO COM SUAS INFORMAÇÕES == * * *

 				$file_array = array(



 					'file_name' => $file_name,

 					'file_type' => $format,

 					'file_dir' => $type,

 					'file_date' => Connect::DateInfo("1")

 				);



 				return array(

 					'file_type' => $type,

 					'file_name' => $array['name'],

 					'file_directory' => $path,

 					'cripto_name' => $file_name

 				);

 			}



 			/* FIM DO IF "move_uploaded_file" */



 		}/* FIM "FOREACH" */

 	}

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * Validationtable

 *----------------------------------------------

 *

 * 	Verifica se existe o campo consultado e retorna tru ou false

 *

 **/

 //==============================================================================

 public function Validationtable($tab, $column, $info){

 	$select = Connect::select($tab,"$column LIKE '%$info%'");

 	if(empty($select)){

 		return false;

 	}else{

 		return true;

 	}

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * Validation

 *----------------------------------------------

 *

 * 	Método usando para login, verifica se o email e a senha do usuário em questão

 * estão de acordo com os dados cadastrados dentro do sistema.

 *

 **/

 //==============================================================================

 public function Validation($table, $email, $pass){



 	$select = Connect::select($table,"user_mail = '$email' and user_pass = '$pass' ");

 	$count  = count($select);



 	if(isset($_SESSION)){



 		if($count == 0){

 			return false;

 		}else{



 			return true;

 		}

 		return true;



 	}





 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * VerifyUser

 *----------------------------------------------

 *

 * 	Verifica se o existe uma Sessão, caso contrário o usuário e redirecionado

 *

 **/

 //==============================================================================

 public function VerifyUser(){



 	if(empty($_SESSION['email'])){



 		$dados = '<script type="text/javascript">

 		window.location.replace("'.$site_end.'");

 		</script>';



 	}else{

 		unset($_SESSION['start']);

 		session_destroy();

 		header("location: login.php ");

 	}



 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * DateInfo

 *----------------------------------------------

 *

 * 	Retorna dados de datas formatados a sua escolha basta apenas adicionar novos

 * formatos dentro de uma condição numérica pela váriavel $value.

 *

 **/

 //==============================================================================

 public function DateInfo($value){



 	$date = new DateTime();



 		$day 			= $date->format("d"); // DIA

 		$month 			= $date->format("m"); //MÊS

 		$year 			= $date->format("y"); //APENAS OS DOIS ÚLTIMOS DIGÍTOS DO ANO

 		$fullYear 		= $date->format("Y");; //ANO COMPLETO

 		$semana 		= date('w', time()); //NÚMERO DA SEMANA

 		$mes 			= date('m', time())-1; //NÚMERO DOS MESES



 		$dias_da_semana = Array(

 			"Domingo",

 			"Segunda Feira",

 			"Terça Feira",

 			"Quarta Feira",

 			"Quinta Feira",

 			"Sexta Feira",

 			"Sábado");



 		$meses_do_ano 	= Array(

 			"Janeiro",

 			"Fevereiro",

 			"Março",

 			"Abril",

 			"Maio",

 			"Junho",

 			"Julho",

 			"Agosto",

 			"Setembro",

 			"Outubro",

 			"Novembro",

 			"Dezembro");



 		$s 				= " - "; //SEPARADOR



 		if($value == "1"){



 //Exemplo: 02 - 11 - 2018 === * * *

 			$dateinfo = $day.$s.$month.$s.$fullYear;



 		}elseif($value == "2"){



 //Exemplo: Sexta dia 02 de Dezembro de 2018 === * * *

 			$dateinfo = $dias_da_semana[$semana]." dia ".$day." de ".$meses_do_ano[$mes]." de ".$fullYear;



 		}elseif($value == "3"){



 //Exemplo: Sexta === * * *

 			$dateinfo = $dias_da_semana[$semana];



 		}elseif($value == "4"){



 //Exemplo: 02 === * * *

 			$dateinfo = $day;



 		}elseif($value == "5"){



 //Exemplo: 12 === * * *

 			$dateinfo = $month;

 		}



 		return $dateinfo;

 	}



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * ConvertDate

 *----------------------------------------------

 *

 * 	Muda o separador de uma data

 *

 **/

 //==============================================================================

 public function ConvertDate($date){



 	$convert = str_replace("/", "-", $date);

 	return date('d-m-Y', strtotime($convert));



 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * VerifyLogin

 *----------------------------------------------

 *

 *	Caso exista uma sessão ele é emcaminhado para uma página especifica

 *

 **/

 //==============================================================================

 public function VerifyLogin(){

 	if($_SESSION){

 		header("Location: index.php");

 	}

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * VerifySession

 *----------------------------------------------

 *

 *	Caso não exista uma sessão ele é emcaminhado para uma página especifica

 *

 **/

 //==============================================================================

 public function VerifySession(){

 	if(!$_SESSION){

 		header("Location: login.php");

 	}

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * Verifytype

 *----------------------------------------------

 *

 *	Verifica qual é o tipo de usuário que está acessando o sistema

 *

 **/

 //==============================================================================

 public function Verifytype($id){

 	$status = Connect::select("system_users", "id = $id");

 	if($status[0]['user_type'] == 1){

 	}elseif($status[0]['user_type'] == 2){

 		echo '<script>';

 		echo 'window.location.assign("index.php")';

 		echo '</script>';

 	}



 	return;

 }



 public function dir_creator($dir){



 	if(!is_dir($dir)){

 		mkdir($dir, 0777, true);

 	}



 }



 public function dir_rename($old, $new){



 	if(file_exists($old)){

 		rename($old, $new);

 	}

 

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * Verifystatus

 *----------------------------------------------

 *

 *	Verifica qual é o status do usuário que está acessando o sistema

 *

 **/

 //==============================================================================

 public function Verifystatus($id){



 	$status = Connect::select("system_users", "id = $id");

 	if($status[0]['user_status'] == 1){



 	}elseif($status[0]['user_status'] == 2){



 		session_destroy();

 		echo '<script>';

 		echo 'window.location.assign("login.php?info=aguardando")';

 		echo '</script>';

 		return;



 	}elseif($status[0]['user_status'] == 3){



 		session_destroy();

 		echo '<script>';

 		echo 'window.location.assign("login.php?info=bloqueado")';

 		echo '</script>';



 	}elseif($status[0]['user_status'] == 4){



 		session_destroy();

 		echo '<script>';

 		echo 'window.location.assign("login.php?info=desativado")';

 		echo '</script>';



 	}



 	return;

 }



}//FIM DA CLASSE CONNECT








?>