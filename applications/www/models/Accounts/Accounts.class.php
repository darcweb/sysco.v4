<?php

class Accounts {
        
    public $sysco = null;
    public $config = array();
    public $fields = array();

    function __construct($sysco){
        
        $this->sysco = $sysco;
        
        $this->config['table'] = "sysco_accounts";
        $this->config['engine'] = "InnoDB";
        $this->config['charset'] = "utf8";
        $this->config['collation'] = "utf8_general_ci";
        
        $this->fields['id'] = array('type'=>'int(11)','default'=>'','key'=>'pk');
        $this->fields['account_key'] = array('type'=>'varchar(128)','null'=>'yes');
        $this->fields['account_father'] = array('type'=>'int(11)','null'=>'yes');
        $this->fields['category'] = array('type'=>'int(11)','null'=>'yes');
        $this->fields['type'] = array('type'=>'varchar(8)','null'=>'yes');
        $this->fields['name'] = array('type'=>'varchar(64)','null'=>'yes');
        $this->fields['nickname'] = array('type'=>'varchar(32)','null'=>'yes');
        $this->fields['dateofbirth'] = array('type'=>'varchar(16)','null'=>'yes');
        $this->fields['taxida'] = array('type'=>'varchar(32)','null'=>'yes');
        $this->fields['taxidb'] = array('type'=>'varchar(32)','null'=>'yes');
        $this->fields['token'] = array('type'=>'varchar(128)','null'=>'yes');
        $this->fields['lastupdate'] = array('type'=>'datetime','null'=>'yes');
        $this->fields['datetime'] = array('type'=>'datetime','null'=>'yes');
        $this->fields['status'] = array('type'=>'int(11)','default'=>'1','null'=>'yes');
        
    }
    
    function insert($param) {
        
        $result = "Nada foi feito!";
        
        $query = "INSERT INTO ".$this->config['table']." (name,description,price,datetime,status)VALUE("
                . "'".$param['name']."',"
                . "'".$param['description']."',"
                . "'".$this->sysco->functions->formatMoney($param['value'],'clean')."',"
                . "'".$this->sysco->functions->datetimeSet()."',"
                . "'1')";
        if($this->sysco->objectQuery($query)){
            $result = "Inserido com sucesso!";
        }else{
            $result = "Falha ao inserir!";
        }

        return $result;
        
    }
    
    function getitem(){
        
        $item = array(
            'name'=>'nome teste',
            'description'=>'descricao teste',
            'price'=>'10,00',
        );
        $result = $item;
        
        return $result;
        
    }
    
}
