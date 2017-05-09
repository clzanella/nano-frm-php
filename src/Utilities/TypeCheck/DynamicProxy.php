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
        
        $parentMethodsOverrides = DynamicProxy::createProxyMethodsOverriding($className);
        
        // TODO : HHVM not support eval();
        $proxyClassCode = " 

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
                
                {$parentMethodsOverrides}

            }

            return new {$spyClassName}();

        ";

        $ret = eval($proxyClassCode);
        return $ret;
    }
    
    private static function createProxyMethodsOverriding($className){
        
        $argListFunc = function($parameters, $withTypes) {
          
            $result = "";
            
            foreach ($parameters as $parameter){

                if($parameter->hasType() && $withTypes){
                    $result .= $parameter->getType() . " ";
                }

                $result .= "$".$parameter->name.", ";
            }
            
            if(strlen($result) > 0){
                $result = rtrim($result,", ");
            }
            
            return $result;
        };
        
        $class = new \ReflectionClass($className);
        $methods = $class->getMethods(\ReflectionMethod::IS_PUBLIC);
        
        $mResult = "";
        
        foreach ($methods as $method){
            
            if($method->isFinal()){
                continue;
            }
            
            $methodName = $method->name;
            $signatureArgs = $argListFunc($method->getParameters(), true);
            $parentCallArgs = $argListFunc($method->getParameters(), false);
            $return = $method->hasReturnType() ? "return" : "";
            
            $mResult .= "public function {$methodName}({$signatureArgs}) { {$return} \$this->interceptFunction(\"{$methodName}\", array({$parentCallArgs})); }";
            $mResult .= "\n";
        }
        
        return $mResult;
    }

}
