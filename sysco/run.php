<?php
/**
 * @system Sysco Framework
 * @version 4.0.1
 * 
 * @action Aciona o Autoloader
 * 
 * @copyright (c) 2015, DARC WEB - SOLUÇÕES WEB
 * @author Dárcio Gomes :: <darcio@darcweb.com.br>
*/
  
function customError($error_level,$error_message,$error_file,$error_line,$error_context) {
    
    echo "<b>Error:</b> [$error_level] $error_message<br>";
    echo "<b>Arquivo:</b> $error_file - Linha: $error_line<br>";
    
    print_r($error_context);
    
    echo "Ending Script";
    die();
}

//set_error_handler("customError");

include_once(dirname(__FILE__) . "/Start/Botloader.php");

use Sysco\Engine\Work\Run;

new Run();


