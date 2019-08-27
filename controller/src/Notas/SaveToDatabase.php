<?php



namespace Notas;

use Notas\RetornXmlArray as Save;



class SaveToDatabase{

	public function save_db($dir_xml){

		return SaveToDatabase::exec($dir_xml);

	}

	public function exec($dir_xml){

		$dir = opendir($dir_xml);

		$info = array();

		while(false !== ( $file = readdir($dir)) ) {

			if (( $file != '.' ) && ( $file != '..' )) {

				$full = $dir_xml . $file;

				if ( is_dir($full) ) {

					SaveToDatabase::exec($full);

				}else{

					$ext = ltrim( substr( $full, strrpos( $full, '.' ) ), '.' );

					if($ext == "xml"){

						$info[] = Save::exec($full);

					}

				}

			}

		}

		return $info;

	}

}

?>