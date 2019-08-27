<?php



namespace Notas;



/**
 *
 * RETORNA UM ARRAY COM OS DADOS DO XML
 *
 * @param $dir = CHAVE_DA_NOTA.xml
 *
 **/



class RetornXmlArray{



	public function exec($dir_xml, $json = "")

	{



		$file_get = file_get_contents($dir_xml);



		$xml   = simplexml_load_string($file_get, 'SimpleXMLElement', LIBXML_NOCDATA);



		$arr = json_decode(json_encode((array)$xml), TRUE);



		$info = RetornXmlArray::verify_type($arr, $dir_xml);



		$retorno = $json ? json_encode($info) : $info;





		return $retorno;

	}





	public function verify_type($arr, $dir_xml)

	{

		if (isset($arr['NFe'])) {



			$NFe = $arr['NFe']['infNFe'];



			$chave 				= str_replace('NFe','',$NFe['@attributes']['Id']);

			$numero_nf 			= $NFe['ide']['nNF'];



			$data_emissao 		= date('Y-m-d', strtotime($NFe['ide']['dhEmi']));

			$data_vencimento 	= isset($NFe['cobr']['dup']['dVenc'])?$NFe['cobr']['dup']['dVenc']:"";





			$valor 				= $NFe['total']['ICMSTot']['vNF'];



			$emitente 			= $NFe['emit'];

			$emitente_nome 		= $emitente['xNome'];

			$emitente_CnpjOuCpf = isset($emitente['CNPJ'])?$emitente['CNPJ']:$emitente['CPF'];





			$destinatario 		= $NFe['dest'];

			$destinatario_nome 	= isset($destinatario['xNome'])?$destinatario['xNome']:'NÃO CONSTA';

			if(isset($destinatario['CNPJ'])){
				$destinatario_cnpj 	= $destinatario['CNPJ'];

			}else{
				$destinatario_cnpj 	= $destinatario['CPF'];
			}


			$info = array(

				"documento" 			=> "NFE",
				"chave" 				=> $chave,
				"numero_nf" 			=> $numero_nf,
				"data_emissao" 			=> $data_emissao,
				"data_vencimento" 		=> $data_vencimento,
				"valor" 				=> $valor,
				"emitente_nome" 		=> $emitente_nome,
				"emitente_CnpjOuCpf" 	=> $emitente_CnpjOuCpf,
				"destinatario_nome" 	=> $destinatario_nome,
				"destinatario_cnpj" 	=> $destinatario_cnpj,
				"file_dir"				=> $dir_xml

			);



		}else if(isset($arr['CTe'])){

			$CTe = $arr['CTe']['infCte'];



			$chave 				= str_replace('CTe','',$CTe['@attributes']['Id']);

			$numero_cte			= $CTe['ide']['nCT'];



			$data_emissao 		= date('Y-m-d', strtotime($CTe['ide']['dhEmi']));



			$cfop = $CTe['ide']['CFOP'];





			$valor 				= $CTe['vPrest']['vTPrest'];


			$emitente 			= $CTe['emit'];

			$emitente_nome 		= $emitente['xNome'];

			$emitente_CnpjOuCpf = isset($emitente['CNPJ'])?$emitente['CNPJ']:$emitente['CPF'];





			$destinatario 		= $CTe['dest'];

			$destinatario_nome 	= $destinatario['xNome'];
			if(isset($destinatario['CNPJ'])){
				$destinatario_cnpj 	= $destinatario['CNPJ'];
			}else{
				$destinatario_cnpj 	= $destinatario['CPF'];
			}

			$info = array(

				"documento" 			=> "CTE",
				"chave" 				=> $chave,
				"numero_cte" 			=> $numero_cte,
				"data_emissao" 			=> $data_emissao,
				"cfop" 					=> $cfop,
				"valor" 				=> $valor,
				"emitente_nome" 		=> $emitente_nome,
				"emitente_CnpjOuCpf" 	=> $emitente_CnpjOuCpf,
				"destinatario_nome" 	=> $destinatario_nome,
				"destinatario_cnpj" 	=> $destinatario_cnpj,
				"file_dir"				=> $dir_xml
			);

		}else if(empty($arr['evento'])){

			$info = array(

				"error" 				=> $dir_xml

			);

			unlink($dir_xml);

		}else if($arr['evento']){



			$evento 			= $arr['evento']['infEvento'];

			$numero_evento		= str_replace('ID','',$evento['@attributes']['Id']);

			$cnpj = $evento['CNPJ'];

			$NFe = $evento['chNFe'];



			if(empty($evento['detEvento']['xJust'])){

				$evento['detEvento']['xJust'] = "";

			}



			$descricao 	= $evento['detEvento']['descEvento'];

			$observacao = isset($evento['detEvento']['xCorrecao'])?

			$evento['detEvento']['xCorrecao']:

			$evento['detEvento']['xJust'];



			if($descricao == "Confirmacao da Operacao"){

				unlink($dir_xml);

			}



			$info = array(

				"documento" 			=> $descricao,
				"NFe" 					=> $NFe,
				"numero_evento" 		=> $numero_evento,
				"cnpj" 					=> $cnpj,
				"observacao" 			=> $observacao,
				"file_dir"				=> $dir_xml
			);

		}else{

			$info = array(

				"error" 				=> $dir_xml

			);

		}



		return $info;



	}



	public function teste($dir){



		$info = file_get_contents($dir);



		$xml   = simplexml_load_string($info, 'SimpleXMLElement', LIBXML_NOCDATA);



		return $array = json_decode(json_encode((array)$xml), TRUE);



	}

}



?>