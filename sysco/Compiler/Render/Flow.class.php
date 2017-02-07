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
    
    public $system = null;
    public $model = null;
    public $modelobject = null;
    public $controller = null;
    
    function __construct($build){
        
        $this->system = $build->system;
        $model = $build->model;
        $this->model = $model;
        $this->modelobject = $build->modelobject;
        
        $this->init();
        
    }
    
    private function serachClass(){
        
        $pathfile = dirname(__FILE__);
        $pathEx = explode($this->system->SPATH.$_SERVER['SYSTEM'].$this->system->SPATH,$pathfile);
        $pathset = $pathEx[0].$this->system->SPATH.'applications'.$this->system->SPATH;
        $pathclass = $pathset.$this->system->params[$_SERVER['SYSTEM']]['application'].$this->system->SPATH.'controllers'.$this->system->SPATH;
        
        if(is_dir($pathclass)){

            $files = array_diff(scandir($pathclass), array('.','..')); 
            
            foreach ($files as $file) { 
                
                $includeclass = $pathclass.$file;
                $this->includeClass($includeclass);
                
            } 
            
        }else{
            
            echo " está faltando o diretório controllers na aplicação ".$this->system->params[$_SERVER['SYSTEM']]['application'];
            die;
            
        }
        
    }
    
    private function includeClass($includeClass){
        
        if(file_exists($includeClass)){
            
            $wayex = explode($this->system->SPATH,$includeClass);
            
            $file = end($wayex);
            $path = str_replace($file,"",$includeClass);
            
            $classname = str_replace($this->system->class_ex,"",$file); 
            
            include($includeClass);
            
            $this->proccess($classname);
            
        }else{
            
            echo " está faltando o diretório models na aplicação ".$this->system->params[$_SERVER['SYSTEM']]['application'];
            die;
            
        }
        
    }
    
    private function proccess($classname){
        
        $this->$classname = new $classname($this->system,$this->modelobject);
        $this->setobject($this->$classname,$classname);
        
    }
    
    private function setobject($object,$class){
        
        $this->controller = $class;
        
    }
    
    public function init(){
        
        $this->serachClass();
        
        return new Sights($this);
        
    }
    
}
