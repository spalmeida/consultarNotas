<?php

namespace Utility;

class Paginations{

	const DEFAULT = "default";

	const DIR 	  = "controller/pages/";


	public function Pages($value){

		$result = explode("/", $value);

		Paginations::Execute($result);


	}


	public function Execute($url){


		if (empty($_SESSION)) {



			if($url[2] == 'home'){

				Paginations::Prepare("home.php", self::DEFAULT);





			}elseif($url[2] == 'blog'){

				Paginations::Prepare("blog.php", self::DEFAULT);







			}else{

				require_once("controller/pages/error_page/error.php");

			}





		}else{

			require_once("controller/pages/login.php");

		}



	}



	public function Prepare($page, $type){



		if($type == self::DEFAULT){

			require_once(self::DIR.$page);

		}

	}



/*	public function render(array $data){



		ob_start();

		extract($data);



		include $this->file



		return ob_get_clean();

	}*/

}





