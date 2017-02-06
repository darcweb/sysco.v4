<?php

namespace Sysco\Compiler\Render;

/**
 * @system Sysco Framework
 * @version 4.0.1
 * 
 * @class Sights - Classe responsavel pela compilação de views
 * 
 * @copyright (c) 2015, DARC WEB - SOLUÇÕES WEB
 * @author Dárcio Gomes :: <darcio@darcweb.com.br>
 */

use Sysco\Proccess\GlobalVars;

class Sights {
    
    protected $setindex = "";
    protected static $codehtml = "";
    protected $viewspath = array("theme","pages");
    protected $functions = null;
    protected $system = null;
    protected $model = null;
    protected $modelobject = null;
    protected $controller = null;
    protected $request = null;
    protected $globalvars = null;
    protected $compileid = null;
    private $loadindex = true;
    
    function __construct($build){
        
        $this->compileid = uniqid().time();
        
        $this->system = $build->system;
        
        $model = $build->model;
        $this->model = $model;
        $this->modelobject = $build->modelobject;
        $this->$model = $this->modelobject;
        
        $controller = $build->controller;
        $this->controller = $controller;
        $this->$controller = $build->$controller;
        
        $this->request = $this->system->request;
        $this->globalvars = new GlobalVars($this->request);
        $this->functions = $this->system->functions;
        
        $this->init();
        
    }
    
    public function init() {
        
        return $this->showView();

    }

    private function setIndex() {
        
        $this->setindex = $this->layer("application." . $this->system->params[$_SERVER['SYSTEM']]['application'] . ".theme." . $this->system->params[$_SERVER['SYSTEM']]['setindex']);
        
        return $this->setindex;
        
    }

    private function setPage() {
        
        $result = "";

        $setway = "";
        $checkway = "application." . $this->system->params[$_SERVER['SYSTEM']]['application'].".pages";
        $rowback = false;
        $isdir = false;
        $x = 1;
        $mainset = "";
        
        foreach($this->request->gets as $ind=>$get){
            
            if($get != ""){
                
                foreach($this->viewspath as $pathview){
                    
                    if($pathview == "pages"){
                        
                        $checkway .= ".".$get; 
                        $checkmain = $checkway.".main";
                        
                        $rescheck = $this->layer($checkway);
                        $rescheckmain = $this->layer($checkmain);
                        
                        $getcheck = isset($this->request->gets[$x+1])?$this->request->gets[$x+1]:'';

                        if($get == "main"){
                            $mainset = $get;
                        }
                        
                        if($rowback == false){
                            
                            if(is_dir($rescheck) && file_exists($rescheckmain) && $getcheck == ""){
                            
                                $setway = $checkmain;
                                $rowback = true;
                            
                            }else{
                                
                                if($get == $mainset){

                                    $setway = $checkway;
                                    $rowback = true;

                                }else{
                                    
                                    if(!is_dir($rescheck) && file_exists($rescheck)){

                                        $setway = $checkway;
                                        $rowback = true;
                                        
                                    }else{
                                        
                                        $setway = $checkway;
                                        
                                    }
                                }

                            }
                            
                        }

                    }
                    
                }
                
                $x++;
                
            }
            
        }
        
        if($x <= 2 && ($this->request->a == "" || ($this->request->a == "home" && $this->request->b == "") || ($this->request->a == "home" && $this->request->b == "main"))){
            
            $checkhome = $this->request->root.'application'.$this->system->SPATH.$this->system->params[$_SERVER['SYSTEM']]['application'].$this->system->SPATH.'views'.$this->system->SPATH.'pages'.$this->system->SPATH.'home.layer.php';
            
            if(file_exists($checkhome)){

                $setway = 'application.'.$this->system->params[$_SERVER['SYSTEM']]['application'].'.pages.home';

            }else{

                $checkhome = $this->request->root.'application'.$this->system->SPATH.$this->system->params[$_SERVER['SYSTEM']]['application'].$this->system->SPATH.'views'.$this->system->SPATH.'pages'.$this->system->SPATH.'home'.$this->system->SPATH.'main.layer.php';

                if(file_exists($checkhome)){

                    $setway = 'application.'.$this->system->params[$_SERVER['SYSTEM']]['application'].'.pages.home.main';

                }
                
            }   

        }
        
        $result = $this->layer($setway);
        
        if(!file_exists($result)){
            
            header('HTTP/1.1 404 Not Found');

            $custom404 = $this->request->root.'application'.$this->system->SPATH.$this->system->params[$_SERVER['SYSTEM']]['application'].$this->system->SPATH.'views'.$this->system->SPATH.'errors'.$this->system->SPATH.'404.layer.php';

            if(file_exists($custom404)){
                $result = $custom404;
            }

        }
        
        return $result;
        
    }

