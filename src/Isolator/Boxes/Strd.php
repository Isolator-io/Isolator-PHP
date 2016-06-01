<?php

namespace Isolator\Boxes;

/**
 * Extended Language Tag
 * @author Brian Parra
 */
class Strd extends \Isolator\Box {
    
    

    function __construct($file) {
        
        $this->boxType = \Isolator\Box::STRD;
        parent::__construct($file);
        
    }
    
    public function loadData() {
        
    }

}