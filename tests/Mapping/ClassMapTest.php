<?php

namespace NanoFrm\Tests\Mapping;

use PHPUnit\Framework\TestCase;
use NanoFrm\Mapping\ClassMap;
use NanoFrm\Tests\DTO\Endereco;
use NanoFrm\Tests\DTO\Pessoa;

/**
 * Description of MapperTest
 *
 * @author cleber.zanella
 */
class ClassMapTest  extends TestCase
{
    public function testClassMap()
    {
        
        $enderecoMapping = new class extends ClassMap {
            
            private function e() : Endereco {
                return $this->fromClass(Endereco::class);
            }

            public function map() {
                
                $this->field($this->e()->id)->primaryKey()->autoGenerated();
                
                $this->field($this->e()->logradouro);
                $this->field($this->e()->numero);
                $this->fieldFn(function(Endereco $e){ return $e->principal; });
                
                $this->field($this->e()->pessoaId)
                        ->referenceTo(function(Pessoa $p) { return $p->id; });
                
            }
            
        };
        
        $enderecoMapping->map();
        
        $this->assertEquals(count($enderecoMapping->getFields()), 5);
        
        // id
        $idField = $enderecoMapping->getField("id")->getState();
        
        $this->assertEquals($idField->parentClass, Endereco::class);
        $this->assertEquals($idField->fieldName, "id");
        $this->assertEquals($idField->fieldType, "int");
        
        $this->assertTrue($idField->primaryKey);
        $this->assertTrue($idField->autoGenerated);
        
        $this->assertNull($idField->foreignClass);
        $this->assertNull($idField->foreignFieldName);

        // principal
        $principalField = $enderecoMapping->getField("principal")->getState();
        
        $this->assertEquals($principalField->parentClass, Endereco::class);
        $this->assertEquals($principalField->fieldName, "principal");
        $this->assertEquals($principalField->fieldType, "bool");
        
        $this->assertFalse($principalField->primaryKey);
        $this->assertFalse($principalField->autoGenerated);
        
        $this->assertNull($principalField->foreignClass);
        $this->assertNull($principalField->foreignFieldName);

        // logradouro
        $logradouroField = $enderecoMapping->getField("logradouro")->getState();
        
        $this->assertEquals($logradouroField->parentClass, Endereco::class);
        $this->assertEquals($logradouroField->fieldName, "logradouro");
        $this->assertEquals($logradouroField->fieldType, "string");
        
        $this->assertFalse($logradouroField->primaryKey);
        $this->assertFalse($logradouroField->autoGenerated);
        
        $this->assertNull($logradouroField->foreignClass);
        $this->assertNull($logradouroField->foreignFieldName);
        
        // numero
        $numeroField = $enderecoMapping->getField("numero")->getState();
        
        $this->assertEquals($numeroField->parentClass, Endereco::class);
        $this->assertEquals($numeroField->fieldName, "numero");
        $this->assertEquals($numeroField->fieldType, "int");
        
        $this->assertFalse($numeroField->primaryKey);
        $this->assertFalse($numeroField->autoGenerated);
        
        $this->assertNull($numeroField->foreignClass);
        $this->assertNull($numeroField->foreignFieldName);

        // pessoaId
        $pessoaIdField = $enderecoMapping->getField("pessoaId")->getState();
        
        $this->assertEquals($pessoaIdField->parentClass, Endereco::class);
        $this->assertEquals($pessoaIdField->fieldName, "pessoaId");
        $this->assertEquals($pessoaIdField->fieldType, "int");
        
        $this->assertFalse($pessoaIdField->primaryKey);
        $this->assertFalse($pessoaIdField->autoGenerated);
        
        $this->assertEquals($pessoaIdField->foreignClass, Pessoa::class);
        $this->assertEquals($pessoaIdField->foreignFieldName, "id");

    }
}
