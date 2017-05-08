<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace NanoFrm\Tests\DTO;

/**
 * Description of Endereco
 *
 * @author cleber.zanella
 */
class Endereco {
    
    /**
     * @var int
     */
    public $id;
    
    /**
     * @var bool
     */
    public $principal;
    
    /**
     * @var string
     */
    public $logradouro;
    
    /**
     * @var int
     */
    public $numero;
    
    /**
     * @var int
     */
    public $pessoaId;
    
    /**
     * @var DateTimeAudit 
     */
    public $dateAudit;
    
    public function getEnderecoCompleto(){
        return $this->logradouro . " " . $this->numero;
    }
}
