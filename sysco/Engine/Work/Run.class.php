<?php
namespace Sysco\Engine\Work;

/**
 * @system Sysco Framework
 * @version 4.0.1
 * 
 * @class Running - Classe responsável pelo arranque do sistema
 * 
 * @copyright (c) 2015, DARC WEB - SOLUÇÕES WEB
 * @author Dárcio Gomes :: <darcio@darcweb.com.br>
*/

use Sysco\Engine\Work\System;

class Run {

    function __construct(){
        
        $this->init();
        
    }
    
    public function init(){
        
        new System();
        
    }
	
}
