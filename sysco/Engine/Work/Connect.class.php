<?php

namespace Sysco\Engine\Work;

use PDO;

class Connect {

    private $connect = null;
    private $config = array();

    function __construct($getconfig){
        
        $this->config = $getconfig;
        
    }
    
    private function conn(){
        try{
            if($this->connect == null){
                $options = array();
                if($this->config['driver'] == 'firebird'){
                    $dsn = 'firebird:dbname='.$this->config['database'].';host='.$this->config['host'].';port='.$this->config['port'];
                }else if($this->config['driver'] == 'postgresql'){
                    $dsn = 'pgsql:dbname='.$this->config['database'].';host='.$this->config['host'].';port='.$this->config['port'];
                }else if($this->config['driver'] == 'mysql'){
                    $dsn = 'mysql:host='.$this->config['host'].';port='.$this->config['port'].';dbname='.$this->config['database'];
                    $options = array(
                                PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING,
                                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                            );
                }
                $this->connect = new PDO($dsn,$this->config['user'],$this->config['password'],$options);
            }
        }catch(PDOException $e){
            //echo "<hr/>ERRO DE CONEXÃO COM O BANCO DE DADOS<br /><br />Código de erro: {$e->getCode()}<br />Mensagem: {$e->getMessage()}<hr/>";
            //die;
        }
        return $this->connect;
    }

    public function getConnect(){
        return $this->conn();
    }

}
