<?php

namespace Isolator\Boxes;

/**
 * Description of Stsd
 *
 * @author Brian Parra
 */
class Stco extends \Isolator\Box {
    
    

    function __construct($file) {
        
        $this->boxType = \Isolator\Box::STCO;
        parent::__construct($file);
        
    }
    
    public function loadData() {
        
    }

}