<?php

namespace Isolator\Boxes;

/**
 * Description of Tkhd
 *
 * @author Brian Parra
 */
class Tkhd extends \Isolator\FullBox {

    private $creationTime;
    private $modificationTime;
    private $trackID;
    private $duration;
    private $layer;
    private $alternateGroup;
    private $volume;
    private $matrix = [];
    private $width;
    private $height;
    
    private $minf; //cached reference to $minf box


    function __construct($file) {

        $this->boxType = \Isolator\Box::TKHD;
        parent::__construct($file);
    }

    public function loadData() {

        if ($this->largeSize) {

            $this->headerSize = 16; //4 size + 4 type + 8 extended size; 
        } else {

            $this->headerSize = 8; //4 size + 4 type 
        }

        fseek($this->file, $this->offset + $this->headerSize);
        $this->version = \Isolator\ByteUtils::readUnsignedByte($this->file);
        $this->flags[0] = \Isolator\ByteUtils::readUnsignedByte($this->file);
        $this->flags[1] = \Isolator\ByteUtils::readUnsignedByte($this->file);
        $this->flags[2] = \Isolator\ByteUtils::readUnsignedByte($this->file);

        if ($this->version == 1) {
            $this->creationTime = \Isolator\ByteUtils::readUnsignedLong($this->file);
            $this->modificationTime = \Isolator\ByteUtils::readUnsignedLong($this->file);
            $this->trackID = \Isolator\ByteUtils::readUnsingedInteger($this->file);
            \Isolator\ByteUtils::skipBytes($this->file, 4); //skip 32 bits
            $this->duration = \Isolator\ByteUtils::readUnsignedLong($this->file);
        } else {

            $this->creationTime = \Isolator\ByteUtils::readUnsingedInteger($this->file);
            $this->modificationTime = \Isolator\ByteUtils::readUnsingedInteger($this->file);
            $this->trackID = \Isolator\ByteUtils::readUnsingedInteger($this->file);
            \Isolator\ByteUtils::skipBytes($this->file, 4); //skip 32 bits
            $this->duration = \Isolator\ByteUtils::readUnsingedInteger($this->file);
        }

        \Isolator\ByteUtils::skipBytes($this->file, 8); //skip 64 bits
        $this->layer = \Isolator\ByteUtils::readUnsignedShort($this->file);
        $this->alternateGroup = \Isolator\ByteUtils::readUnsignedShort($this->file);
        $this->volume = \Isolator\ByteUtils::readUnsignedShort($this->file);

        \Isolator\ByteUtils::skipBytes($this->file, 2); //skip 16 bits

        $this->readMatrix();
        $this->width = \Isolator\ByteUtils::readFixedPoint16_16($this->file);
        $this->height = \Isolator\ByteUtils::readFixedPoint16_16($this->file);
    }

    public function getBoxDetails() {

        $details = [];

        $details["Size"] = $this->size;
        $details["Offset"] = $this->offset;
        $details["Version"] = $this->version;
        $details["Flags"] = [
            "Track Enabled" => $this->flags[0],
            "Track In Movie" => $this->flags[1],
            "Track In Preview" => $this->flags[2]
        ];

        $details["Creation Time"] = $this->creationTime;
        $details["Modification Time"] = $this->modificationTime;
        $details["Track ID"] = $this->trackID;
        $details["Duration"] = $this->duration;
        $details["Layer"] = $this->layer;
        $details["Alternate Group"] = $this->alternateGroup;
        $details["Volume"] = $this->volume;
        $details["Matrix"] = $this->matrix;
        $details["Height"] = $this->height;
        $details["Width"] = $this->width;



        return $details;
    }

    protected function readMatrix() {
        for ($i = 0; $i < 9; $i++) {
            $this->matrix[$i] = \Isolator\ByteUtils::readUnsingedInteger($this->file);
        }
    }
    
    public function getTrackID(){
        return $this->trackID;
    }

}
