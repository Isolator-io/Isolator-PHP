<?php

namespace Isolator\Boxes;

/**
 * Description of Hdlr
 *
 * @author Brian Parra
 */
class Hdlr extends \Isolator\FullBox {

    //ISO BMFF 
    const VIDE = "vide";
    const SOUN = "soun";
    const HINT = "hint";
    const META = "meta";
    const NULL_HANDLER = "null";
    //MP4 types
    const ODSM = "odsm";
    const CRSM = "crsm"; //crsm
    const SDSM = "sdsm"; //sdsm
    const M7SM = "m7sm"; //m7sm
    const OCSM = "ocsm"; //ocsm
    const IPSM = "ipsm"; //ipsm
    const MJSM = "mjsm"; //mjsm

    private $handlerType = self::NULL_HANDLER;
    private $name = "SoundHandler"; // Place holder for now

    function __construct($file) {

        $this->boxType = \Isolator\Box::HDLR;
        parent::__construct($file);
    }

    public function loadData() {
        $this->readHeader();
        \Isolator\ByteUtils::skipBytes($this->file, 4);
        $this->handlerType = \Isolator\ByteUtils::read4Char($this->file);
        \Isolator\ByteUtils::skipBytes($this->file, 12);
        
        //Get exact number of characters to read to prevent any malicious out of bounds reading
        $internalOffset = ftell($this->file);
        $chars = $this->size - ($internalOffset - $this->offset) - 1;
        
        //String should be equal to the current read postition minus the length;
        $this->name = \Isolator\ByteUtils::readString($this->file, $chars);
    }

    public function getBoxDetails() {

        $details = [];

        $details["Size"] = $this->size;
        $details["Offset"] = $this->offset;
        $details["Version"] = $this->version;
        $details["Flags"] = $this->flags;
        $details["Handler Type"] = $this->handlerType;
        $details["Name"] = $this->name;



        return $details;
    }
    
    public function writeToFile() {
        $this->offset = ftell($this->file); //Save the file pointer
        $this->size = 32 + (strlen($this->name) + 1 );
        \Isolator\ByteUtils::writeUnsignedInteger($this->file, $this->size); //Write the box size place holder for now
        \Isolator\ByteUtils::writeChars($this->file, $this->boxType); //Write the box type
        
        \Isolator\ByteUtils::writeUnsignedByte($this->file, $this->version); //Write the box version
        \Isolator\ByteUtils::writeUnsignedByte($this->file, $this->flags[0]); //Write the box version
        \Isolator\ByteUtils::writeUnsignedByte($this->file, $this->flags[1]); //Write the box version
        \Isolator\ByteUtils::writeUnsignedByte($this->file, $this->flags[2]); //Write the box version //12 bytes so far
        
        \Isolator\ByteUtils::padBytes($this->file, 4);
        \Isolator\ByteUtils::writeChars($this->file, $this->handlerType); //Write the handler type
        
        \Isolator\ByteUtils::padBytes($this->file, 12);
        \Isolator\ByteUtils::writeCString($this->file, $this->name);
    }

    public function setHandlerType($handlerType){
        $this->handlerType = $handlerType;
    }
}
