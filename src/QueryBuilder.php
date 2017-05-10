<?php

namespace NanoFrm;

use NanoFrm\Utilities\TypeCheck\DynamicProxy;
use NanoFrm\Utilities\TypeCheck\PropertyHandler;

/**
 * Description of QueryBuilder
 *
 * @author cleber.zanella
 */
class QueryBuilder {
    
    public $readingTree = false;
    
    public function proxy($className){
        
        return DynamicProxy::proxy($className, new class implements PropertyHandler {
            
            public function handleProperty($operation, $propertyName, $propertyValue) {
                
                //if()
                
            }
        });
        
    }
    
}
