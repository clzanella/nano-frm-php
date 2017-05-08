<?php
declare(strict_types=1);

namespace NanoFrm\Utilities\TypeCheck;

interface PropertyHandler {
    
    const GET = "get";
    const SET = "set";

    function handleProperty($operation, $propertyName, $propertyValue);
}

interface FunctionHandler {
    function handleFunction($functionName, $functionArgs);
}

trait ClassProxy {
    
    public $lastCalledProperty;
    public $lastCalledFunction;
    
    public $propertyHandler;
    public $functionHandler;
    
    protected function interceptProperty($operation, $name, $value){
        
        $this->lastCalledProperty = $name;
        
        if($this->propertyHandler !== null){
            return $this->propertyHandler->handleProperty($operation, $name, $value);
        }
        
        return null;
    }
    
    protected function interceptFunction($name, $args){
        
        $this->lastCalledFunction = $name;
        
        if($this->functionHandler !== null){
            return $this->functionHandler->handleFunction($name, $args);
        }
        
        return null;
    }
    
}

/**
 * Description of DynamicProxy
 *
 * @author cleber.zanella
 */
class DynamicProxy {
    
    private function __construct() {

    }

    public static function proxy($className, $handler = null){
        
        $reflector = new \ReflectionClass($className);
        
        if($reflector->isFinal()){
            throw new \InvalidArgumentException("{$reflector->getShortName()} can't be final.");
        }
        
        $spyClassName = "{$reflector->getShortName()}SpyProxy";
        
        $proxy = DynamicProxy::createProxy($spyClassName, $className);

        // remove todas as propriedades herdadas para direcionar todas as chamadas para o __get do proxy
        foreach ($reflector->getProperties() as $prop) {
            $name = $prop->name;
            
            unset($proxy->{"{$name}"});
        }
        
        if($handler instanceof PropertyHandler){
            $proxy->propertyHandler = $handler;
        }

        if($handler instanceof FunctionHandler){
            $proxy->functionHandler = $handler;
        }
        
        return $proxy;
    }
    
    private static function createProxy($spyClassName, $className){
        
        if(class_exists($spyClassName)){
            return new $spyClassName();
        }
        
        // TODO : HHVM not support eval();
        $ret = eval(" 

            class {$spyClassName} extends {$className} {
                
                use \NanoFrm\Utilities\TypeCheck\ClassProxy;

                public function __get(\$name) {
                    return \$this->interceptProperty(\NanoFrm\Utilities\TypeCheck\PropertyHandler::GET, \$name, null);
                }

                public function __set(\$name, \$value) {
                    \$this->interceptProperty(\NanoFrm\Utilities\TypeCheck\PropertyHandler::SET, \$name, \$value);
                }

                public function __call(\$method, \$args) {
                    \$this->interceptFunction(\$method, \$args); 
                }
            }

            return new {$spyClassName}();

        ");
            
        return $ret;
    }

}
