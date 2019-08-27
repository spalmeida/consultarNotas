<?php

namespace Utility;
use Connect\Connect;
use ZipArchive;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Utility extends Connect{


	function ZIP($source_path, $zipname, $dir){

    // Normaliza o caminho do diretório a ser compactado

		$source_path = realpath($source_path);



    // Caminho com nome completo do arquivo compactado

    // Nesse exemplo, será criado no mesmo diretório de onde está executando o script

		$zip_file = $dir.$zipname.'.zip';



    // Inicializa o objeto ZipArchive

		$zip = new ZipArchive;

		$zip->open($zip_file, ZipArchive::CREATE | ZipArchive::OVERWRITE);



    // Iterador de diretório recursivo

		$files = new RecursiveIteratorIterator(

			new RecursiveDirectoryIterator($source_path),

			RecursiveIteratorIterator::LEAVES_ONLY

		);



		foreach ($files as $name => $file) {

        // Pula os diretórios. O motivo é que serão inclusos automaticamente

			if (!$file->isDir()) {

            // Obtém o caminho normalizado da iteração corrente

				$file_path = $file->getRealPath();



            // Obtém o caminho relativo do mesmo.

				$relative_path = substr($file_path, strlen($source_path) + 1);



            // Adiciona-o ao objeto para compressão

				$zip->addFile($file_path, $relative_path);

			}

		}



    // Fecha o objeto. Necessário para gerar o arquivo zip final.

		$zip->close();



    // Retorna o caminho completo do arquivo gerado

		return $zip_file;

	}



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * @method Apagar_diretorio($src)

 *----------------------------------------------

 * @param $src recebe o endereço da pasta

 *

 *  TENHA CERTEZA DO DIRETÓRIO QUE ESTA ENVIADO PARA SER APAGADO!!!

 * 	Apaga todo um diretório arquivos e subpastas cuidado ao usar essa função!!!

 *

 **/

 //==============================================================================

 public function Apagar_diretorio($src){

 	$dir = opendir($src);

 	while(false !== ( $file = readdir($dir)) ) {

 		if (( $file != '.' ) && ( $file != '..' )) {

 			$full = $src . '/' . $file;

 			if ( is_dir($full) ) {

 				Utility::Apagar_diretorio($full);

 			}

 			else {

 				unlink($full);

 			}

 		}

 	}

 	closedir($dir);

 	rmdir($src);

 }



 public function Return_page($value){

 	echo '<script>';

 	echo 'window.location.assign("'.$value.'")';

 	echo '</script>';



 	return;

 }



 public function Log($string, $user_id, $user_name, $type){



 	$array = array(

 		"info"			=> $string,

 		"user_name"		=> $user_name,

 		"user_id"		=> $user_id,

 		"type"			=> $type,

 		"data" 			=> date("Y/m/d"),

 		"hora" 			=> date("H:i:s")



 	);



 	Utility::Query("logs", $array, "insert", "");



 }



 public function Hash_pass($pass){



 	$hash = hash('whirlpool', md5(sha1($pass)));



 	return $hash;

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * @method RemoveAcento($string)

 * @param  $string exemplo: "São Paulo é Sensacional"

 * @return sao_paulo_e_sensacional

 *----------------------------------------------

 *

 **/

 //==============================================================================

 function RemoveAcento($string){

 	$remove_separator = str_replace(" ", "_", $string);

 	$name = strtolower(preg_replace( '/[`^~\'"]/', null, iconv( 'UTF-8', 'ASCII//TRANSLIT', $remove_separator ) ));



 	return utf8_encode($name);

 }



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * @method Rand

 * @param $value informe um numero de 1 até ...

 *----------------------------------------------

 *

 * 	Retorna uma sequência aleatória de caracteres, basta apena informar o valor desejado.

 *

 **/

 //==============================================================================

 public function Rand($value){



 	$characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMOPQRSTUVXWYZ0123456789+_-';

 	$qtd_characters = strlen($characters);

 	$hash=NULL;

 	for($x=1;$x<=$value;$x++){

 		$position = rand(0,$qtd_characters);

 		$hash .= substr($characters,$position,1);

 	}



 	return $hash;

 }//fim do método Rand();



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * @method 	Encrypt

 * @param 	$input 	Informe o texto que será criptografado.

 * @param 	$key1 	Informe sua primeira senha ou deixe em branco.

 * @param 	$key2 	Informe sua segunda senha ou deixe em branco.

 * @example Encrypt("apenas um teste", "123", "321");

 *----------------------------------------------

 *

 * 	Esse método irá criar uma criptografia do seu texto

 *

 **/

 //==============================================================================

 public function Encrypt($input, $key1, $key2){



 	$first_key 	= $key1;

 	$second_key = $key2;



 	$method = "aes-256-cbc";

 	$iv_length = openssl_cipher_iv_length($method);

 	$iv = openssl_random_pseudo_bytes($iv_length);



 	$first_encrypted = openssl_encrypt($input,$method,$first_key, OPENSSL_RAW_DATA ,$iv);

 	$second_encrypted = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);



 	$output = base64_encode($iv.$second_encrypted.$first_encrypted);

 	return $output;

 }//fim do método Encrypt();



 //==============================================================================

 /**

 *

 *----------------------------------------------

 * @method 	Decrypt

 * @param 	$output 	coloque aqui a criptografia gerada no Método Encrypt().

 * @param 	$key1 	coloque a primeira senha informada no Método Encrypt().

 * @param 	$key2 	coloque a segunda senha informada no Método Encrypt().

 *----------------------------------------------

 *

 * 	Esse método irá criar uma criptografia do seu texto

 *

 **/

 //==============================================================================

 public function Decrypt($output, $key1, $key2){



 	$first_key 	= $key1;

 	$second_key = $key2;

 	$mix = base64_decode($output);



 	$method = "aes-256-cbc";

 	$iv_length = openssl_cipher_iv_length($method);



 	$iv = substr($mix,0,$iv_length);

 	$second_encrypted = substr($mix,$iv_length,64);

 	$first_encrypted = substr($mix,$iv_length+64);



 	$data = openssl_decrypt($first_encrypted,$method,$first_key,OPENSSL_RAW_DATA,$iv);

 	$second_encrypted_new = hash_hmac('sha3-512', $first_encrypted, $second_key, TRUE);



 	if (hash_equals($second_encrypted,$second_encrypted_new))

 		return $data;



 	return false;

 }//fim do método Decrypt();



 public function tributaria(){

 	setlocale(LC_ALL, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
 	date_default_timezone_set('America/Sao_Paulo');

 	$mes = strftime('%B', strtotime('today'));
 	$mesDaConsulta = $mes;

 	$url = file_get_contents('http://receita.economia.gov.br/acesso-rapido/agenda-tributaria/agenda-tributaria-2019/agenda-tributaria-agosto-2019/declaracoes-demonstrativos-e-documentos');

 	preg_match_all('/<td(.+)\/td>/', $url, $conteudo);

 	$listagem = $conteudo[0];
 	$array = array_chunk($listagem, 3);

 	return $array;

 }


}











