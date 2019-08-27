<?php

namespace Utility;
use Connect\Connect;

class Links extends Connect{


	function Menu_right($title, $link, $icon, $color){

		echo $menu = '<li class="k-aside-secondary__nav-toolbar-item nav-item" data-toggle="k-tooltip" title="'.$title.'" data-placement="left">

		<a class="k-aside-secondary__nav-toolbar-icon nav-link" '.$link.' >

		<i class="'.$icon.' k-font-'.$color.'"></i>

		</a>

		</li>';

	}



	function Menu_status($value, $page){



		$menu_open = "k-menu__item  k-menu__item--submenu k-menu__item--open k-menu__item--here";

		$menu_close = "k-menu__item k-menu__item--submenu k-menu__item--here k-menu__item--hover";



		if($value == $page){

			$return = $menu_open;

		}else{

			$return = $menu_close;

		}



		return $return;



	}



	function subMenu($system_permission, $page, $page_name, $permission_name, $submenu_name, $link){



		$submenu_open = "k-menu__item k-menu__item--active";

		$submenu_close = "k-menu__item";



		if(isset($page[2])){

			if($page[2] == $page_name){



				$classe = $submenu_open;

			}else{

				$classe = $submenu_close;

			}

		}else{



			$classe = $submenu_close;

		}



		if($system_permission[0][$permission_name] == 1){

			echo  '	<div class="k-menu__submenu">

			<span class="k-menu__arrow"></span>

			<ul class="k-menu__subnav">

			<li class="'.$classe.'" aria-haspopup="true"><a id="k_blockui_3_5" href="'.$link.'" class="btn-upper k-menu__link "><i class="k-menu__link-bullet k-menu__link-bullet--dot"><span></span></i><span class="k-menu__link-text">'.$submenu_name.'</span></a>

			</li>



			</ul>

			</div>';

		}







	}









}





?>