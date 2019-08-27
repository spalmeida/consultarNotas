<?php

namespace Notas;

class Manifesta{

	public function exec($chNFe, $tools){

		try {

	    $tpEvento = '210210'; //ciencia da operação
	    $xJust = ''; //a ciencia não requer justificativa
	    $nSeqEvento = 1; //a ciencia em geral será numero inicial de uma sequencia para essa nota e evento

	    $response = $tools->sefazManifesta($chNFe,$tpEvento,$xJust = '',$nSeqEvento = 1);
	    //você pode padronizar os dados de retorno atraves da classe abaixo
	    //de forma a facilitar a extração dos dados do XML
	    //NOTA: mas lembre-se que esse XML muitas vezes será necessário,
	    //      quando houver a necessidade de protocolos
	    var_dump($response);
	    echo "<br><br><br><br>";

	} catch (\Exception $e) {
		echo $e->getMessage();
	}

}

}