<?php

class ProdutosController {
    
    public $sysco = null;
    public $produtos = null;
     
    function __construct($sysco,$model){
        
        $this->sysco = $sysco;
        $this->produtos = $model;
                
    }
    
    function produtos(){
         
        //print_r($this);
        //var_dump($this->sysco->params);
        print_r($this->sysco->objectCount());
        //print_r($this->produtos);
        echo "<br><br>";
        
        //print_r($this->produtos->insert());
        echo $this->produtos->insert(array('teste1'=>'1','teste2'=>'2','teste3'=>'3'));//"<br><br>";
        
        $text = "Controller produtos <br/>";
        
        return $text;
        
    }

}
