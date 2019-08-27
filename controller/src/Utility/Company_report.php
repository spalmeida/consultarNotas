<?php

namespace Utility;
use Connect\Connect;

class Company_report extends Connect {



	public function CNPJ($empresa_cnpj){



		$nf_entrada = Company_report::select("nf", "empresa_cnpj = $empresa_cnpj and type = 'entrada'");

		$nf_saida	= Company_report::select("nf", "empresa_cnpj = $empresa_cnpj and type = 'saida'");

		$empresa 	= Company_report::select("system_companys", "company_cnpj = $empresa_cnpj");





		$mes_atual = date('m');

		$ano_atual = date('Y');





		$dados = array(



			"total_nf_entrada" => count($nf_entrada),

			"total_nf_saida"   => count($nf_saida),

			"empresa_numero"   => $empresa[0]['company_number']

		);



		return $dados;





	}



}



