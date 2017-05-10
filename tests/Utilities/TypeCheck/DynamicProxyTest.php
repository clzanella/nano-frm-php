<?php
declare(strict_types=1);

namespace NanoFrm\Tests\Utilities\TypeCheck;

use PHPUnit\Framework\TestCase;
use NanoFrm\Utilities\TypeCheck\DynamicProxy;
use NanoFrm\Utilities\TypeCheck\FunctionHandler;
use NanoFrm\Utilities\TypeCheck\PropertyHandler;
use NanoFrm\Tests\DTO\Pessoa;
use NanoFrm\Tests\DTO\Endereco;
use NanoFrm\Tests\DTO\FinalPessoa;

/**
 * Description of DynamicProxy
 *
 * @author cleber.zanella
 */
class DynamicProxyTest extends TestCase
{
    public function testInstanceOfProxyMatch()
    {
        
        $proxy = DynamicProxy::proxy(Pessoa::class);
        
        $this->assertInstanceOf(
            Pessoa::class,
            $proxy
        );
    }

    public function testExceptionOnInvalidType()
    {
        $this->expectException(\InvalidArgumentException::class);

        DynamicProxy::proxy(FinalPessoa::class);
    }

    public function testPropertyAccess()
    {

        $propertyHandler = new class implements PropertyHandler {
            
            public $lastPropertyOperation;
            public $lastPropertyName;
            public $lastPropertyValue;
            
            public function handleProperty($operation, $propertyName, $propertyValue) {
                $this->lastPropertyOperation = $operation;
                $this->lastPropertyName = $propertyName;
                $this->lastPropertyValue = $propertyValue;
            }
        };
        
        $proxy = new Pessoa();// just for IDE refactoring
        $proxy = DynamicProxy::proxy(Pessoa::class, $propertyHandler);
        
        // getter
        $proxy->id;
        
        $this->assertEquals($propertyHandler->lastPropertyOperation, PropertyHandler::GET);
        $this->assertEquals($propertyHandler->lastPropertyName, "id");
        $this->assertEquals($propertyHandler->lastPropertyValue, null);
        
        // setter
        $proxy->nome = "Joao";
        
        $this->assertEquals($propertyHandler->lastPropertyOperation, PropertyHandler::SET);
        $this->assertEquals($propertyHandler->lastPropertyName, "nome");
        $this->assertEquals($propertyHandler->lastPropertyValue, "Joao");
    }
    
    public function testFunctionAccess(){
        
        $functionHandler = new class implements FunctionHandler {
            
            public $lastFunctionName;
            public $lastFunctionArgs;
            
            public function handleFunction($functionName, $functionArgs) {
                $this->lastFunctionName = $functionName;
                $this->lastFunctionArgs = $functionArgs;
                
                return null;
            }
        };
        
        $proxy = new Endereco();// just for IDE refactoring
        $proxy = DynamicProxy::proxy(Endereco::class, $functionHandler);
        
        $proxy->getEnderecoCompleto();
        
        $this->assertEquals($functionHandler->lastFunctionName, "getEnderecoCompleto");
        $this->assertEquals(count($functionHandler->lastFunctionArgs), 0);

    }
}
