<?php
namespace Isolator;
/**
 * Description of FullBox
 *
 * @author Brian Parra
 */
abstract class FullBox extends \Isolator\Box {
    
    protected $version; //1 byte
    protected $flags = [0 ,0 ,0]; // 3 bytes 
    
    function __construct($file) {

        parent::__construct($file);  
        
    }
    

    protected function readHeader(){
        
        $headerSize = 12; //4 for size, 4 for type, 4 for flags
        if($this->size > 4294967295){
            $headerSize += 4;
        }
        
        
        fseek($this->file, $this->offset + $headerSize-4); //Set the read position to get version/flags
        $this->version = \Isolator\ByteUtils::readUnsignedByte($this->file);
        $this->flags[0] = \Isolator\ByteUtils::readUnsignedByte($this->file);
        $this->flags[1] = \Isolator\ByteUtils::readUnsignedByte($this->file);
        $this->flags[2] = \Isolator\ByteUtils::readUnsignedByte($this->file);
        
        $this->headerSize = $headerSize; //Set the overall header size;
        
    }
    
    public function getVersion(){
        return $this->version;
    }
    
    public function getFlags(){
        return $this->flags;
    }
    
}
