<?php


namespace Connect;
use PDO;

class Connection{

	public $conn;



 //==============================================================================

 /**

 * @package	INTERMEZIUM

 * @author	Samuel Prado Almeida

 * @link	https://intermezium.com.br

 * @since	Version 1.0.0

 *

 *----------------------------------------------

 * Construct

 *----------------------------------------------

 *

 * 	Function construct é um método mágico no qual vai puxar os dados automaticamente,

 * sem a necessidade de ficar instaciando um objeto para cada vez que for utilizada essa classe.

 *

 * 	Foi criada constantes para chamar os dados do banco de dados para facilitar a inclusão deles.

 *

 * 	Caso precise adicione todas as váriaveis que serão utilizadas mais de uma vez como "public

 * NOME_DA_VARIAVEL", e para as constantes "que não iram mudar o seu valor", utilize "const

 * NOME_DA_CONSTANTE", ambas dentro do quadro acima para organização e melhor visualização do código.

 *

 //=======================================================================

 //=======================================================================

 *----------------------------------------------

 * INFORMAÇÕES DO BANCO DE DADOS

 *----------------------------------------------

 *	Logo abaixo estão as váriaveis responsáveis pela conexão com banco de dados, informe seus dados.

 *

 **/

 //==============================================================================

 const  HOST 		= "localhost";			//Endereço do seu servidor.

 const  DBNAME 		= "intermez_system";	//Nome do seu banco de dados

 const  USERNAME 	= "intermez_system";	//Nome do usuáriodo banco de dados

 const  PASS 		= "Siglanero23";		//Senha do seu banco de dados

 const 	ID 			= "id"; 				//Nome da indentificação da coluna padrao

 //==============================================================================



 function __construct(){

 	try{

 		$this->conn = new PDO(

 			'mysql:host='.self::HOST.';

 			dbname='.self::DBNAME.';

 			charset=utf8',

 			self::USERNAME,

 			self::PASS,



 			array(

 				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

 				PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'

 			));



 	}catch(PDOException $e){

 		echo 'ERROR: ' . $e->getMessage();



 	}

 }



}



?>