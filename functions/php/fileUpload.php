<?php
include('ImageResize.php');
use \Gumlet\ImageResize;
class uploadPDF{
    public $FORMNAME;
    public $STATUS = false;
    public $error = "about uploading pdf";
    public $newName = "";
    function __construct($nameOfTheform){
       $this->FORMNAME = $nameOfTheform; 
    }
    function upload_($locations){
         $cont = 0;
         $temp = $locations.md5($cont).'.pdf';
         $tempxx = md5($cont).'.pdf';
         while(file_exists($temp)) {
               $cont++;
               $temp = $locations.md5($cont).'.pdf';
               $tempxx = md5($cont).'.pdf';
         }
         if (is_uploaded_file($_FILES[$this->FORMNAME]['tmp_name'])){
             if($_FILES[$this->FORMNAME]['type'] != "application/pdf") {
                $this->error = "file must be uploaded in PDF format."; 
                $this->STATUS = false;
              } else {
                 $result = move_uploaded_file($_FILES[$this->FORMNAME]['tmp_name'], $locations.$temp);
                 if ($result == 1){
                     $this->STATUS = true;
                     $this->error = "Upload done";
                     $this->newName = $tempxx;
                 } else {
                     $this->STATUS = false;
                     $this->error = "Sorry, Error happened while uploading . ";
                 }
            }
         } else {
             $this->STATUS = false;
             $this->error = "There is no such file uploaded";
         }
        
        return $this->STATUS;
    }
}
class uploadImage{
    public $uploadOk;
    public $imageFileType;
    public $fileName;
    public $imageSize;
    public $imageData;
    public $newName;
    public $imageSizebytes;
    public $error;
    function __construct($nameOfTheform){
      if(isset($_FILES["$nameOfTheform"])){
 		$this->fileName = $_FILES["$nameOfTheform"]["name"];
        $this->imageData = $_FILES["$nameOfTheform"]["tmp_name"];
        $this->imageSize = getimagesize($this->imageData);
        $this->imageSizebytes = $_FILES["$nameOfTheform"]["size"];
        $this->imageFileType = strtolower(pathinfo($this->fileName,PATHINFO_EXTENSION));
        }
        else {
            $this->error = 'undefined index '.$nameOfTheform;
            $this->uploadOk = false;
        }
 	}
    function upload($locations,$maximumBytes,$WIDTH,$height){
        $cont = 0;
        $temp = $locations.md5($cont).'.'.$this->imageFileType;
        $tempxx = md5($cont).'.'.$this->imageFileType;
        $temp2 = $locations.'pipTemps2018101.'.$this->imageFileType;
        while(file_exists($temp)) {
               $cont++;
               $temp = $locations.md5($cont).'.'.$this->imageFileType;
               $tempxx = md5($cont).'.'.$this->imageFileType;
            }
        if($this->imageSizebytes > $maximumBytes) {
        $this->error = "Sorry, your file is too large. and must be less than ".$maximumBytes;
        $this->uploadOk = false;
               }
        
        if($this->imageFileType != "jpg" && $this->imageFileType != "png" && $this->imageFileType != "jpeg" && $this->imageFileType != "gif" && $this->imageFileType != "GIF" && $this->imageFileType != "JPEG" && $this->imageFileType != "PNG" && $this->imageFileType != "JPG") {
        $this->error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed and  this one is[".$this->imageFileType.']';
        //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $this->uploadOk = false;     
         }
        if (move_uploaded_file($this->imageData,$temp2)){
           $image = new ImageResize($temp2);
           $image->resize($WIDTH,$height);
           $image->save($temp);
           unlink($temp2);
           $this->uploadOk = true;
           $this->newName = $tempxx;
        }
      return $this->uploadOk;
  }
    function upload_($locations,$maximumBytes){
        $cont = 0;
        $temp = $locations.md5($cont).'.'.$this->imageFileType;
        $tempxx = md5($cont).'.'.$this->imageFileType;
        $temp2 = $locations.'pipTemps2018101.'.$this->imageFileType;
        while(file_exists($temp)) {
               $cont++;
               $temp = $locations.md5($cont).'.'.$this->imageFileType;
               $tempxx = md5($cont).'.'.$this->imageFileType;
            }
        if($this->imageSizebytes > $maximumBytes) {
        $this->error = "Sorry, your file is too large. and must be less than ".$maximumBytes;
        $this->uploadOk = false;
               }
        
        if($this->imageFileType != "jpg" && $this->imageFileType != "png" && $this->imageFileType != "jpeg" && $this->imageFileType != "gif" && $this->imageFileType != "GIF" && $this->imageFileType != "JPEG" && $this->imageFileType != "PNG" && $this->imageFileType != "JPG") {
        $this->error = "Sorry, only JPG, JPEG, PNG & GIF files are allowed and  this one is[".$this->imageFileType.']';
        //echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
        $this->uploadOk = false;     
         }
        
        if(move_uploaded_file($this->imageData,$temp)){
           $this->uploadOk = true;
           $this->newName = $tempxx;
        }
        return  $this->uploadOk;
  }
    function upload_EXE($locations,$maximumBytes,$EXTS){
        $cont = 0;
        $temp = $locations.md5($cont).'.'.$this->imageFileType;
        $tempxx = md5($cont).'.'.$this->imageFileType;
        $temp2 = $locations.'pipTemps2018101.'.$this->imageFileType;
        while(file_exists($temp)) {
               $cont++;
               $temp = $locations.md5($cont).'.'.$this->imageFileType;
               $tempxx = md5($cont).'.'.$this->imageFileType;
            }
        if($this->imageSizebytes > $maximumBytes) {
        $this->error = "Sorry, your file is too large. and must be less than ".$maximumBytes;
        $this->uploadOk = false;
               }
        
        for($xx=0;$xx<sizeof($EXTS);$xx++){
            if($this->imageFileType != $EXTS[$xx]){
                $this->uploadOk = false;
                $this->error = "Sorry, this file is not allowed";
            } else{
              $this->uploadOk = false;
              break;  
            } 
        }
        
        if(!$this->uploadOk){
           return  $this->uploadOk;
        } else if (move_uploaded_file($this->imageData,$temp)){
           $this->uploadOk = true;
           $this->newName = $tempxx;
           return $this->uploadOk;
        }
      
  }
}
function image($index){
    return new uploadImage($index);
}
?>