    public function layer($layer = "", $increment = "") {

        $result = "";

        $le = ".layer.php";
        $leway = $layer;
        $leway = str_replace('.', '/', $leway);
        
        foreach($this->viewspath as $pathview){
            if(strpos($leway,$pathview) !== false){
                $leway = str_replace($pathview.'/', 'views/'.$pathview.'/', $leway);
            }
        }
        
        $leway = $this->request->root . $leway;
        
        if (file_exists($leway.$le)) {
            $result = $leway.$le;
        } else {
            if (file_exists($leway.$this->system->SPATH.$increment.$le)) {
                $result = $leway.$this->system->SPATH.$increment.$le;
            } else {
                $result = $leway;
            }
        }

        return $result;
    }

    public function compress($buffer) {
        
        // remove comments
        $buffer = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $buffer);
        
        // remove tabs, spaces, newlines, etc.
        $buffer = str_replace(
                array("\n","; 
", ";\r\n", ";\r", ";\n", ";\t", "}\r\n", "}\r", "}\n", "}\t", ">\r\n", ">\r", ">\n", ">\t", '    ', '   ', '  '), 
                array("",";", ";", ";", ";", ";", "}", "}", "}", "}", ">", ">", ">", ">", '', '', ''), 
                $buffer);
        
        return $buffer;
        
    }
    
    public function getBuffer($getInclude) {
        
        $codehtml = "";
        
        @ob_start();

        if ($getInclude != 'none') {

            if (file_exists($getInclude)) {

                $getInclude = $getInclude;

            } else {

                $setpage = $this->request->a;

            }

            if(file_exists($getInclude)){

                include($getInclude);

            }else{

                echo "Erro 404";

            }

        }

        $extendsHTML = @ob_get_contents();
        $compactHTML = @ob_get_clean();

        if ($this->system->params[$_SERVER['SYSTEM']]['compact'] === true) {
            $codehtml = $compactHTML;
        } else {
            $codehtml = $extendsHTML;
        }

        $codehtml = $this->globalvars->compile($codehtml);

        @ob_end_flush();

        return $codehtml;

    }
    
    public function compile($codeview){
        
        $result = "";
        
        $viewcompile = "";
        
        $compile = $codeview;
        
        $pathcompile = dirname(__FILE__).$this->system->SPATH."..".$this->system->SPATH."..".$this->system->SPATH."..".$this->system->SPATH."storage".$this->system->SPATH."compile".$this->system->SPATH;
        $this->functions->delTree($pathcompile);
        
        $oldumask = umask(0); 
        if(!file_exists($pathcompile)){
            if(@!mkdir($pathcompile,0777,true)){}
        }
        umask($oldumask);

        $viewcompile = $pathcompile.$this->compileid.".php";
        
        $build = "";
        $contentcheck = $compile;
        $linesfile = preg_split('/(?:\r\n|\r|\n)/', $contentcheck); 

        @unlink($viewcompile);

        $handle = fopen($viewcompile, "w+");

        foreach($linesfile as $key => $line){

            $codeprepare = $line;
            $codeprepare = str_replace(array("{{","}}"), array("<?php "," ?>"), $codeprepare);
            $codeprepare = $codeprepare;
            $build = $codeprepare."\n";

            if($build != ""){
                fwrite($handle, $build);
            }

        }

        fclose($handle);
        
        $result = $this->getBuffer($viewcompile);

        if(file_exists($viewcompile)){
            
            @unlink($viewcompile);
            
        }
        
        return $result;
        
    }
    
    public function showView() {

        $page = $this->getBuffer($this->setPage());
        $this->globalvars->vars['content'] = $page;
        
        foreach($this->request->gets as $i => $v){
            if($v == 'setindex=false'){
                $this->loadindex = false;
            }
        }
        
        if($this->loadindex == true){
            
            $index = $this->getBuffer($this->setIndex());
            
        }else{
            
            $index = $page;
            
        }

        $view = $this->compile($index);

        if ($this->system->params[$_SERVER['SYSTEM']]['compact'] == true) {
            
            $view = $this->compress($view);
            
        }
            
        $print = $view;

        return print($print);
        
    }

}
