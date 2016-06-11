<?php

namespace Isolator;

/**
 * Container for an iso file
 *
 * @author Brian Parra
 */
class Iso {



    private $filename;
    private $file;
    private $fileSize;
    private $boxMap;
    private $moov; //Keep direct reference to movie box to not waste time iterating every time

    function __construct($filename) {

        $this->filename = $filename;
        $this->file = fopen($this->filename, "rb+") or die("Unable to open file!");
        $this->fileSize = filesize($filename);
        $this->boxMap = [];
        //$this->loadData();
        //var_dump($this->boxMap);
    }
    
    

    public function getFileName(){
        return $this->filename;
    }

    public function getBoxMap(){
        return $this->boxMap;
    }
    
    
    public function displayBoxMap() {
        
        echo "<div>";
        echo "<h1>>" . basename($this->filename) . "</h1>";
        foreach ($this->boxMap as $box) {
            echo "<div>";
            $box->displayBoxMap();
            echo "box";
            echo "</div>";
        }
        echo "</div>";
        
    }

    public function displayDetailedBoxMap() {
      
        echo "<div>";
        echo "<h1>>" . basename($this->filename) . "</h1>";
        foreach ($this->boxMap as $box) {
            echo "<div>";
            $box->displayDetailedBoxMap();
            echo "</div>";
        }
        echo "</div>";
        
    }

    public function loadData() {

        $offset = 0;
        $boxSize;
        $boxType;
        $dataBuffer;
        $newBox;
        
        do {
            //Set the offset 
            //fseek($this->file, $offset);

            //$boxSize = ByteUtils::readUnsingedInteger($this->file);
            //$boxType = ByteUtils::readBoxType($this->file);
            

                
            $newBox = \Isolator\Box::parseTopLevelBox($this->file, $offset, $this);
            
            if($newBox instanceof \Isolator\Boxes\Moov){
                $this->moov = $newBox;
           
                
            }
                
   

            $offset += $newBox->getSize();
        } while ($offset < $this->fileSize);
    }

    public function addBox($box){
        
        $this->boxMap[] = $box;
        
    }
    

    public function getAudioTracks(){
        return $this->moov->getAudioTracks();
    }
    
    
    public function getTrackByID($trackID){
        
        $moovTracks = $this->moov->getTracks();
        foreach($moovTracks as $box){
            if( $box->getTrackID() == $trackID)
                return $box;
        }
        
        return null;
    }
    
    public static function IsoFileFrom(){
        
    }
    
    function createEmptyIso(){
        
    }
    
    function getFtyp(){
        foreach($this->boxMap as $box){
            if( $box instanceof \Isolator\Boxes\Ftyp)
                return $box;
        }
    }
    
    public function getMoov(){
        foreach($this->boxMap as $box){
            if( $box instanceof \Isolator\Boxes\Moov)
                return $box;
        }
    }
    
    public function getMvhd(){
        foreach($this->getMoov()->getBoxMap() as $box){
            if( $box instanceof \Isolator\Boxes\Mvhd)
                return $box;
        }
    }
    
    public function getFile(){
        return $this->file;
    }


    public static function RipAudio($inputIso, $outputFile){
        
        if(file_exists ( $outputFile)){
            unlink ($outputFile);
        }
        
        //Fix this later
        $out = fopen($outputFile,"w");
        
        
        $iso = new Iso($outputFile);
        
        //Need to rethink the constructors
        $ftyp = new \Isolator\Boxes\Ftyp($iso->getFile());
        $ftyp->loadDataFromBox($inputIso->getFtyp());
        
        
        
        //$moov = new \Isolator\Boxes\Moov($iso->getFile());
        //$mvhd = new \Isolator\Boxes\Mvhd($iso->getFile());
        //$mvhd->loadDataFromBox($inputIso->getMvhd());
        
        $iso->addBox($ftyp);
        
        $free = new \Isolator\Boxes\Free($iso->getFile());
        $iso->addBox($free);
        $free->setFreeBytes(8); //Add some extra padding to extend header size if necessary
        $ftyp->writeToFile();
        $free->writeToFile();
        
        //$moov->addBox($mvhd);
        //$mvhd->setContainer($moov);
        //$iso->addBox($moov);
        
        $mdat = new \Isolator\Boxes\Mdat($iso->getFile());
        $iso->addBox($mdat);
        
        $mdat->prepareForWriting();
        
        $audioTracks = $inputIso->getAudioTracks();
        
        foreach ($audioTracks as $track){
            $audioTrack = new \Isolator\Presentation\AudioTrack($track);
            $audioTrack->setOutputFile($outputFile);
            $audioTrack->dumpBinary($iso->getFile()); // Testing for now
        }
        
        $mdat->finalizeWriting();
        
        
        return $iso;
        
    }
    
    
    public function finalize(){
        
    }
    
   
    
}
