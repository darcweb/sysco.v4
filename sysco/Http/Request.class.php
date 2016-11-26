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
    
    public 
            $system,
            $baseurl,
            $uploads,
            $uploadsfiles,
            $root,
            $local,
            $gets,
            $a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r;
    
    function __construct($sys){
        
        $this->system = $sys;
        
        $this->init();
        
    }
    
    public function init(){
        
        $this->prepareGets();
        $this->prepareVars();
        
    }
    
    public function cleanGETURL($strURL){
        $resturnSetEx = explode('?',$strURL);
        $resturnSet = $resturnSetEx[0];
        return $resturnSet;
    }

    public function setGets($a,$b,$c,$d,$e,$f,$g,$h,$i,$j,$k,$l,$m,$n,$o,$p,$q,$r){
        
        $this->a = $a; $this->b = $b; $this->c = $c; 
        $this->d = $d; $this->e = $e; $this->f = $f; 
        $this->g = $g; $this->h = $h; $this->i = $i; 
        $this->j = $j; $this->k = $k; $this->l = $l; 
        $this->m = $m; $this->n = $n; $this->o = $o;
        $this->p = $p; $this->q = $q; $this->r = $r;
        
    }

    public function prepareVars(){
       
        if($this->system->functions->onlineCheck() || @strpos($this->system->params['sysco']['root'],$this->a) === false){
            
            $this->root = $this->system->rootPath;
            $this->baseurl = $this->getProtocoll().$_SERVER['SERVER_NAME'].$this->system->SPATH;
            $this->uploads = $this->baseurl.$this->system->params['sysco']['uploads'];
            $this->uploadsfiles = $this->root.$this->system->params['sysco']['uploads'];
                    
        }else{

            $this->root = $this->system->rootPath.$this->system->params['sysco']['root'];
            $this->baseurl = $this->getProtocoll().$_SERVER['SERVER_NAME'].$this->system->SPATH.$this->system->params['sysco']['root'];
            $this->uploads = $this->baseurl.$this->system->params['sysco']['uploads'];
            $this->uploadsfiles = $this->root.$this->system->params['sysco']['uploads'];
            
        }
        
        $this->local = $this->local();
        
    }
    
    public function prepareGets(){
        
        $this->gets = ['','','','','','','','','','','','','','','','','','',''];
        
        $url = $_SERVER['REQUEST_URI'];
        $url = str_replace(":80", "", $url);
        $url = str_replace($this->system->params['sysco']['root'], '', $url);
        $caract = strlen($url);
        $utm = substr($url, ($caract-1), 1);
        $local = "";
        
        $getsprepare = explode("/", $url);
        foreach($getsprepare as $index => $value){
        
            $this->gets[$index] = $value;
            
        }
        
        $num = count($this->gets);
        
        if($this->system->functions->onlineCheck() || @strpos($this->system->params['sysco']['root'],$this->gets[1]) === false){
            $a=1; $b=2; $c=3; $d=4; $e=5; $f=6; $g=7; $h=8; $i=9; $j=10; $k=11; $l=12; $m=13; $n=14; $o=15; $p=16; $q=17; $r=18;
        }else{
            $a=2; $b=3; $c=4; $d=5; $e=6; $f=7; $g=8; $h=9; $i=10; $j=11; $k=12; $l=13; $m=14; $n=15; $o=16; $p=17; $q=18; $r=19;
        }
        
        @$this->setGets($this->gets[$a],$this->gets[$b],$this->gets[$c],$this->gets[$d],$this->gets[$e],$this->gets[$f],$this->gets[$g],$this->gets[$h],$this->gets[$i],$this->gets[$j],$this->gets[$k],$this->gets[$l],$this->gets[$m],$this->gets[$n],$this->gets[$o],$this->gets[$p],$this->gets[$q],$this->gets[$r]);
        
    }

    public function getProtocoll(){
        
        $result = @(isset($_SERVER['HTTPS'])&&$_SERVER['HTTPS']=='on')?'https':'http';
        $result = $this->system->functions->onlyChar($this->system->functions->strCase($result)).'://';
        
        return $result;
        
    }

    public function setport(){
        
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
