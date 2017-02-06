<?php

class Produtos {
        
    public $config = array();
    public $fields = array();

    function __construct(){
        
        $this->config['table'] = "produtos";
        $this->config['engine'] = "MyISAM";
        $this->config['charset'] = "utf8";
        $this->config['collation'] = "utf8_general_ci";
        
        $this->fields['id'] = array('type'=>'int(11)','default'=>'','key'=>'pk');
        $this->fields['account_id'] = array('type'=>'int(32)','null'=>'yes');
        $this->fields['name'] = array('type'=>'varchar(256)','default'=>'Meu nome','null'=>'yes');
        $this->fields['description'] = array('type'=>'text','null'=>'yes');
        $this->fields['price'] = array('type'=>'float','null'=>'yes');
        $this->fields['datetime'] = array('type'=>'datetime','null'=>'yes');
        $this->fields['status'] = array('type'=>'int(11)','null'=>'yes');
        
    }
    
    function insert($param) {
        
        //$this->functions->objectQuery("");
        
    }
    
    function getitem(){
        
        print_r($this);
        
        $result = "<br><br>Model produtos";
        
        return $result;
        
    }
    
}
