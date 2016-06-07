<?php

namespace Isolator\Boxes;

/**
 * Description of Minf
 *
 * @author Brian Parra
 */
class Minf extends \Isolator\Box {
    
    

    function __construct($file) {
        
        $this->boxType = \Isolator\Box::MINF;
        parent::__construct($file);
        
    }
    
    public function loadData() {

        $headerLength = 8;
        $internalOffset = $this->offset + $headerLength;
        $this->loadChildBoxes($internalOffset);
    }
    
    public function getStblBox(){
        
        foreach ($this->boxMap as $box) {
            if ($box instanceof \Isolator\Boxes\Stbl) {
                return $box;
            }
        }
        return null;
        
    }
    
    public function isAudioTrack(){
        return $this->getStblBox()->isAudioTrack();
    }

}