<?php
namespace Sysco\Engine\Work;

/**
 * @system Sysco Framework
 * @version 4.0.1
 * 
 * @class System - Classe principal do sistema
 * 
 * @copyright (c) 2015, DARC WEB - SOLUÇÕES WEB
 * @author Dárcio Gomes :: <darcio@darcweb.com.br>
*/

use Sysco\Engine\Utils\Functions;
use Sysco\Http\Request;
use Sysco\Compiler\Render\Charge;

class System {
    
    public 
        $layer_ex = ".layer.php",
        $class_ex = ".class.php",
        $SPATH,
        $rootPath,
        $params,
        $request,
        $functions;

    function __construct() {
        
        $this->SPATH = DIRECTORY_SEPARATOR;
        
        $pathsys = dirname(__FILE__);
        $pathEx = explode($this->SPATH.$_SERVER['SYSTEM'].$this->SPATH,$pathsys);
        $pathsys = $pathEx[0].$this->SPATH;
        
        $this->rootPath = $pathsys;
        
        $this->includeSetup('setup.php');
        
        $this->functions = new Functions($this);
        
        $this->request = new Request($this);
        
        return $this->init();
        
    }
    
    private function includeSetup($fileInclude){
        
        $currentinclude = $this->rootPath.$fileInclude;
        
        if(file_exists($currentinclude)){
            
            include($currentinclude);
            $this->params = $params;
            
            return;
            
        }else{
            
            return $currentinclude;

        }
        
    }
    
    public function getnamespace($file) {
        
        $ns = NULL;
        $handle = fopen($file, "r");
        
        if ($handle) {
            
            while (($line = fgets($handle)) !== false) {
                if (strpos($line, 'namespace') === 0) {
                    $parts = explode(' ', $line);
                    $ns = rtrim(trim($parts[1]), ';');
                    break;
                }
            }
            
            fclose($handle);
            
        }
        
        return $ns;
    }
    
    public function init(){
        
        $showerror = $this->params[$_SERVER['SYSTEM']]['errors'];

        ini_set('display_errors',$showerror);
        ini_set('display_startup_erros',$showerror);
        error_reporting(E_ALL);
        
        return new Charge($this);
        
    }
    
}
