<?php

namespace NanoFrm\Tests\Samples;

use NanoFrm\QueryBuilder;
use NanoFrm\Functions;
use NanoFrm\Tests\DTO\Pessoa;
use NanoFrm\Tests\DTO\Endereco;

class PessoaDao {
    
    /**
     * @var QueryBuilder
     */
    private $builder;
    
    public function __construct(QueryBuilder $builder) {
        $this->builder = $builder;
    }
    
    public function byName($name) : QueryBuilder {
        
        // SELECT p.*, e.* FROM Pessoa p
        //      LEFT OUTER JOIN Endereco e ON(e.pessoaId = p.id AND e.principal)
        // WHERE p.nome = ?
        // ORDER BY p.nome ASC, logradouro DESC 
        
        return $this->builder->from(Pessoa::class)
            ->join(Endereco::class)
                ->on( function(Functions $f, Pessoa $p, Endereco $e) {
                    return $f->and($f->eq($e->pessoaId, $p->id, $e->principal));
                })
            ->where( function(Functions $f, Pessoa $p, Endereco $e) use($name) {
                return $f->eq($p->nome, $name);
            })
            ->order( function(Functions $f, Pessoa $p, Endereco $e) {
                $f->by($f->asc($p->nome), $f->desc($e->logradouro));
            });
    }

}

/**
 * Description of FrmUsageWithDITest
 *
 * @author cleber.zanella
 */
class FrmUsageWithDITest extends TestCase {
    
    public function testPessoaQuery(){

        // https://github.com/jamolkhon/Sharbat
        $injector; \Sharbat\Sharbat::createInjector(new MainModule());

        /* @var $dao PessoaDao */
        $dao = $injector->getInstance(PessoaDao::class);
        
        $query = $dao->byName("Cleber")
                ->order( function(Functions $f, Pessoa $p) { // overwrite default sort
                    $f->by($f->desc($p->dateAudit->updatedOn));
                })
                ->limit(10,5); // paging
        
        $query->query()->asList();
        
                
    }
}
