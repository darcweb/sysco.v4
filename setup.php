<?php

//configurações padrão do sistema
$params['sysco']['version'] = $_SERVER['VERSION_SYS'];
$params['sysco']['versionsas'] = '3.1.0';
$params['sysco']['versionfiles'] = '2.2.0';
$params['sysco']['ssl'] = false;
$params['sysco']['compact'] = true;
$params['sysco']['errors'] = true;
$params['sysco']['syskey'] = "JHJIHSJH68HJ9870985JHG765BKHJLLS";

//configurações de layout
$params['sysco']['title'] = "Sysco ffgf - v 4.0";
$params['sysco']['author'] = "Darc Web";
$params['sysco']['copyright'] = "Copyright © 2016 - {author} - Todos os direitos reservados";
$params['sysco']['baseurl'] = $_SERVER['SERVER_NAME'];
$params['sysco']['url'] = $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];



//configurações de base
$params['sysco']['system'] = $_SERVER['SYSTEM'];
$params['sysco']['domain'] = "www.sysco.com.br";
$params['sysco']['root'] = 'sysco.v4/';
$params['sysco']['uploads'] = 'storange/uploads/';

//pasta do template
$params['sysco']['application'] = "sysco_v4";
$params['sysco']['setindex'] = "index";

//conexão com o banco de dados local
$params['sysco']['localdb']['driver'] = "mysql";
$params['sysco']['localdb']['host'] = "localhost";
$params['sysco']['localdb']['port'] = 3306;
$params['sysco']['localdb']['user'] = "root";
$params['sysco']['localdb']['password'] = "abc123";
$params['sysco']['localdb']['database'] = "sysco_v4";

//conexão com o banco de dados online
$params['sysco']['localdb']['driver'] = "mysql";
$params['sysco']['onlinedb']['host'] = "localhost";
$params['sysco']['onlinedb']['port'] = 3306;
$params['sysco']['onlinedb']['user'] = "coisapp_user";
$params['sysco']['onlinedb']['password'] = "7KMoxf584m";
$params['sysco']['onlinedb']['database'] = "coisapp_database";


