<?php

namespace NanoFrm\Tests\Samples;

use PHPUnit\Framework\TestCase;
use NanoFrm\Frm;
use NanoFrm\QueryBuilder;
use NanoFrm\Functions;
use NanoFrm\Tests\DTO\Pessoa;
use NanoFrm\Tests\DTO\Endereco;

/**
 * Description of FrmUsageTest
 *
 * @author cleber.zanella
 */
class FrmUsageTest extends TestCase {
    
    public function testQuery1(){
        
        // SELECT p.*, e.* FROM Pessoa p
        //      LEFT OUTER JOIN Endereco e ON(e.pessoaId = p.id AND e.principal)
        // WHERE e.principal
        // ORDER BY p.nome ASC, logradouro DESC 
        // LIMIT 10,5
        
        $query = Frm::query(function(QueryBuilder $b, Functions $f) {
            
            return $b->from(Pessoa::class)
            ->join(Endereco::class)
                ->on( function(Pessoa $p, Endereco $e) use($f) {
                    return $f->and($f->eq($e->pessoaId, $p->id, $e->principal));
                })
            ->where( function(Pessoa $p, Endereco $e) use($f) {
                return $e->principal;
            })
            ->order( function(Pessoa $p, Endereco $e) use($f) {
                $f->by($f->asc($p->nome), $f->desc($e->logradouro));
            })
            ->limit(10,5);
        });
        
    }
}
