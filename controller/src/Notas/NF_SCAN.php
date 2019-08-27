<?php

namespace Notas;
use Connect\Connect;
use Notas\RetornXmlArray as Save;

class NF_SCAN extends Connect{

//INICIA A BUSCA E INSERÇÃO DOS DADOS ====================
	public function save_db($dir_xml){
		self::exec($dir_xml);
	}
//========================================================

//PERCORRE O DIRETÓRIO E RETORNA TODOS OS ARQUIVOS ENCONTRADOS
	private function exec($dir_xml){

		$dir = opendir($dir_xml);
		$info = array();

		while(false !== ( $file = readdir($dir)) ) {

			if (( $file != '.' ) && ( $file != '..' )) {

				$full = $dir_xml . $file;

				if ( is_dir($full) ) {

					self::exec($full);

				}else{

					$ext = ltrim( substr( $full, strrpos( $full, '.' ) ), '.' );

					if($ext == "xml" or $ext == "XML"){

						$teste = new Save();
						$info[] = $teste->exec($full);

					}
				}
			}
		}

		self::insert($info);

	}
//========================================================

//VERIFICA QUAL A ORIGEM DOS DOCUMENTOS RETORNADOS =======
	private function insert($full){

		$count 	= count($full);
		$info 	= array();

		for ($i=0; $i <$count ; $i++) {

			if(!empty($full[$i]['documento'])){

//O DOCUMENTO SE TRATA DE UMA NFE
				if($full[$i]['documento'] == 'NFE'){
					$info = self::insert_NFE($full[$i]);

//O DOCUMENTO SE TRATA DE UMA CTE
				}else if($full[$i]['documento'] == 'CTE'){
					$info = self::insert_CTE($full[$i]);

//O DOCUMENTO SE TRATA DE UM EVENTO
				}else{
					$info = self::insert_EVENTO($full[$i]);
				}
			}
		}
		return $info;
	}
//========================================================

//RETORNA UM ARRAY PADRONIZADO PARA NFE ==================
	private function gerarArray_NFE($type, $file_xml){
		$array = array(

			"numero_nf" 		=> $file_xml['numero_nf'],
			"chave_nf"			=> $file_xml['chave'],
			"fornecedor_nome"	=> $file_xml['emitente_nome'],
			"fornecedor_cnpj"	=> $file_xml['emitente_CnpjOuCpf'],
			"empresa_nome"		=> $file_xml['destinatario_nome'],
			"empresa_cnpj"		=> $file_xml['destinatario_cnpj'],
			"data_emissao"		=> $file_xml['data_emissao'],
			"data_vencimento"	=> $file_xml['data_vencimento'],
			"valor"				=> $file_xml['valor'],
			"dir_xml"			=> "",
			"type"				=> $type
		);

		return $array;
	}
//========================================================

//RETORNA UM ARRAY PADRONIZADO PARA EVENTO ===============
	private function gerarArray_EVENTO($file_xml){

		$array = array(

			"chave_nf" 		=> $file_xml['NFe'],
			"xevento"		=> $file_xml['documento'],
			"motivo"		=> $file_xml['observacao'],
			"empresa_cnpj"	=> $file_xml['cnpj']
		);

		return $array;
	}
//========================================================

//RETORNA UM ARRAY PADRONIZADO PARA CTE ==================
	private function gerarArray_CTE($file_xml){

		$array = array(

			"chave_cte" 		=> $file_xml['chave'],
			"numero_cte"		=> $file_xml['numero_cte'],
			"empresa_cnpj"		=> $file_xml['destinatario_cnpj'],
			"empresa_nome"		=> $file_xml['destinatario_nome'],
			"emitente_cnpj"		=> $file_xml['emitente_CnpjOuCpf'],
			"emitente_nome"		=> $file_xml['emitente_nome'],
			"cfop"				=> $file_xml['cfop'],
			"valor"				=> $file_xml['valor'],
			"data_emissao"		=> $file_xml['data_emissao'],
			"dir_xml"			=> ""
		);

		return $array;
	}
//========================================================

//CRIA UMA CÓPIA PARA O DIRETÓRIO ESPECIFICADO
	private function copy_files($old_dir, $new_dir){
		if(!is_file($new_dir)){
			copy($old_dir, $new_dir);
		}
	}
//========================================================

//RETORNA O NOME DO MÊS EM pt_BR
	private function gerar_mes($data){

		$value = date('m', strtotime($data));

		if($value == 1){
			$data = "JANEIRO";
		}elseif($value == 2){
			$data = "FEVEREIRO";
		}elseif($value == 3){
			$data = "MARCO";
		}elseif($value == 4){
			$data = "ABRIL";
		}elseif($value == 5){
			$data = "MAIO";
		}elseif($value == 6){
			$data = "JUNHO";
		}elseif($value == 7){
			$data = "JULHO";
		}elseif($value == 8){
			$data = "AGOSTO";
		}elseif($value == 9){
			$data = "SETEMBRO";
		}elseif($value == 10){
			$data = "OUTUBRO";
		}elseif($value == 11){
			$data = "NOVEMBRO";
		}elseif($value == 12){
			$data = "DEZEMBRO";
		}

		return $data;

	}
//========================================================

//NFE ====================================================
	private function insert_NFE($file_xml){
		$info = array();
		$busca = new Connect();
		$empresa_cnpj = $file_xml['emitente_CnpjOuCpf'];

//verifica se o emitente está cadastrado no sistema
		if(!empty($busca->select("system_companys", "company_cnpj = $empresa_cnpj"))){

//retorna um array com os dados de saida
			$array_saida = self::gerarArray_NFE("saida", $file_xml);

//retorna um array com os dados de entrada
			$array_entrada = self::gerarArray_NFE("entrada", $file_xml);
		}else{
			$array_entrada = self::gerarArray_NFE("entrada", $file_xml);
		}

//verifica se existe dados para notas de saida
		if(isset($array_saida)){
			$chave_nf 	= $array_saida['chave_nf'];
			$numero_nf 	= $array_saida['numero_nf'];
			$raiz 		= $file_xml['file_dir'];

			$ano 	= date('Y', strtotime( $array_entrada['data_emissao'] ));
			$cnpj 	= $array_entrada['empresa_cnpj'];
			$mes 	= self::gerar_mes($array_entrada['data_emissao']);

			$file = "Notas_Saida/$cnpj/$ano/$mes/";
			if(!file_exists($file)){
				mkdir($file, 0777, true);
			}

			$file_dir = $file.$chave_nf.'-nfe.xml';

			self::copy_files($raiz, $file_dir);

			$array_saida['dir_xml'] = $file_dir;

//verifica se já existe uma nota de saida com esses dados no sistema
			if(empty($busca->select("nf", "chave_nf = '$chave_nf' and numero_nf = '$numero_nf' and type = 'saida' "))){
//echo $file_dir para debug <<;
				$busca->Query("nf", $array_saida, "insert", "");

			}
		}
//verifica se existe dados para notas de entrada
		if(isset($array_entrada)){

			$chave_nf 	= $array_entrada['chave_nf'];
			$numero_nf 	= $array_entrada['numero_nf'];
			$raiz 		= $file_xml['file_dir'];

			$ano 	= date('Y', strtotime( $array_entrada['data_emissao'] ));
			$cnpj 	= $array_entrada['empresa_cnpj'];
			$mes 	= self::gerar_mes($array_entrada['data_emissao']);

			$file = "Notas_Entrada/$cnpj/$ano/$mes/";
			if(!file_exists($file)){
				mkdir($file, 0777, true);
			}

			$file_dir = $file.$chave_nf.'-nfe.xml';

			self::copy_files($raiz, $file_dir);

			$array_entrada['dir_xml'] = $file_dir;

//verifica se já existe uma nota de entrada com esses dados no sistema
			if(empty($busca->select("nf", "chave_nf = '$chave_nf' and numero_nf = '$numero_nf' and type = 'entrada' "))){

				$busca->Query("nf", $array_entrada, "insert", "");

			}

		}
		unlink($raiz);
	}
//========================================================

//CTE ====================================================
	private function insert_CTE($file_xml){

		$busca 		= new Connect();
		$chave_cte 	= $file_xml['chave'];
		$numero_cte = $file_xml['numero_cte'];
		$array_cte 	= self::gerarArray_CTE($file_xml);
		$raiz 		= $file_xml['file_dir'];

		$ano 	= date('Y', strtotime( $array_cte['data_emissao'] ));
		$cnpj 	= $array_cte['empresa_cnpj'];
		$mes 	= self::gerar_mes($array_cte['data_emissao']);

		$file = "CTEs/$cnpj/$ano/$mes/";
		if(!file_exists($file)){
			mkdir($file, 0777, true);
		}

		$file_dir = $file.$chave_cte.'-cte.xml';

		self::copy_files($raiz, $file_dir);

		$array_cte['dir_xml'] = $file_dir;

		if(empty($busca->select("cte", "chave_cte = '$chave_cte' and numero_cte = '$numero_cte' "))){

			$busca->Query("cte", $array_cte, "insert", "");
		}
		unlink($raiz);
	}
//========================================================

//EVENTO =================================================
	private function insert_EVENTO($file_xml){
		$busca 		= new Connect();
		$chave_nf 	= $file_xml['NFe'];
		$raiz 		= $file_xml['file_dir'];

		$array_evento = self::gerarArray_EVENTO($file_xml);

		if(empty($busca->select("nf_event", "chave_nf = '$chave_nf' "))){

			$busca->Query("nf_event", $array_evento, "insert", "");

		}
		unlink($raiz);
	}
//========================================================

}