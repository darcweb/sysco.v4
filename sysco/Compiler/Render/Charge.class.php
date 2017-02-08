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
    
    private $loadcontinue = true;
    public $sysco = null;
    public $model = null;
    public $modelobject = null;
    
    function __construct($build){
        
        $this->sysco = $build;
        
        $this->init();
        
    }
    
    private function serachClass(){
        
        $pathfile = dirname(__FILE__);
        $pathEx = explode($this->sysco->SPATH.$_SERVER['SYSTEM'].$this->sysco->SPATH,$pathfile);
        $pathset = $pathEx[0].$this->sysco->SPATH.'applications'.$this->sysco->SPATH;
        $pathclass = $pathset.$this->sysco->request->application.$this->sysco->SPATH.'models'.$this->sysco->SPATH;
        
        if(is_dir($pathclass)){

            $files = array_diff(scandir($pathclass), array('.','..')); 
            
            foreach ($files as $file) { 
                $includeclass = "";
                $fileEx = explode(".",$file);
                $modelName = strtolower($fileEx[0]);

                foreach($this->sysco->request->gets as $get){
                        
                    $includeclass = $pathclass.$file;

                    if(file_exists($includeclass) && is_dir($includeclass)){
                        $subfiles = array_diff(scandir($includeclass), array('.','..')); 
                        foreach ($subfiles as $subfile) { 
                            $includeclass = $includeclass.$this->sysco->SPATH.$subfile."";
                        }
                    }
                }

                if(file_exists($includeclass) && !is_dir($includeclass)){
                    $this->includeClass($includeclass);
                }else{
                    
                    $this->loadcontinue = false;

                }
                
            } 
            
        }else{
            
            echo " está faltando o diretório models na aplicação ".$this->sysco->request->application;
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
            
            $namespace = $this->sysco->getnamespace($includeClass);
            
            $this->proccess($namespace,$classname);
            
        }else{
            
            echo " está faltando o diretório models na aplicação ".$this->sysco->request->application;
            die;
            
        }
        
    }
    
    private function proccess($namespace,$classname){

        $this->$classname = new $classname($this->sysco);
        $this->setobject($this->$classname,$classname);

    }
    
    private function setobject($object,$class){
        
        $this->model = $class;
        
        foreach($object->fields as $index => $value){
            
            $this->$class->$index = $value;
            
        }
        
        $this->modelobject = $this->$class;
        
        if($this->sysco->request->b == "modeling"){
            if($this->sysco->params[$_SERVER['SYSTEM']]['syskey'] == $this->sysco->request->a){
                new Modeling($this,$this->modelobject);
            }else{
                echo "Chave de sistema inválida!";
                die;
            }
        }
        
    }
    
    public function init(){
        
        $this->serachClass();
         
        if($this->loadcontinue){
            
            return new Flow($this); 
            
        }else{
            
            echo "Está faltando a controller principal da aplicação {$this->sysco->request->application}, exemplo:<br/>";
            echo "Main.class.php na pasta models";
            die;
            
        }
        
        
    }
    
}
