<?php

namespace Notas;

use NFePHP\DA\NFe\Danfe;
use NFePHP\DA\Legacy\FilesFolders;


class GerarDanfe{

 	/**
 	* @param $dir recebe o caminho do xml que será convertido eçm danfe.
 	*/

 	public function exec($dir){


 		$filename = str_replace('.xml','.pdf',$dir);
 		$docxml = FilesFolders::readFile($dir);

 		try {

 			$danfe = new Danfe($docxml, 'P', 'A4', '', 'I', '');
 			$id = $danfe->montaDANFE();
 			$pdf = $danfe->render();

    //o pdf porde ser exibido como view no browser
    //salvo em arquivo
    //ou setado para download forçado no browser
    //ou ainda gravado na base de dados

 			header('Content-Description: File Transfer');
 			header('Content-Type: application/octet-stream');
 			header('Content-Disposition: attachment; filename='.$filename);

 			return $pdf;

 		} catch (InvalidArgumentException $e) {

 			return "Ocorreu um erro durante o processamento :" . $e->getMessage();

 		}
 	}
 }

 ?>