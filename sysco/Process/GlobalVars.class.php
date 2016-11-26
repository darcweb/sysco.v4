<?php
namespace Sysco\Proccess;

/**
 * @system Sysco Framework
 * @version 4.0.1
 * 
 * @class GlobalVars - Classe de preparação de variáveis global da aplicação
 * 
 * @copyright (c) 2015, DARC WEB - SOLUÇÕES WEB
 * @author Dárcio Gomes :: <darcio@darcweb.com.br>
*/

class GlobalVars {
    
    public $basevars = array(
        'header'=>'{{header}}',
        'content'=>'{{content}}',
        'footer'=>'{{footer}}',
        'baseurl'=>'{{baseurl}}',
        'uploads'=>'{{uploads}}',
        'uploadsfiles'=>'{{uploadsfiles}}',
        'root'=>'{{root}}',
        'local'=>'{{local}}',
    );
    
    public $vars = array(
        'header'=>'Topo da pagina',
        'content'=>'Conteúdo da página',
        'footer'=>'Rodapé da página',
        'baseurl'=>'URL base da aplicação',
        'uploads'=>'URL de uploads da aplicação',
        'uploadsfiles'=>'Caminho da pasta de uploads',
        'root'=>'Caminho raiz do sistema',
        'local'=>'Caminho atual da requisição',
    );
    
    private $request = null;
    
    function __construct($req){
        
        $this->request = $req;
        
        $preparevars = array(
            'baseurl'=> $this->request->baseurl,
            'uploads'=> $this->request->uploads,
            'uploadsfiles'=> $this->request->uploadsfiles,
            'root'=> $this->request->root,
            'local'=> $this->request->local,
        );
        
        $this->vars = array_merge($this->vars,$preparevars);
        
    }
    
    public function compile($HTML = ""){

        foreach($this->basevars as $index=>$value){
        
            $HTML = str_replace($value,$this->vars[$index],$HTML);
        
        }
        
        return $HTML;
        
    }
    
}
