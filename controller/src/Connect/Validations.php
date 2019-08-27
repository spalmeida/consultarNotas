<?php

namespace Connect;
use Connect\Connect;
use Utility\Utility;

	class Validations extends Connect{


		public function Valida($user_id, $user_name, $user_mail, $user_status, $url){



			$_SESSION['user_id'] 		= $user_id;

			$_SESSION['user_name'] 		= $user_name;

			$_SESSION['user_mail'] 		= $user_mail;

			$_SESSION['user_status'] 	= $user_status;



			$utility = new Utility();



			$utility->Log("O usuário entrou no sistema", $user_id, $user_name, "Login");


			$end = '<script type="text/javascript"> window.location.replace("'.$url.'"); </script>';





			return $end;

		}

	}



