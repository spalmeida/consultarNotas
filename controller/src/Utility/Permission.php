<?php

namespace Utility;
use Connect\Connect;

class Permission extends Connect{


	public function Name($name, $system_permission){





		if($system_permission[0][$name] == 0){

			echo '<script>';

			echo 'window.location.assign("/index.php")';

			echo '</script>';

		}



	}

}





?>