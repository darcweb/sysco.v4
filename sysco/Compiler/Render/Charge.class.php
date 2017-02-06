<?php

namespace Sysco\Compiler\Render;

/**
 * @system Sysco Framework
 * @version 4.0.1
 * 
 * @class Charge - Classe responsavel pelo carregamento de dados do banco
 * 
 * @copyright (c) 2015, DARC WEB - SOLUÇÕES WEB
 * @author Dárcio Gomes :: <darcio@darcweb.com.br>
 */

use Sysco\Compiler\Render\Flow;
use Sysco\Proccess\Modeling;

class Charge {
    
    public $system = null;
    public $model = null;
    public $modelobject = null;
    
    function __construct($build){
        
        $this->system = $build;
        
        $this->init();
        
    }
    
    private function serachClass(){
        
        $pathfile = dirname(__FILE__);
        $pathEx = explode($this->system->SPATH.$_SERVER['SYSTEM'].$this->system->SPATH,$pathfile);
        $pathset = $pathEx[0].$this->system->SPATH.'application'.$this->system->SPATH;
        $pathclass = $pathset.$this->system->params[$_SERVER['SYSTEM']]['application'].$this->system->SPATH.'models'.$this->system->SPATH;
        
        if(is_dir($pathclass)){

            $files = array_diff(scandir($pathclass), array('.','..')); 
            
            foreach ($files as $file) { 
                
                $includeclass = $pathclass.$file;
                $this->includeClass($includeclass);
                
            } 
            
        }else{
            
            echo " está faltando o diretório models na aplicação ".$this->system->params[$_SERVER['SYSTEM']]['application'];
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
            
            $namespace = $this->system->getnamespace($includeClass);
            
            $this->proccess($namespace,$classname);
            
        }else{
            
            echo " está faltando o diretório models na aplicação ".$this->system->params[$_SERVER['SYSTEM']]['application'];
            die;
            
        }
        
    }
    
    private function proccess($namespace,$classname){
        
        $this->$classname = new $classname($this->system);
        $this->setobject($this->$classname,$classname);
        
    }
    
    private function setobject($object,$class){
        
        $this->model = $class;
        
        foreach($object->fields as $index => $value){
            
            $this->$class->$index = $value;
            
        }
        
        $this->modelobject = $this->$class;
        
        new Modeling($this,$this->$class);
        
    }
    
    public function init(){
        
        $this->serachClass();
        
        return new Flow($this);
        
    }
    
}
