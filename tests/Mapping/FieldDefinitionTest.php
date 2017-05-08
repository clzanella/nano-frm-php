<?php

namespace NanoFrm\Tests\Mapping;

use PHPUnit\Framework\TestCase;
use NanoFrm\Mapping\FieldDefinition;
use NanoFrm\Tests\DTO\Pessoa;
use NanoFrm\Tests\DTO\Endereco;

class FieldDefinitionMock extends FieldDefinition {
    
    public $literal;
    
    /**
     * @var int 
     */
    public $number;
    
    public static function getPropertyType($className, $propertyName){
        return FieldDefinition::getPropertyType($className, $propertyName);
    }
    
}

/**
 * Description of FieldDefinitionTest
 *
 * @author cleber.zanella
 */
class FieldDefinitionTest extends TestCase
{
    public function testCreation() {
        
        $fieldDef = $this->defaultField();
        $this->assertPessoaId($fieldDef);
    }

    private function assertPessoaId(FieldDefinition $fieldDef){
        
        $this->assertEquals($fieldDef->getState()->parentClass, Pessoa::class);
        $this->assertEquals($fieldDef->getFieldName(), "id");
        $this->assertEquals($fieldDef->getState()->fieldType, "int");
        
    }
    
    public function testReferenceTo() {
        
        $fieldDef = new FieldDefinition(Endereco::class, "pessoaId");
        
        $fieldDef->referenceTo(function(Pessoa $p) { return $p->id; });
        
        $this->assertEquals($fieldDef->getState()->foreignClass, Pessoa::class);
        $this->assertEquals($fieldDef->getState()->foreignFieldName, "id");
        
    }
    
    private function defaultField() : FieldDefinition {
        return new FieldDefinition(Pessoa::class, "id");
    }

    public function testPrimaryKey() {
        
        $fieldDef1 = $this->defaultField();
        $this->assertFalse($fieldDef1->getState()->primaryKey);

        $fieldDef2 = $this->defaultField()->primaryKey();
        $this->assertTrue($fieldDef2->getState()->primaryKey);
        
    }

    public function testAutoGenerated() {
        
        $fieldDef1 = $this->defaultField();
        $this->assertFalse($fieldDef1->getState()->autoGenerated);

        $fieldDef2 = $this->defaultField()->autoGenerated();
        $this->assertTrue($fieldDef2->getState()->autoGenerated);
        
    }

    public function testGetPropertyType() {
        
        $literalPropType = FieldDefinitionMock::getPropertyType(FieldDefinitionMock::class, "literal");
        $numberPropType = FieldDefinitionMock::getPropertyType(FieldDefinitionMock::class, "number");

        $this->assertNull($literalPropType);
        $this->assertEquals($numberPropType, "int");
        
    }

    public function testFromClosureWithNull() {
        
        $this->expectException(\TypeError::class);
        
        FieldDefinition::fromClosure(null);
        
    }

    
    public function testFromClosureUntyped() {
        
        $this->expectException(\InvalidArgumentException::class);
        
        FieldDefinition::fromClosure(function($p) { return $p->id; });
        
    }
    
    public function testFromClosureWithoutPropCall() {
        
        $this->expectException(\InvalidArgumentException::class);
        
        FieldDefinition::fromClosure(function(Pessoa $p) { return null; });
        
    }

    public function testFromClosure() {
        
        $fieldDef = FieldDefinition::fromClosure(function(Pessoa $p) { return $p->id; });
        $this->assertPessoaId($fieldDef);

    }
    
}