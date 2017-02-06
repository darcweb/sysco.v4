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
        //echo "<br><br>";
        //var_dump($this->sysco->params);
        //var_dump($this->sysco->request->a);
        //print_r($this->produtos);
        
        //print_r($this->produtos->insert());
        echo $this->produtos->insert(array('teste1'=>'1','teste2'=>'2','teste3'=>'3'));//"<br><br>";
        
        $text = "Controller produtos <br/>";
        
        return $text;
        
    }

}
