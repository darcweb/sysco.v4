<?php

namespace Sysco\Compiler\Render;

/**
 * @system Sysco Framework
 * @version 4.0.1
 * 
 * @class Flow - Classe responsavel pela distribuição do fluxo de controllers
 * 
 * @copyright (c) 2015, DARC WEB - SOLUÇÕES WEB
 * @author Dárcio Gomes :: <darcio@darcweb.com.br>
 */

use Sysco\Compiler\Render\Sights;

class Flow {
    
    private $loadcontinue = true;
    public $sysco = null;
    public $model = null;
    public $modelobject = null;
    public $controller = null;
    
    function __construct($build){
        
        $this->sysco = $build->sysco;
        $model = $build->model;
        $this->model = $model;
        $this->modelobject = $build->modelobject;
        
        $this->init();
        
    }
    
    private function serachClass(){
        
        $pathfile = dirname(__FILE__);
        $pathEx = explode($this->sysco->SPATH.$_SERVER['SYSTEM'].$this->sysco->SPATH,$pathfile);
        $pathset = $pathEx[0].$this->sysco->SPATH.'applications'.$this->sysco->SPATH;
        $pathclass = $pathset.$this->sysco->request->application.$this->sysco->SPATH.'controllers'.$this->sysco->SPATH;
        
        if(is_dir($pathclass)){

            $files = array_diff(scandir($pathclass), array('.','..')); 
            $includeclass = "";
            
            foreach ($files as $file) { 
                
                $includeclass = $pathclass.$file;
                $this->includeClass($includeclass);
                
            } 
            
            if($includeclass == ""){
                
                $this->loadcontinue = false;

            }

        }else{
            
            echo " está faltando o diretório controllers na aplicação ".$this->sysco->request->application;
            die;
            
        }
    }
    
    private function includeClass($includeClass){
        
        if(file_exists($includeClass)){
            
            $wayex = explode($this->sysco->SPATH,$includeClass);
            
            $file = end($wayex);
            $path = str_replace($file,"",$includeClass);
            
            $classname = str_replace($this->sysco->class_ex,"",$file); 
            include_once($includeClass);
            
            $this->proccess($classname);
            
        }else{
            
            echo " Está faltando o diretório controllers na aplicação ".$this->sysco->request->application;
            die;
            
        }
        
    }
    
    private function proccess($classname){
    
        $this->$classname = new $classname($this->sysco,$this->modelobject);
        $this->setobject($this->$classname,$classname);
        
    }
    
    private function setobject($object,$class){
        
        $this->controller = $class;
        
    }
    
    public function init(){
        
        $this->serachClass();
        
        if($this->loadcontinue){
            
            return new Sights($this);
            
        }else{
            
            echo "Está faltando a controller principal da aplicação {$this->sysco->request->application}, exemplo:<br/>";
            echo "Main.class.php na pasta controllers";
            die;
            
        }
        
    }
    
}
