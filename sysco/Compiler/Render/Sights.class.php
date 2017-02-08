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
    
    private $loadcontinue = true;
    protected $setindex = "";
    protected static $codehtml = "";
    protected $viewspath = array("theme","pages");
    protected $functions = null;
    protected $sysco = null;
    protected $model = null;
    protected $modelobject = null;
    protected $controller = null;
    protected $request = null;
    protected $globalvars = null;
    protected $compileid = null;
    private $loadindex = true;
    
    function __construct($build){
        
        $this->compileid = uniqid().time();
        
        $this->sysco = $build->sysco;
        
        $model = $build->model;
        $this->model = $model;
        $this->modelobject = $build->modelobject;
        $this->$model = $this->modelobject;
        
        $controller = $build->controller;
        $this->controller = $controller;
        $this->$controller = $build->$controller;
        
        $this->request = $this->sysco->request;
        $this->globalvars = new GlobalVars($this->request);
        $this->functions = $this->sysco->functions;
        
        $this->init();
        
    }
    
    public function init() {
        
        return $this->showView();

    }

    private function setIndex() {
        
        $this->setindex = $this->layer("applications." . $this->sysco->request->application . ".theme." . $this->sysco->params[$_SERVER['SYSTEM']]['setindex']);
        
        return $this->setindex;
        
    }

    private function setPage() {
        
        $result = "";

        $setway = "";
        $checkway = "applications." . $this->sysco->request->application.".pages";
        $rowback = false;
        $isdir = false;
        $x = 1;
        $mainset = "";
        
        foreach($this->request->gets as $ind=>$get){
            
            if($get != ""){
                
                foreach($this->viewspath as $pathview){
                    
                    if($pathview == "pages" && $this->request->application != $get){
                        
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
            
            $checkhome = $this->request->root.'applications'.$this->sysco->SPATH.$this->sysco->request->application.$this->sysco->SPATH.'views'.$this->sysco->SPATH.'pages'.$this->sysco->SPATH.'home.layer.php';
            
            if(file_exists($checkhome)){

                $setway = 'applications.'.$this->sysco->request->application.'.pages.home';

            }else{

                $checkhome = $this->request->root.'applications'.$this->sysco->SPATH.$this->sysco->request->application.$this->sysco->SPATH.'views'.$this->sysco->SPATH.'pages'.$this->sysco->SPATH.'home'.$this->sysco->SPATH.'main.layer.php';

                if(file_exists($checkhome)){

                    $setway = 'applications.'.$this->sysco->request->application.'.pages.home.main';

                }
                
            }   

        }

        $result = $this->layer($setway);
        
        if(!file_exists($result)){
            
            header('HTTP/1.1 404 Not Found');

            $custom404 = $this->request->root.'applications'.$this->sysco->SPATH.$this->sysco->request->application.$this->sysco->SPATH.'views'.$this->sysco->SPATH.'errors'.$this->sysco->SPATH.'404.layer.php';

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
        $leway = str_replace('.', $this->sysco->SPATH, $leway);
        
        foreach($this->viewspath as $pathview){
            if(strpos($leway,$pathview) !== false){
                $leway = str_replace($pathview.$this->sysco->SPATH, 'views'.$this->sysco->SPATH.$pathview.$this->sysco->SPATH, $leway);
            }
        }
        
        $leway = $this->request->root . $leway;
        
        if (file_exists($leway.$le)) {
            $result = $leway.$le;
        } else {
            if (file_exists($leway.$this->sysco->SPATH.$increment.$le)) {
                $result = $leway.$this->sysco->SPATH.$increment.$le;
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
                
                $this->loadcontinue = false;
                echo "Erro 404";

            }

        }

        $extendsHTML = @ob_get_contents();
        $compactHTML = @ob_get_clean();

        if ($this->sysco->params[$_SERVER['SYSTEM']]['compact'] === true) {
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

        $pathcompile = $this->request->storage."compile".$this->sysco->SPATH;
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
        
        if ($this->sysco->params[$_SERVER['SYSTEM']]['compact'] == true) {
            
            $view = $this->compress($view);
            
        }
            
        $print = $view;

        if($this->loadcontinue){
            
            return print($print);
        
        }else{
            
            echo "Não foi possível compilar nenhuma view da aplicação {$this->sysco->request->application}!";
            die;
            
        }

    }

}
