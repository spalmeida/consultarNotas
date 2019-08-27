<?php

//DEFINE O DIRETÓRIO PADRÃO DAS PAGINAS DO SISTEMA
define("DIRECTORY", $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR.'controller'.DIRECTORY_SEPARATOR);
//pasta central
define("DIRPATH", $_SERVER['DOCUMENT_ROOT'].DIRECTORY_SEPARATOR);
//carrega os modulos do sistema
define("MODULES", $_SERVER['DOCUMENT_ROOT'].'/controller/pages/module_include/');
//sair do sistema
define("LOGOUT", '/sessao/sair');
define("ASSETS", '/controller/pages/assets/');