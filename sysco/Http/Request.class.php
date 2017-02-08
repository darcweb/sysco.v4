<?php
namespace Sysco\Http;

/**
 * @system Sysco Framework
 * @version 4.0.1
 * 
 * @class Request - Classe responsave pela formacao de rotas
 * 
 * @copyright (c) 2015, DARC WEB - SOLUÇÕES WEB
 * @author Dárcio Gomes :: <darcio@darcweb.com.br>
*/


class Request {
    
    private $appcheck;
    
    public 
            $sysco,
            $application,
            $applications,
            $baseurl,
            $appbaseurl,
            $appviewsurl,
            $appfilesurl,
            $uploads,
            $uploadsfiles,
            $root,
            $local,
            $storage,
            $gets,
            $a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r;
    
    function __construct($sysco){
        
        $this->sysco = $sysco;
        
        $this->init();
        
    }
    
    private function init(){
        
        $this->application = "www";
        $this->applications = array();
        
        $applicationsGet = explode(",",$this->sysco->params['sysco']['applications']);
        foreach($applicationsGet as $app){

            $appnameclean = $this->sysco->functions->linkTitle($app);
            if($app == $appnameclean){
                $this->applications[] = $appnameclean;
            }else{
                echo "Por padrão da web não é permitido caracteres especiais como endereço de aplicação, exemplo:<br/>";
                echo "Nome de aplicação web inválido: {$app}<br/>";
                echo "Nome de aplicação web válido: {$appnameclean}<br/>";
                die;
            }

        }
        
        //$this->prepareVars();
        $this->prepareGets();
        
    }
    
    private function cleanGETURL($strURL){
        
        $resturnSetEx = explode('?',$strURL);
        $resturnSet = $resturnSetEx[0];
        
        return $resturnSet;
        
    }

    private function setGets($a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r){
        
        $this->a = $a; $this->b = $b; $this->c = $c; 
        $this->d = $d; $this->e = $e; $this->f = $f; 
        $this->g = $g; $this->h = $h; $this->i = $i; 
        $this->j = $j; $this->k = $k; $this->l = $l; 
        $this->m = $m; $this->n = $n; $this->o = $o;
        $this->p = $p; $this->q = $q; $this->r = $r;
        
    }
    
    private function getsPriorize(){
        
        /*if($this->sysco->functions->onlineCheck() || @strpos($this->sysco->params['sysco']['root'],$this->gets[1]) === false){
            $a=1; $b=2; $c=3; $d=4; $e=5; $f=6; $g=7; $h=8; $i=9; $j=10; $k=11; $l=12; $m=13; $n=14; $o=15; $p=16; $q=17; $r=18;
        }else{
            $a=2; $b=3; $c=4; $d=5; $e=6; $f=7; $g=8; $h=9; $i=10; $j=11; $k=12; $l=13; $m=14; $n=15; $o=16; $p=17; $q=18; $r=19;
        }*/
        
        if($this->application == "www"){
            
            if($this->sysco->functions->onlineCheck() || @strpos($this->sysco->params['sysco']['root'],$this->gets[1]) === false){
                $a=1; $b=2; $c=3; $d=4; $e=5; $f=6; $g=7; $h=8; $i=9; $j=10; $k=11; $l=12; $m=13; $n=14; $o=15; $p=16; $q=17; $r=18;
            }else{
                $a=2; $b=3; $c=4; $d=5; $e=6; $f=7; $g=8; $h=9; $i=10; $j=11; $k=12; $l=13; $m=14; $n=15; $o=16; $p=17; $q=18; $r=19;
            }

        }else{
            
            if($this->sysco->functions->onlineCheck() || @strpos($this->sysco->params['sysco']['root'],$this->gets[1]) === false){
                $a=2; $b=3; $c=4; $d=5; $e=6; $f=7; $g=8; $h=9; $i=10; $j=11; $k=12; $l=13; $m=14; $n=15; $o=16; $p=17; $q=18; $r=19;
            }else{
                $a=3; $b=4; $c=5; $d=6; $e=7; $f=8; $g=9; $h=10; $i=11; $j=12; $k=13; $l=14; $m=15; $n=16; $o=17; $p=18; $q=19; $r=20;
            }
            
        }
        
        @$this->setGets($this->gets[$a],$this->gets[$b],$this->gets[$c],$this->gets[$d],$this->gets[$e],$this->gets[$f],$this->gets[$g],$this->gets[$h],$this->gets[$i],$this->gets[$j],$this->gets[$k],$this->gets[$l],$this->gets[$m],$this->gets[$n],$this->gets[$o],$this->gets[$p],$this->gets[$q],$this->gets[$r]);
        
        $this->prepareVars();
        
    }
    
    private function prepareGets(){
        
        $this->gets = ['','','','','','','','','','','','','','','','','','',''];
        
        $url = $_SERVER['REQUEST_URI'];
        $url = str_replace(":80", "", $url);
        $url = str_replace($this->sysco->params['sysco']['root'], '', $url);
        $caract = strlen($url);
        $utm = substr($url, ($caract-1), 1);
        $local = "";
        $setgetindex = false;

        $getsprepare = explode("/", $url);
        foreach($getsprepare as $index => $value){

            foreach($this->applications as $app){

               if($app == $value && !$setgetindex){
                    $this->application = $app;
                    $setgetindex = true;
                }

            }

            if($setgetindex || $this->application == "www"){
                $this->gets[$index] = $value;
            }
            
        }
        
        $this->getsPriorize();
        
    }

    private function prepareVars(){
        
        if($this->sysco->functions->onlineCheck() || @strpos($this->local(),$this->sysco->params['sysco']['root']) === false){
            
            $this->baseurl = $this->getProtocoll().$_SERVER['SERVER_NAME'].$this->sysco->SPATH;
                    
        }else{

            $this->baseurl = $this->getProtocoll().$_SERVER['SERVER_NAME'].$this->sysco->SPATH.$this->sysco->params['sysco']['root'];
            
        }

        $this->root = $this->sysco->rootPath;
        
        $this->appbaseurl = $this->baseurl.($this->application == "www"?"":$this->application.$this->sysco->SPATH);
        $this->appviewsurl = $this->baseurl."applications".$this->sysco->SPATH.$this->application.$this->sysco->SPATH."views".$this->sysco->SPATH;
        $this->appfilesurl = $this->baseurl."applications".$this->sysco->SPATH.$this->application.$this->sysco->SPATH;
        
        $this->uploads = $this->baseurl.$this->sysco->params['sysco']['uploads'];
        $this->uploadsfiles = $this->root.$this->sysco->params['sysco']['uploads'];

        $this->local = $this->local();
        $this->storage = $this->root."storage".$this->sysco->SPATH;
        
    }
    
    public function getProtocoll(){
        
        $result = @(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on')?'https':'http';
        $result = $this->sysco->functions->onlyChar($this->sysco->functions->strCase($result)).'://';
        
        return $result;
        
    }

    private function setport(){
        
        if($_SERVER['SERVER_PORT'] != '80' && $_SERVER['SERVER_PORT'] != '443'){
            $port = ':'.$_SERVER['SERVER_PORT'];
        }else{
            $port = '';
        }
        
        return $port;
        
    }

    public function local(){
        
        $server = $_SERVER['SERVER_NAME'];
        $uri = $_SERVER['REQUEST_URI'];
        $local = $this->getProtocoll().$server.$this->setport().$uri;
        
        return $local;
        
    }

}
