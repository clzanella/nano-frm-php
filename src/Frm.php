<?php

namespace NanoFrm;

use NanoFrm\Utilities\TypeCheck\DynamicProxy;
use NanoFrm\Utilities\TypeCheck\PropertyHandler;

/**
 * Description of Frm
 *
 * @author cleber.zanella
 */
class Frm {
    //put your code here
}

class CallContext implements PropertyHandler {
    
    const DEFAULT_STRING = "/*-";

    private $callStack;
    
    private $propertiesDefaulValues;
    
    public $readingTree = false;

    public function __construct() {
        $this->callStack = new \SplStack();
    }

    public function proxy($className){
        
        return DynamicProxy::proxy($className, $this);
        
    }

    public function handleProperty($operation, $propertyName, $propertyValue) {
        
        if($this->readingTree){
            $callsAndValues = $this->verifyParams(array(), array()); // only getters
            
            $this->registerCall(new Expr(Expr::CALL, null, $propertyName, $callsAndValues));
        }
        
        return null;
    }

    public function verifyParams(array $paramTypes, array $paramValues){
        
        
        
    }
    
    public function registerCall(Expr $expr){
        
        // add at stack
        $this->callStack->push($expr);
    }

}

class Expr {
    
    const CALL = 1;
    const VALUE = 2;
    
    public $type, $returnType, $name;
    public $childrem;
    
    public function __construct($type, $returnType, $name, array $childrem) {
        
        $this->type = $type;
        $this->returnType = $returnType;
        $this->name = $name;
        $this->childrem = $childrem;
    }
    
}