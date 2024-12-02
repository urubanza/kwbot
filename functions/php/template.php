<?php
    class  template {
        // a variable to keep the html doctype definition 
        public $doctype = "<!DOCTYPE html>";
        // a variable to keep url of header and footer javascript and css files 
        public $header_files;
        public $footer_files;
        public $body_files;
        // a variable to keep any string that can proceed the body for multiple body files
        private $after_body = [];
        // a variables that will keep all required string ( tags ) before any body files to be loaded
        private $before_body = [];
        // a variable the title of the page
        public $title = "";
        // a url of the icon of the page 
        public $icon = "";
        // a variable to keep the current directory of the template
        public $dir = "template";
        public $locs = "";
        private $parent_location = "";
        public $TAGS;
        private $body_attributes = "";
        private $variables = [];
        private $data = [];
        private $language = "en";
        public const TAGS_CAT = ["must",
                                 "form",
                                 "layout",
                                 "text",
                                 "list",
                                 "table",
                                 "media",
                                 "link",
                                 "external"];
        
        private $url_attributes = ["src","href"];
        
        private const pip_variable_key = "<pip::template::variables>";
        private const pip_variable_end = "</pip::template::variables>";
        
        private const pip_variable_key_s = "<pip::temp::var>";
        private const pip_variable_end_s = "</pip::temp::var>";
        
        private const pip_loop_key = "<pip::template::loops";
        private const pip_loop_end = "</pip::template::loops>";
        
        private const pip_loop_key_s = "<pip::temp::loop";
        private const pip_loop_end_s = "</pip::temp::loop>";
        
        private const pip_files_key = "<pip::template::files";
        private const pip_files_end = "</pip::template::files>";
        
        private const pip_files_key_s = "<pip::temp::file";
        private const pip_files_end_s = "</pip::temp::file>";
        
        private const pip_jsapi_key = "<pip::template::jsapi";
        private const pip_jsapi_end = "</pip::template::jsapi>";
        
        private const pip_jsapi_key_s = "<pip::temp::jsapi";
        private const pip_jsapi_end_s = "</pip::temp::jsapi>";
        
        private const pip_form_key = "<pip::template::form";
        private const pip_form_end = "</pip::template::form>";
        
        private const pip_form_key_s = "<pip::temp::form";
        private const pip_form_end_s = "</pip::temp::form>";
        
        private const dataStart = "{{{";
        private const dataEnds = "}}}";
        
        private const smallVarStart = '${';
        private const smallVarEndin = '}';
        
        
        private $document;
        
        private const locations_of_userfull_image = [
            "danger"=>"img/sys_files/danger.png",
            "loading"=>"img/sys_files/loading.gif",
            "noimage"=>"img/sys_files/noimage.png",
            "avatar"=>"img/sys_files/avatar",
            "flags"=>"img/sys_files/flags"
        ];
        function __construct($title = NULL, $icon = NULL){
            if($title==NULL){
                $this->title = "NOT :: TITLED";
            } 
            else {
                $this->title = $title;
            }
            $this->icon = $icon;
            $this->header_files = new PIP_Array([["name"=>$this->title]]);
            $this->footer_files = new PIP_Array([["name"=>$this->title]]);
            $this->body_files = new PIP_Array([["name"=>$this->title]]);
            $this->locs = "";
            $this->TAGS;
        }
        // a function to set the parent folder location 
        function locs($locs = ""){
            if(pipStr($locs)->length()){
                $this->parent_location = $locs;
                return $this;
            }
            return $this->parent_location;
        }
        // a function to set up a language 
        public function language($lang){
            $this->language = $lang;
        }
        // a function to add a url attribute to the template
        public function url_attr($attr){
            if(!contains_arr($this->url_attributes,$attr))
                array_push($this->url_attributes,$attr);
            return $this;
        }
        public function header($url, $pos = NULL){
          $lastNum = $this->header_files->width();
          if(!is_numeric($url)){
             $this->header_files = $this->header_files
                                            ->insertH(new PIP_Array([["url$lastNum"=>$url]]),$pos)
                                            ->filterthis_distinct();
             return $this;   
          } else {
              if(intval($url)>=$this->header_files->width()){
                  return $this->header_files->AV[0][$this->header_files->width()-1];
              } else return $this->header_files->AV[0][intval($url)];
          }
        }
        public function footer($url, $pos = NULL){
            
          if(!is_numeric($url)){
             $lastNum = $this->footer_files->width();
             $this->footer_files = $this->footer_files
                                            ->insertH(new PIP_Array([["url$lastNum"=>$url]]),$pos)
                                            ->filterthis_distinct();
             return $this;   
          } else {
              if(intval($url)>=$this->footer_files->width()){
                  return $this->footer_files->AV[0][$this->footer_files->width()-1];
              } else return $this->footer_files->AV[0][intval($url)];
          }
        }
        public function body($url, $pos = NULL){
          if(!is_numeric($url)){
              $lastNum = $this->body_files->width();
              $this->body_files = $this->body_files
                                        ->insertH(new PIP_Array([["url$lastNum"=>$url]]),$pos)
                                        ->filterthis_distinct();
              return $this;
          }
          else {
              if(intval($url)>=$this->body_files->width()){
                  return $this->body_files->AV[0][$this->body_files->width()-1];
              } else return $this->body_files->AV[0][intval($url)];
          }
        }
        public function abody($classes = "",$name = "",$attr = ""){
            $this->addBody($this->after_body,$classes,$name,$attr);
            return $this;
        }
        public function bbody($classes = "",$name = "", $attr = "",$starts = "",$ends = ""){
            $this->addBody($this->before_body,$classes,$name,$attr,$starts,$ends);
            return $this;
        }
        private static function addBody(&$var,$classes = "",$name = "", $attr = "",  $starts = "",$endx = ""){
            if(strlen($name)==0)
                $name = "div";
            $rets = "<$name ";
            
            if(strlen($classes)>0){
                $rets .= " class=\"$classes\" ";
            }
            
            if(is_array($attr)){
                for($ii=0;$ii<sizeof($attr);$ii++){
                    if(isset($attr[$ii]["name"])&&isset($attr[$ii]["val"])){
                        $rets = $rets.$attr[$ii]["name"]."=\"".$attr[$ii]["val"]."\" ";
                    }
                }
            }
            $rets = $rets." >";
            $ends = "</$name>";
            if($var instanceof PIP_Array)
                $var->_add_(["start"=>$rets,"ends"=>$ends, "final"=>$endx,"point"=>$starts]);
            else $var = pipArr([["start"=>$rets,"ends"=>$ends,"final"=>$endx,"point"=>$starts]]);
        }
        // a function designed to transform all variables into single text including multivariables
        public function multi_level_variables($str){
            if($str instanceof PIP_Str){
                return $this->multi_level_variables($str->str());
            }
            else if(is_numeric($str)){
                return $this->multi_level_variables("$str");
            }
            else if(!is_string($str)){
                throw new Exception("The variable to be used on remove pip variables must me a string, number or instance of PIP_Str");
                return "";
            }
            $str = pipStr($str);
            $between_text = $str->between(self::pip_variable_key,self::pip_variable_end);
            $current_level = $between_text->contains(self::pip_variable_key);
            if($current_level>0){
               $theFirstVar =  $str->between(self::pip_variable_key,self::pip_variable_end);
               $after_the = $str->between(self::pip_variable_end,self::pip_variable_key);
               $beforeChild = $str->between(self::pip_variable_key,self::pip_variable_key);
               
               return $beforeChild->str();
            }
            else {
                return $between_text->singleLine()->singleText()->str(); 
            }
        }
        // a function to remove all needed values and , variables, loops and more at the sametime
        public function removeAll($str){
            if(is_string($str)) return $this->removeAll(pipStr($str));
            else if($str instanceof PIP_Str){
                $url_before = pipArr([$this->url_attributes]);
                $untouchable = pipArr([["http://","https://","file:///","ws://","#"]]);
                $temp = pipStr();
                if($str->contains(self::pip_variable_key)){
                    // the initialization of the string to process by making a copy of the string to init_var
                    $init_vars = $str;
                    // saving the string between the end tag and closing tag
                    $the_first = $init_vars->between(self::pip_variable_key,self::pip_variable_end);
                    // checking the number of embedded variables inside a tag
                    $embed_level = $the_first->contains(self::pip_variable_key);
                    
                    $the_outside = $init_vars->after(self::pip_variable_end);
                    $toBe_added = "";
                    
                    
                    
                    for($ii=0;$ii<$embed_level;$ii++){
                        
                    }
                    
                    
                }
                if($str->after(self::pip_variable_key)->length()>0){
                    $the_btn = $str->between(self::pip_variable_key,self::pip_variable_end);
                    $embed_level = $the_btn->contains(self::pip_variable_key);
                }
                for($ii=0;$ii<$str->length();){
                    // check for the pip::template::variables
                    if($str->sub($ii,pipStr(self::pip_variable_key)->length())==self::pip_variable_key){
                       //$the_var = 
                    } 
                    // check for the pip::temp::var
                    else if($str->sub($ii,pipStr(self::pip_variable_key_s)->length())==self::pip_variable_key_s){
                        
                    }
                    // check for the pip::template::loops
                    else if($str->sub($ii,pipStr(self::pip_loop_key)->length())==self::pip_loop_key){
                        
                    }
                    // check for the pip::temp::loop
                    else if($str->sub($ii,pipStr(self::pip_loop_key_s)->length())==self::pip_loop_key_s){
                        
                    }
                    // check for the pip::template::files
                    else if($str->sub($ii,pipStr(self::pip_files_key)->length())==self::pip_files_key){
                        
                    }
                    // check for the pip::temp::file
                    else if($str->sub($ii,pipStr(self::pip_files_key_s)->length())==self::pip_files_key_s){
                        
                    }
                    // check for the pip::template::jsapi
                    else if($str->sub($ii,pipStr(self::pip_jsapi_key)->length())==self::pip_jsapi_key){
                        
                    }
                    // check for the pip::temp::jsapi
                    else if($str->sub($ii,pipStr(self::pip_jsapi_key_s)->length())==self::pip_jsapi_key_s){
                        
                    }
                    // check for the pip::template::form
                    else if($str->sub($ii,pipStr(self::pip_form_key)->length())==self::pip_form_key){
                        
                    }
                    // check for the pip::temp::form
                    else if($str->sub($ii,pipStr(self::pip_form_key_s)->length())==self::pip_form_key_s){
                        
                    } 
                    // check for the ${
                    else if($str->sub($ii,pipStr(self::smallVarStart)->length())==self::smallVarStart){
                        
                    }
                    else {
                        $temp->add($str->sub($ii));
                        $ii++;
                    }
                }
            } 
            else {
                throw new Exception("The variable to be used on remove pip variables must me a string or instance of PIP_Str");
                return "";
            }
        }
        public function render(){
            $rets = "";
            for($ii=1;$ii<$this->header_files->width();$ii++){
                 if(is_file($this->locs.$this->dir."/".$this->header($ii))){
                     $fp = fopen($this->locs.$this->dir."/".$this->header($ii),'r');
                     $all = fgets($fp);
                     while(!feof($fp)){
                        $all = $all.fgets($fp); 
                     }
                     $rets .= $this->realUrl($rets.$all,$this->locs.$this->dir."/",0);
                     fclose($fp);
                 } else {
                     $rets = template::danger("<h2>".$this->sys_images()." template file ".$this->locs.$this->dir."/".$this->header($ii)." not found!!<h2>");
                 }
                 
            }
            $component_Css = '<link rel="stylesheet" href="'.$this->locs().'functions/layout/components.css">
                              <script src="'.$this->locs().'functions/SOP/js/config.js"></script>
                              <script src="'.$this->locs().'functions/js/functions.js"></script>';
            $rets = "\n\t<head >".$rets.$component_Css."</head>";
            $new_rets = "";
            if($this->after_body instanceof PIP_Array)
                for($ii=0;$ii<$this->after_body->height();$ii++){
                    $new_rets = $new_rets.$this->after_body->JS($ii)->start;
                }
            for($ii=1;$ii<$this->body_files->width();$ii++){
                 if(is_file($this->locs.$this->dir."/".$this->body($ii))){
                     $fp = fopen($this->locs.$this->dir."/".$this->body($ii),'r');
                     $all = fgets($fp);
                     while(!feof($fp)){
                        $all = $all.fgets($fp); 
                     }
                     $new_rets .= $this->realUrl($new_rets.$all,$this->locs.$this->dir."/",1);
                     fclose($fp);
                 } else {
                     $new_rets = template::danger("<h2>".$this->sys_images()."template file ".$this->locs.$this->dir."/".$this->body($ii)." not found!!</h2>");
                 }  
            }
            
            $rets = $rets."\n\t<body ".$this->body_attributes." >".$new_rets;
            $new_rets = "";
            if($this->after_body instanceof PIP_Array)
                for($ii=0;$ii<$this->after_body->height();$ii++){
                    $new_rets = $new_rets.$this->after_body->JS($ii)->ends;
                }
            for($ii=1;$ii<$this->footer_files->width();$ii++){
                if(is_file($this->locs.$this->dir."/".$this->footer($ii))){
                     $fp = fopen($this->locs.$this->dir."/".$this->footer($ii),'r');
                     $all = fgets($fp);
                     while(!feof($fp)){
                        $all = $all.fgets($fp); 
                     }
                     
                     $new_rets .= $this->realUrl($new_rets.$all,$this->locs.$this->dir."/",2);
                    if($this->before_body instanceof PIP_Array)
                         if($this->before_body->size){
                             if($this->before_body->_gets_("final",$ii)->size){
                                 $new_rets = $new_rets.$this->before_body->_gets_("final",$ii)->JS()->ends;
                             } else if($this->before_body->_gets_("point",$ii)->size){
                                 $new_rets = $this->before_body->_gets_("final",$ii)->JS()->start.$new_rets;
                             }   
                         }
                     fclose($fp); 
                } else {
                    $new_rets = template::danger("<h2>".$this->sys_images()."template file ".$this->locs.$this->dir."/".$this->footer($ii)." not found!!</h2>");
                }  
            }
            $rets = $this->doctype."\n".'<html lang="'.$this->language.'">'.$rets.$new_rets."</body>\n</html>";
            $this->document = $rets;
            $this->removeVariables();
            $this->removeLoops();
            return $this;
        }
        public function renderAjax(){
            $rets = "";
            for($ii=1;$ii<$this->header_files->width();$ii++){
                 if(is_file($this->locs.$this->dir."/".$this->header($ii))){
                     $fp = fopen($this->locs.$this->dir."/".$this->header($ii),'r');
                     $all = fgets($fp);
                     while(!feof($fp)){
                        $all = $all.fgets($fp); 
                     }
                     $rets .= $this->realUrl($rets.$all,$this->locs.$this->dir."/",3);
                     fclose($fp);
                 } else {
                     $rets = template::danger("<h2>".$this->sys_images()."template file ".$this->locs.$this->dir."/".$this->header($ii)." not found!!</h2>");
                 } 
            }
            $new_rets = "";
            for($ii=1;$ii<$this->body_files->width();$ii++){
                 if(is_file($this->locs.$this->dir."/".$this->body($ii))){
                     $fp = fopen($this->locs.$this->dir."/".$this->body($ii),'r');
                     $all = fgets($fp);
                     while(!feof($fp)){
                        $all = $all.fgets($fp);
                     }
                     $new_rets .= $this->realUrl($new_rets.$all,$this->locs.$this->dir."/",3);
                     fclose($fp);
                 } else {
                     $new_rets = template::danger("<h2>".$this->sys_images()."template file ".$this->locs.$this->dir."/".$this->body($ii)." not found!!</h2>");
                 }  
            }
            $rets = $rets.$new_rets;
            $new_rets = "";
            for($ii=1;$ii<$this->footer_files->width();$ii++){
                if(is_file($this->locs.$this->dir."/".$this->footer($ii))){
                     $fp = fopen($this->locs.$this->dir."/".$this->footer($ii),'r');
                     $all = fgets($fp);
                     while(!feof($fp)){
                        $all = $all.fgets($fp); 
                     }
                     $new_rets .= $this->realUrl($new_rets.$all,$this->locs.$this->dir."/",3);
                     fclose($fp); 
                } else {
                    $new_rets = template::danger("<h2>".$this->sys_images()."template file ".$this->locs.$this->dir."/".$this->footer($ii)." not found!!</h2>");
                }  
            }
            $this->document = $rets.$new_rets;
            $this->removeVariables();
            $this->removeLoops();
            return $this;
        }
        public function variables($index,$value = ""){
            if(is_array($index)){
                return $this->var($index);
            }
            $this->variables[$index] = $value;
            return $this;
        }
        public function var($index,$value = ""){
            if(!is_array($index))
                return $this->variables($index,$value);
            else $this->variables = $index;
            return $this;
        }
        public function load($file_path){
            $rets = "";
            if(is_file($this->locs.$this->dir."/".$file_path)){
                 $fp = fopen($this->locs.$this->dir."/".$file_path,'r');
                 $all = fgets($fp);
                 while(!feof($fp)){
                    $all = $all.fgets($fp); 
                 }
                 $rets .= $this->realUrl($rets.$all,$this->locs.$this->dir."/",1);
                 fclose($fp);
             } 
             else {
                 $rets = template::danger("the file (".$this->locs.$this->dir."/".$file_path.") you are looking for was not found");
             }
            return $rets;
        }
        
        // the function to open a static file and return the content as a string
        public static function file($path){
            $rets = "";
            if(is_file($path)){
                 $fp = fopen($path,'r');
                 $all = fgets($fp);
                 while(!feof($fp)){
                    $all = $all.fgets($fp); 
                 }
                 $rets .= $all;
                 fclose($fp);
             } 
             else {
                 $rets = template::danger("failed to open the file $path no such file or directory");
             }
            return $rets;
        }
        public function data($index,$value){
            if($value instanceof PIP_Array){
                $this->data[$index] = $value;
            }
            return $this;
        }
        public function echo(){
            echo $this->document;
        }
        public function pfolder($folder){
            $this->dir = $folder."/$this->dir";
            return $this;
        }
        public function folder($folder){
            $this->dir = $folder;
            return $this;
        }
        // a function to add real url in the file loaded
        // $str is the string to be used in removing and inserting new real url
        // $locs is the location of the template and $type is the number specifying if is the header(0), body(1), footer(2) and nothing(3)
        public function realUrl($str,$locs, $type = 3){
            $url_before = pipArr([$this->url_attributes]);
            $untouchable = pipArr([["http://","https://","file:///","#"]]);
            $results = array();
            $bounds = [
                "start"=>pipStr(),
                "end"=>pipStr()
            ];
            
            $temp = pipStr();
                
            if(is_string($str)){
                return $this->realUrl(pipStr($str),$locs,$type);
            } 
            else {
                switch($type){
                    case 0:{
                       $bounds["start"] = pipStr("<head");
                       $bounds["end"] = pipStr("</head>");
                       break;
                    }
                    case 1:{
                       $bounds["start"] = pipStr("<body");
                       $bounds["end"] = pipStr("</body>");
                       break;
                    }
                    case 2:{
                        $bounds["start"] = pipStr("<footer");
                        $bounds["end"] = pipStr("</footer>");
                        break;
                    }
                    case 3:{
                        //echo '$str->str()';
                        break;
                    }
                    default:{
                        return $this->realUrl($str,$locs,3);
                    }
                }
                $found = false;
                for($ii=0;$ii<$url_before->width();$ii++){
                    if(strlen(strchr($str->str(),$url_before->AV[0][$ii]))) $found = true;
                }
                if(!$found){
                    $rets = [
                        "start"=>false,
                        "conts"=>pipStr(),
                        "addition"=>pipStr()
                    ];
                    if($bounds["start"]->length()==0) {return $str->str(); echo "....";}
                    for($ii=0;$ii<$str->length();$ii++){
                        if($rets["start"]){
                            if(($str->sub($ii,$bounds["end"]->length())==$bounds["end"]->str())&&($bounds["start"]->length()>0)){
                                break;
                            }
                            $rets["conts"]->add($str->sub($ii));
                        } else {
                            if($str->sub($ii,$bounds["start"]->length())==$bounds["start"]->str()){
                                $rets["start"] = true;
                                for($i=$ii+$bounds["start"]->length();$i<$str->length();$i++){
                                  if($str->sub($i)==">")
                                      break;
                                  $rets["addition"]->add($str->sub($i));
                                }
                                $ii = $i;
                            } else if($str->sub($ii,$bounds["end"]->length())==$bounds["end"]->str()){
                                break;
                            }
                        }
                    }
                    $this->body_attributes = $this->body_attributes." ".$rets["addition"]->str();
                    return $rets["conts"]->str();
                }
            }
            $start = false;
            $additional = pipStr();
            if($bounds["start"]->length()==0) $start = true; 
            for($ii=0;$ii<$str->length();$ii++){
                if($start){
                    if($str->sub($ii,$bounds["end"]->length())==$bounds["end"]->str()){
                        if($type==3) $temp->add($str->str());
                        break;
                    }
                    $temp->add($str->sub($ii));
                    for($iii=0;$iii<$url_before->width();$iii++){
                        $url_before_str = pipStr($url_before->AV[0][$iii]);
                        if(($str->sub($ii,$url_before_str->length())==$url_before->AV[0][$iii])
                          &&($str->sub($ii+$url_before_str->length(),1)=="=")){
                            $url = [
                                "start"=>false,
                                "contents"=>pipStr()
                            ];
                            $local = false;
                            $url_b_length = 0;
                            for($i=$ii;$i<$str->length();$i++){
                                $url_b_length++;
                                if(($str->sub($i,1)=='"')&&(!$url["start"])){
                                    $url["start"] = true;
                                } else if(($url["start"])&&($str->sub($i,1)=='"')){
                                    $url["start"] = false;
                                    break;
                                } else if($url["start"]){
                                    $url["contents"] = $url["contents"]->add($str->sub($i,1));
                                }
                            }
                            if(!$url["contents"]->contains($untouchable)){
                                $local = true;
                                $url["contents"] = $url["contents"]->add($locs,1);
                            }
                            $res = [
                                "value"=>$url["contents"]->str(),
                                "attribute"=>$url_before->AV[0][$iii],
                                "position"=>$ii-1,
                                "length"=>$url_b_length,
                                "type"=>$local,
                                "original"=>$str->sub($ii,$url_b_length),
                                "temp_pos"=>$temp->length()-1
                            ];
                            array_push($results,$res);

                        }  
                    }
                }
                else {
                    if($str->sub($ii,$bounds["start"]->length())==$bounds["start"]->str()){
                        $start = true;
                        for($i=$ii+$bounds["start"]->length();$i<$str->length();$i++){
                          if($str->sub($i)==">")
                              break;
                          $additional->add($str->sub($i));
                        }
                        $ii = $i;
                    } else if($str->sub($ii,$bounds["end"]->length())==$bounds["end"]->str()){
                        break;
                    }
                }
            }
            $this->body_attributes = $this->body_attributes." ".$additional->str();
            $results = pipArr($results);
            $before = pipStr();
            
            if($bounds["start"]->length()==0) return $temp->str();
            
            for($ii=0;$ii<$results->height();$ii++){
                $puts = " ".$results->JS($ii)->attribute.'="'.$results->JS($ii)->value.'" ';
                if($ii==0){
                   $before->add($temp->sub(0,$results->JS($ii)->temp_pos))->add($puts);
                }
                else if($ii==$results->height()-1){
                    $before->add($temp->sub($results->JS("l")->temp_pos,$temp->length()));
                } 
                else {
                   $pev_pos = $results->JS($ii-1)->temp_pos;// + $results->sum_dec("length",$ii-1);
                   $stop_pos = $results->JS($ii)->temp_pos - $pev_pos;
                   $before->add($temp->sub($pev_pos,$stop_pos))->add($puts);  
                }
            }
            //echo $before->str();
            for($ii=0;$ii<$results->height();$ii++){
                if($results->JS($ii)->type)
                    $before = $before->remove($results->JS($ii)->original,
                                              $results->JS($ii)->position+$results->JS($ii)->length);
            }
            return $before->str();
        }
        // a function to write how the template was
        public function writeTags(){
            $fp;
            if(is_dir($this->locs.$this->dir."/reports")){
                if(!is_dir($this->locs.$this->dir."/reports/htmltags")){
                    mkdir($this->locs.$this->dir."/reports/htmltags");
                }
            } else {
                mkdir($this->locs.$this->dir."/reports");
                mkdir($this->locs.$this->dir."/reports/htmltags");
            }
            if (!$fp = @fopen($this->locs.$this->dir."/reports/htmltags/stat.html",'w')) {
               throw new Exception('could not open the file.');
               return false;
            }
            $rets = "";
            $the_document = pipStr($this->document);
            for($ii=0;$ii<$the_document->length();$ii++){
                for($iii=0;$iii<sizeof(template::TAGS_CAT);$iii++){
                    for($i=0;$i<sizeof($this->TAGS);$i++){
                        for($j=0;$j<sizeof($this->TAGS[template::TAGS_CAT[$iii]]);$j++){
                            //echo "<pre>".print_r($this->TAGS[template::TAGS_CAT[$iii]][$j]["name"])."</pre>";
                            if(!is_array($this->TAGS[template::TAGS_CAT[$iii]][$j]["name"])){
                                if($this->TAGS[template::TAGS_CAT[$iii]][$j]["name"]==
                                  $the_document->sub($ii,pipStr($this->TAGS[template::TAGS_CAT[$iii]][$j]["name"])->length())){
                                    $rets .= "<".$this->TAGS[template::TAGS_CAT[$iii]][$j]["name"].">";
                                }
                            } else {
                                for($jj=0;$jj<sizeof($this->TAGS[template::TAGS_CAT[$iii]][$j]["name"]);$jj++){
                                    if($this->TAGS[template::TAGS_CAT[$iii]][$j]["name"][$jj]==
                                      $the_document->sub($ii,pipStr($this->TAGS[template::TAGS_CAT[$iii]][$j]["name"][$jj])->length())){
                                          $rets .= "<".$this->TAGS[template::TAGS_CAT[$iii]][$j]["name"][$jj].">";

                                      }
                                }
                            }
                        }
                    }
                }
            }
//            if(!@fwrite($fp,$rets)){
//               throw new Exception('could not write to the file.');
//               return false;
//            } else {
//               return true;
//            }
            fclose($fp);
        }
        // a function to remove all pipTemplate variable and replace them with their values
        private function removeVariables(){
            $new = pipStr();
            $the_str = pipStr($this->document);
            $the_key_open = pipStr(template::pip_variable_key);
            $the_key_close = pipStr(template::pip_variable_end);
            for($ii=0;$ii<$the_str->length();$ii++){
                if($the_str->sub($ii,$the_key_open->length())==$the_key_open->str()){
                    $variable = "";
                    $counter = 0;
                    for($i=$ii+$the_key_open->length();$i<$the_str->length();$i++){
                        $counter++;
                        if($the_str->sub($i,$the_key_close->length())==$the_key_close->str())
                            break;
                        else if($the_str->sub($i)==" "){
                            continue;
                        } else {
                            $variable .= $the_str->sub($i);
                        }
                    }
                    $variable = pipStr($variable)->singleLine()->singleText()->str();
                    if(isset($this->variables[$variable])){
                        $new->add($this->variables[$variable]);
                    } else {
                        $new->add(template::danger(
                            $this->sys_images()." $variable variable not found"));
                    }
                    $ii = $i+$the_key_close->length()-1;
                } else {
                    $new->add($the_str->sub($ii));
                }
            }
            $this->document = $new->str();
        }
        //a function to load a sys_image
        private function sys_images($name = "danger",$size = 30){
            if(!isset(template::locations_of_userfull_image[$name])) $name = "danger";
            return template::image($this->locs().template::locations_of_userfull_image[$name],"",[template::attr("style","width:".$size."px")]);
        }
        // a function to remove all pipTemplate loop variables to be used
        private function removeLoops($str = 0, $parents = ""){
            $new = pipStr();
            $the_str = "";
            if(is_string($str)) $the_str = pipStr($str);
            else $the_str = pipStr($this->document);
            
            $the_key_open = pipStr(template::pip_loop_key);
            $the_key_close = pipStr(template::pip_loop_end);
            
            $processed = pipStr(strchr($the_str->str(),$the_key_open->str()));
            $params = [];
            $insiders = [];
            $outsiders = [];
            $child_founds = 0;
            while($processed->length()>20){
               $parameters = [
                   "name"=>pipStr(),
                   "size"=>pipStr(),
                   "start"=>pipStr(),
                   "order"=>pipStr(),
                   "condition"=>pipStr(),
                   "value"=>pipStr(),
                   "sign"=>pipStr(),
                   "childs"=>false
               ];
               $insideStrings = pipStr();
               $outsideStrings = pipStr();
               $last_index = 0;
               $variables_start = true;
               $subloops = 0;
               for($ii=21;$ii<$processed->length();$ii++){
                   if($variables_start){
                          $outsideStrings->add($processed->sub($ii));
                          if($processed->sub($ii,strlen('pip:name="'))=='pip:name="'){
                              for($i=$ii+strlen('pip:name="');$i<$processed->length();$i++){
                                  if($processed->sub($i)=='"')
                                      break;
                                  else {
                                      if(!($parameters["name"] instanceof PIP_str))
                                          $parameters["name"] = pipStr($parameters["name"]);
                                      $parameters["name"]->add($processed->sub($i));
                                      
                                  }
                              }
                          } 
                          else if($processed->sub($ii,strlen('pip:size="'))=='pip:size="'){
                              for($i=$ii+strlen('pip:size="');$i<$processed->length();$i++){
                                  if($processed->sub($i)=='"')
                                      break;
                                  else {
                                      if(!($parameters["size"] instanceof PIP_str))
                                          $parameters["size"] = pipStr($parameters["size"]);
                                      $parameters["size"]->add($processed->sub($i));    
                                  }
                              }
                              
                          } 
                          else if($processed->sub($ii,strlen('pip:start="'))=='pip:start="'){
                              for($i=$ii+strlen('pip:start="');$i<$processed->length();$i++){
                                  if($processed->sub($i)=='"')
                                      break;
                                  else {
                                      if(!($parameters["start"] instanceof PIP_str))
                                          $parameters["start"] = pipStr($parameters["start"]);
                                      $parameters["start"]->add($processed->sub($i));
                                  }
                              }
                          } 
                          else if($processed->sub($ii,strlen('pip:order="'))=='pip:order="'){
                              for($i=$ii+strlen('pip:order="');$i<$processed->length();$i++){
                                  if($processed->sub($i)=='"')
                                      break;
                                  else {
                                      if(!($parameters["order"] instanceof PIP_str))
                                          $parameters["order"] = pipStr($parameters["order"]);
                                      $parameters["order"]->add($processed->sub($i));
                                  }
                              }
                              
                          } 
                          else if($processed->sub($ii,strlen('pip:condition="'))=='pip:condition="'){
                              for($i=$ii+strlen('pip:condition="');$i<$processed->length();$i++){
                                  if($processed->sub($i)=='"')
                                      break;
                                  else {
                                      if(!($parameters["condition"] instanceof PIP_str))
                                          $parameters["condition"] = pipStr($parameters["condition"]);
                                  }$parameters["condition"]->add($processed->sub($i));
                              }
                          } 
                          else if($processed->sub($ii,strlen('pip:value="'))=='pip:value="'){
                              for($i=$ii+strlen('pip:value="');$i<$processed->length();$i++){
                                  if($processed->sub($i)=='"')
                                      break;
                                  else {
                                      if(!($parameters["value"] instanceof PIP_str))
                                          $parameters["value"] = pipStr($parameters["value"]);
                                      $parameters["value"]->add($processed->sub($i));
                                  }
                              }
                          } 
                          else if($processed->sub($ii,strlen('pip:sign="'))=='pip:sign="'){
                              for($i=$ii+strlen('pip:sign="');$i<$processed->length();$i++){
                                  if($processed->sub($i)=='"')
                                      break;
                                  else {
                                      if(!($parameters["sign"] instanceof PIP_str))
                                          $parameters["sign"] = pipStr($parameters["sign"]);
                                      $parameters["sign"]->add($processed->sub($i));
                                  }
                              }
                          }
                          else if($processed->sub($ii)==">"){
                              $variables_start = false;
                              $ii++;
                          }
                   }
                   else if($processed->sub($ii,$the_key_open->length())==$the_key_open->str()){
                       $subloops = $subloops + 1;
                       $child_founds++;
                       $parameters["childs"] = true;
                   }
                   else if($processed->sub($ii,$the_key_close->length())==$the_key_close->str()){
                       $subloops = $subloops - 1;
                       if($subloops<0){
                            $last_index = $ii + $the_key_close->length();
                            break;
                       }
                   }
                   if(!$variables_start) $insideStrings->add($processed->sub($ii));
                   
                   if($parameters["name"] instanceof PIP_str) $parameters["name"] = $parameters["name"]->str();
                   if($parameters["size"] instanceof PIP_str) $parameters["size"] = $parameters["size"]->str();
                   if($parameters["start"] instanceof PIP_str) $parameters["start"] = $parameters["start"]->str();
                   if($parameters["order"] instanceof PIP_str) $parameters["order"] = $parameters["order"]->str();
                   if($parameters["condition"] instanceof PIP_str) $parameters["condition"] = $parameters["condition"]->str();
                   if($parameters["value"] instanceof PIP_str) $parameters["value"] = $parameters["value"]->str();
                   if($parameters["sign"] instanceof PIP_str) $parameters["sign"] = $parameters["sign"]->str();
               }
                
                
               $processed->init($processed->sub($last_index,$processed->length()));
               $processed->init(strchr($processed->str(),$the_key_open->str())); 
               array_push($params,$parameters);
               array_push($insiders,$insideStrings->str());
               array_push($outsiders,$outsideStrings->str());
            }
                $fields_to_be_removed = "the pip array to keep all variable table field couple";
                
                for($ii=0;$ii<sizeof($insiders);$ii++){
                    if(!$params[$ii]["childs"]){
                        $new_insider = pipStr(strchr($insiders[$ii],template::dataStart));
                        $variables_data = [];
                        while($new_insider->length()>0){
                            $variable_d = pipStr();
                            $new_pos = 0;
                            for($i=3;$i<$new_insider->length();$i++){
                                if($new_insider->sub($i,3)=="}}}"){
                                    $new_pos = $i+3;
                                    break;
                                }  
                                else $variable_d->add($new_insider->sub($i));
                            }
                            $new_insider->init($new_insider->sub($new_pos,$new_insider->length()));
                            $new_insider->init(strchr($new_insider->str(),template::dataStart));
                            $variables_data = [
                                "table"=>$params[$ii]["name"],
                                "fields"=>$variable_d->str(),
                                "original"=>$variable_d->str()
                            ];
                            if(strpos($variable_d->str(),":")){
                                $variables_data["table"] = $variable_d->sub(0,strpos($variable_d->str(),":"));
                                $variables_data["fields"] = $variable_d->sub(strpos($variable_d->str(),":")+1,$variable_d->length());
                            }
                            if($fields_to_be_removed instanceof PIP_Array)
                                $fields_to_be_removed->_add_($variables_data);
                            else $fields_to_be_removed = pipArr([$variables_data]);
                        }
                        $looped_insiders = "";
                        for($i=0;$i<$fields_to_be_removed->height();$i++){
                            if(isset($this->data[$fields_to_be_removed->JS($i)->table])){
                                if($this->data[$fields_to_be_removed->JS($i)->table]->index($fields_to_be_removed->JS($i)->fields)){
                                    $new_ins = $insiders[$ii];
                                    for($iii=0;$iii<$this->data[$fields_to_be_removed->JS($i)->table]->height();$iii++){
                                        $new_ins = str_replace("{{{".$fields_to_be_removed->JS($i)->original."}}}",
                                                               $this->data[$fields_to_be_removed->JS($i)->table]->AV[$iii][$fields_to_be_removed->JS($i)->fields],
                                                               $new_ins);
                                    }
                                    $looped_insiders = $looped_insiders.$new_ins;
                                } else {
                                    $new_ins = $insiders[$ii];
                                    $new_ins = str_replace("{{{".$fields_to_be_removed->JS($i)->original."}}}",
                                                           template::danger($fields_to_be_removed->JS($i)->fields),
                                                           $new_ins);
                                    $looped_insiders = $looped_insiders.$new_ins;
                                }
                            } else {
                                   $new_ins = $insiders[$ii];
                                   for($iii=0;$iii<$fields_to_be_removed->height();$iii++){ 
                                    $new_ins = str_replace("{{{".$fields_to_be_removed->JS($iii)->original."}}}",
                                                           template::danger($fields_to_be_removed->JS($i)->table),
                                                           $new_ins);
                                   }
                                   $looped_insiders = $looped_insiders.$new_ins;
                            }
                        }
                        echo $looped_insiders;
                        //echo $outsiders[$ii];
                    } else {
                        //echo $insiders[$ii];  
                    }
                }
                
                
            
            
            
            if(is_string($str)&&strlen($parents)){
                return [
                        "params"=>$params,
                        "insiders"=>$insiders,
                        "parent"=>$parents,
                        "founds"=>$child_founds
                ];
            }
            else return $new->str();
        }
        // a function to redirect the page the location of redirection and the ajax for generating JavaScript only.
        public static function redirect($locs,$ajax=0,$milleseconds = 0){
            if($ajax){
                echo '<script>
                        setTimeout( function(){
                           document.location ="'.$locs.'";
                        },'.$milleseconds.');
                      </script>';
            } else {
                echo '<!DOCTYPE html>
                       <html lang="en">
                        <head>
                          <meta charset="utf-8">
                          <meta name="viewport" content="width=device-width, initial-scale=1">
                          <title>Redirecting...</title>
                        </head>
                        <body>
                            <script>
                                setTimeout( function(){
                                     document.location ="'.$locs.'";
                                },'.$milleseconds.');
                            </script>
                        </body>
                     <html>';
            }
            
            
        }
        // an alternative function to the redirect but with simplified name
        public static function goto($locs,$ajax=0,$milleseconds = 0){
            //echo root($folder).$locs;
            return template::redirect($locs,$ajax,$milleseconds);
        }
        // a function to refresh a page 
        public static function refresh($ajax=0,$milleseconds = 0){
            return template::redirect("",$ajax,$milleseconds);
        }
        // a function to surround the a text with a danger styling
        public static function danger($message){
            return '<span style="color:red">'.$message."</span>";
        }
        // a function to generate an image 
        public static function image($url,$classes = "",$attr = ""){
            $rets = "<img src=\"$url\" ";
            if(strlen($classes)>0){
                $rets .= " class=\"$classes\" ";
            }
            if(is_array($attr)){
                for($ii=0;$ii<sizeof($attr);$ii++){
                    if(isset($attr[$ii]["name"])&&isset($attr[$ii]["val"])){
                        $rets = $rets.$attr[$ii]["name"]."=\"".$attr[$ii]["val"]."\" ";
                    }
                }
            }
            $rets = $rets." >";
            return $rets;
        }
        // a function to generate a link with specific location
        public static function link($url,$text,$icon="",$classes = "", $attr = ""){
            $rets = "<a href=\"$url\" ";
            if(strlen($classes)>0){
                $rets .= " class=\"$classes\" ";
            }
            
            if(is_array($attr)){
                for($ii=0;$ii<sizeof($attr);$ii++){
                    if(isset($attr[$ii]["name"])&&isset($attr[$ii]["val"])){
                        $rets = $rets.$attr[$ii]["name"]."=\"".$attr[$ii]["val"]."\" ";
                    }
                }
            }
            $rets = $rets." >";
            if(strlen($icon)>0){
               $rets = $rets." <i class=\"$icon\"></i> "; 
            }
            $rets = $rets." ".$text." </a>";
            return $rets;
        }
        // the same function as the above but with left 
        public static function llink($url,$text,$icon="",$classes = "", $attr = ""){
            $rets = "<a href=\"$url\" ";
            if(strlen($classes)>0){
                $rets .= " class=\"$classes\" ";
            }
            
            if(is_array($attr)){
                for($ii=0;$ii<sizeof($attr);$ii++){
                    if(isset($attr[$ii]["name"])&&isset($attr[$ii]["val"])){
                        $rets = $rets.$attr[$ii]["name"]."=\"".$attr[$ii]["val"]."\" ";
                    }
                }
            }
            $rets = $rets." >".$text;
            if(strlen($icon)>0){
               $rets = $rets." <i class=\"$icon\"></i> "; 
            }
            $rets = $rets." </a>";
            return $rets;
        }
        public static function pleftIcon($text,$icon = "",$classes="",$attr = []){
            $rets = "<p ";
            if(strlen($classes)>0){
                $rets .= " class=\"$classes\" ";
            }
            
            if(is_array($attr)){
                for($ii=0;$ii<sizeof($attr);$ii++){
                    if(isset($attr[$ii]["name"])&&isset($attr[$ii]["val"])){
                        $rets = $rets.$attr[$ii]["name"]."=\"".$attr[$ii]["val"]."\" ";
                    }
                }
            }
            $rets = $rets." >";
            $rets = $rets." ".$text." ";
            if(strlen($icon)>0){
               $rets = $rets."<i class=\"$icon\"></i>"; 
            }
            $rets = $rets." </p>";
            return $rets; 
        }
        // a function to produce a bagde
        public static function badge($text,$class){
            return "<span class=\"$class\">$text</span>";
        }
        // a function to produce a pipdate 
        public static function pipDate($data,$id,$class = "",$element = "xxx",$number = "yyy", $fun = "ego"){
            $rets = "<span id=\"$id\" date-data=\"$data\" date-element=\"$element\" class=\"$class\" date-number=\"$number\"></span>";
            $rets = $rets.'<script>
                                PIP_DATE("#'.$id.'").'.$fun.'();
                           </script>';
            return $rets;
        }
        
        public static function _pipDate_($data,$fun = "ego",$class = "",$element = "xxx",$number = "yyy"){
            return self::pipDate($data,PIP_Str::random(10),$class,$element,$number,$fun);
        }
        // a function to produce a continue button with functionality
        public static function continueButton($data_id,$file,$css_selector,$buttonName,$POSTname){
            return  
            '<button type="button" id="CONTINUE_REMOVES_'.pipStr($buttonName)->singleText()->str().md5($data_id).'" 
                     data-id="'.md5($data_id).$data_id.'" 
                     class="btn btn-secondary btn-flat">
                     Continue
            </button>
            <script>
                    $("#CONTINUE_REMOVES_'.pipStr($buttonName)->singleText()->str().md5($data_id).'")
                        .off("click")
                        .click( function(bn){
                        bn.preventDefault();
                        $("'.$css_selector.'").html(LOADING_S_);
                        var data_id = $(this).attr("data-id");
                        $.post("'.$file.'",
                               "'.$POSTname.'="+data_id.substring('.strlen(md5($data_id)).',data_id.length),
                               function(retsxx){
                            $("'.$css_selector.'").html(retsxx);
                        })
                    })
            </script>';
        }
        // a function to reduce the above one
        public static function btnContinue($data_id,$POSTname,$css_selector,$file = "index.php"){
            return self::continueButton($data_id,$file,$css_selector,PIP_Str::random(15),$POSTname);
        }
        // a function to the refresh btn with more data
        public static function btnContinueData($css_selector,$data = "",$file = "index.php"){
            $buttonName = PIP_str::random(20);
            $retsrets = PIP_str::random(20);
            
            return  
            '<button type="button" id="'.$buttonName.'"
                     class="btn btn-secondary btn-flat">
                     Continue
            </button>
            <script>
                    $("#'.$buttonName.'")
                        .off("click")
                        .click( function(bn){
                        bn.preventDefault();
                        $("'.$css_selector.'").html(LOADING_S_);
                        $.post("'.$file.'",
                               "'.$data.'",
                               function('.$retsrets.'){
                                $("'.$css_selector.'").html('.$retsrets.');
                        })
                    })
            </script>';
        }
        // a function to produce the refresh page btn with functionality
        public static function refreshBtn($name,$type,$locs = ""){
            return '<button type="button" id="'.$name.'_BUTTON_CONT" class="btn btn-'.$type.' btn-flat"> O.k </button>
                      <script>
                         $("#'.$name.'_BUTTON_CONT").click( function(bn){
                             bn.preventDefault();
                             document.location = "'.$locs.'";
                         });  
                      </script>';
        }
        // a function with the as the above but with random generated string
        public static function btnRefresh($locs = "", $class = "info"){
            $theName = PIP_Str::random(10);
            return template::refreshBtn($theName,$class,$locs);
        }
        //a function to produce refresh area javascript 
        // the $file (file to be called)
        // $htmlEle html element to put results
        // $parameter POST parameter 
        // $modal if there is a modal to close
        // $afterTime and a time to refresh the element
        // $jSfun javascript fun to call
        public static function AjaxRefresh($file,$htmlEL,$afterTime = 0,$parameter = "", $modal = "",$jSfun = ""){
            $modClose = "";
            if(pipStr($modal)->length()>0){
                $modClose = '$("'.$modal.'").modal("hide");';
            }
            return '<script>
                        setTimeout(function(){
                            '.$modClose.'
                            $("'.$htmlEL.'").html(LOADING_S_);
                            $.post("'.$file.'",
                                   "'.$parameter.'",
                                   function(rets){
                                        $("'.$htmlEL.'").html(rets);
                                        '.$jSfun.'
                            })
                        },'.$afterTime.');
                    </script>';
        }
        // a function to simplify the one above
        public static function AjaxRefr($htmlEl,$parameter = "", $afterTime = 0,$modal = "",$jSfun = "",$file = "index.php"){
            return self::AjaxRefresh($file,$htmlEl,$afterTime,$parameter,$modal,$jSfun);
        }
        
        public static function AjaxData($name,$values){
            if(is_array($name)&&is_array($values)){
                $rets = "";
                for($ii=0;$ii<sizeof($name);$ii++){
                    if(isset($values[$ii])){
                        if($ii) $rets .= "&";  
                        $rets .= template::AjaxData($name[$ii],$values[$ii]);
                    } else break;
                }
                return $rets;
            } else if(is_array($name)){
                $newV = [];
                for($ii=0;$ii<sizeof($name);$ii++){
                    array_push($newV,$name[$ii]);
                }
                return template::AjaxData($name,$values);
            } else {
                return "$name=$values";
            }
        }
        //a function to compose a javascript function call
        public static function AjaxFun($name, $args = ""){
            $rets = $name."(";
            if(is_array($args)){
                $new_arg = "";
                for($ii=0;$ii<sizeof($args);$ii++){
                    $new_arg .= $args[$ii];
                    if($ii<sizeof($args))
                        $new_arg .= ",";
                }
                return template::AjaxFun($name,$new_arg);
            }
            return $rets.$args.")";
        }
        //a function to create an AJAX form from a file to produce alos the file javascript to send
        public function AjaxForm($HTMLfile,$receiverFile = "index.php",$execEL = "",$APPEND_VAR = "",$APPEND_DATA = ""){
             $newTemp = new template(PIP_Str::random());
             $newTemp->folder($this->dir);
             $newTemp->body($HTMLfile);
             $newTemp->locs($this->locs());
             $newTemp->var($this->variables);
            
             
            
             if(is_array($APPEND_VAR)){
                if(isset($APPEND_VAR[0])){
                    for($ii=0;$ii<sizeof($APPEND_VAR);$ii++){
                        if(isset($APPEND_VAR[$ii]["name"])&&isset($APPEND_VAR[$ii]["value"])){
                            $newTemp->var($APPEND_VAR[$ii]["name"],$APPEND_VAR[$ii]["value"]);
                        }
                    }
                } else {
                    if(isset($APPEND_VAR["name"])&&isset($APPEND_VAR["value"])){ 
                        $newTemp->var($APPEND_VAR["name"],$APPEND_VAR["value"]);
                    }
                }
            } else if((!is_array($APPEND_VAR))&&(!is_array($APPEND_DATA))){
                 return $this->AjaxForm($HTMLfile,$receiverFile,$execEL,[$APPEND_VAR],[$APPEND_DATA]);
             }
             
             $all = $newTemp->renderAjax()->doc();
             $randS = PIP_Str::random();
             $id = PIP_Str::random();
             $returns = PIP_Str::random();
             if(pipStr($execEL)->length()==0)
                 $execEL = "#".$id;
             $all = '<form id="'.$id.'">'.$all."</form>";
             $all .= '
                      <script>
                        $("#'.$id.'").off("submit").on("submit",function('.$randS.'){
                              '.$randS.'.preventDefault();
                              var '.$id.' = new FormData($("#'.$id.'")[0]);';
                            if(is_array($APPEND_DATA)){
                                if(isset($APPEND_DATA[0])){
                                    for($ii=0;$ii<sizeof($APPEND_DATA);$ii++){
                                        if(isset($APPEND_DATA[$ii]["name"])&&isset($APPEND_DATA[$ii]["value"])){
                                           $all .= $id.'.append("'.$APPEND_DATA[$ii]["name"].'","'.$APPEND_DATA[$ii]["value"].'");'; 
                                        }
                                    }
                                } else {
                                    if(isset($APPEND_DATA["name"])&&isset($APPEND_DATA["value"])){
                                        $all .= $id.'.append("'.$APPEND_DATA["name"].'","'.$APPEND_DATA["value"].'");';
                                    }
                                }
                            }
            $all .=
                 '
                              $("'.$execEL.'").html(LOADING_S_);
                              $.ajax({
                                         url : "'.$receiverFile.'",
                                         type : "POST",
                                         data : '.$id.',
                                         processData: false,  // tell jQuery not to process the data
                                         contentType: false,  // tell jQuery not to set contentType
                                         enctype: "multipart/form-data",
                                         success : function('.$returns.'){
                                              $("'.$execEL.'").html('.$returns.');
                                         }
                                });
                        });
                      </script>';
             unset($newTemp);
             return $all;
             
        }
        // a function to produce The ajaxForm script for the form
        public static function AjaxFormScript($FORMEL,$receiverFile = "index.php",$execEL = "",$APPEND_VAR = "",$APPEND_DATA = ""){
             $randS = PIP_Str::random();
             $returns = PIP_Str::random();
             if(pipStr($execEL)->length()==0)
                 $execEL = $FORMEL;
             $all = '
                      <script>
                        $("'.$FORMEL.'").off("submit").on("submit",function('.$randS.'){
                              '.$randS.'.preventDefault();
                              var '.pipStr($FORMEL)->remove("#")->remove(".")->singleText()->singleLine()->str().' = new FormData($("'.$FORMEL.'")[0]);';
                            if(is_array($APPEND_DATA)){
                                if(isset($APPEND_DATA[0])){
                                    for($ii=0;$ii<sizeof($APPEND_DATA);$ii++){
                                        if(isset($APPEND_DATA[$ii]["name"])&&isset($APPEND_DATA[$ii]["value"])){
                                           $all .= $id.'.append("'.$APPEND_DATA[$ii]["name"].'","'.$APPEND_DATA[$ii]["value"].'");'; 
                                        }
                                    }
                                } else {
                                    if(isset($APPEND_DATA["name"])&&isset($APPEND_DATA["value"])){
                                        $all .= $id.'.append("'.$APPEND_DATA["name"].'","'.$APPEND_DATA["value"].'");';
                                    }
                                }
                            }
            $all .=
                 '
                              $("'.$execEL.'").html(LOADING_S_);
                              $.ajax({
                                         url : "'.$receiverFile.'",
                                         type : "POST",
                                         data : '.pipStr($FORMEL)->remove("#")->remove(".")->singleText()->singleLine()->str().',
                                         processData: false,  // tell jQuery not to process the data
                                         contentType: false,  // tell jQuery not to set contentType
                                         enctype: "multipart/form-data",
                                         success : function('.$returns.'){
                                              $("'.$execEL.'").html('.$returns.');
                                         }
                                });
                        });
                      </script>';
            return $all;
        }
        // a function to produce a pagination according to the given data
        public static function pages($name,$total = 0 , $itemNum = 3, $pageNum = 3, $size = ""){
            if((!is_numeric($total))||(!is_numeric($itemNum)))
                return template::danger("??? INVALID PAGINATION INPUT ???");
            else if(($total<=$itemNum)||($itemNum==0)||($total==0))
                return "&nbsp;";
            else {
                $pageNums = intval(intval($total)/intval($itemNum));
                $rets = '<ul class="pagination '.$size.'">';
                if(($pageNums+1)>$pageNum)
                            $rets .='<li class="page-item skip-pages-'.$name.'-back"><a href="#" class="page-link">«</a></li>';
                $pages = 1;
                for($ii=0;$ii<=$pageNums;$ii++){
                   $rets .= '<li class="page-item';
                    if(($ii+1)>$pageNum){
                       $rets .= ' hidden-page-num'.$name.'';
                    } else {
                       $rets .= ' visible-page-num'.$name.''; 
                    }
                    
                    if($ii==0)
                       $rets .= ' active'; 
                    
                    $rets .= ' pages-'.$name.' page-num-'.$pages."-".$name.'" data-index="'.($ii+1).'" >
                            <a href="#" class="page-link">'.($ii+1).'</a></li> '; 
                    if((($ii+1)%$pageNum)==0) $pages++;
                }
                if(($pageNums+1)>$pageNum)
                    $rets .= '<li class="page-item skip-pages-'.$name.'-fwd"><a href="#" class="page-link">»</a></li></ul>';
                       
            }
            return $rets;
        }
        // a function to produce a pagination script of the produced pages
        public static function pageScript($name,$file,$returnDiv,$pageNum = 3,$extra = ""){
            return '<script>
                        var active_'.$name.' = 1;
                        var page_num_'.$name.' = 1;
                        $(".skip-pages-'.$name.'-fwd").click( function(vb){
                            vb.preventDefault();
                            $(".page-num-"+page_num_'.$name.'+"-'.$name.'")
                                    .addClass("hidden-page-num'.$name.'")
                                    .removeClass("visible-page-num'.$name.'");
                            page_num_'.$name.'++;
                            $(".page-num-"+page_num_'.$name.'+"-'.$name.'")
                                    .removeClass("hidden-page-num'.$name.'")
                                    .addClass("visible-page-num'.$name.'");
                            $(".hidden-page-num'.$name.'").hide();
                            $(".visible-page-num'.$name.'").show();
                        });
                        
                        $(".skip-pages-'.$name.'-back").click( function(vb){
                            vb.preventDefault();
                            $(".page-num-"+page_num_'.$name.'+"-'.$name.'")
                                    .addClass("hidden-page-num'.$name.'")
                                    .removeClass("visible-page-num'.$name.'");
                            page_num_'.$name.'--;
                            $(".page-num-"+page_num_'.$name.'+"-'.$name.'")
                                    .removeClass("hidden-page-num'.$name.'")
                                    .addClass("visible-page-num'.$name.'");
                            $(".hidden-page-num'.$name.'").hide();
                            $(".visible-page-num'.$name.'").show();
                        });
                        
                        $(".hidden-page-num'.$name.'").hide();
                        $(".visible-page-num'.$name.'").show();
                        $(".pages-'.$name.'").click( function(bn){
                            bn.preventDefault();
                            $(".pages-'.$name.'").removeClass("active");
                            var indexes = $(this).attr("data-index");
                            active_'.$name.' = parseInt(indexes);
                            $(this).addClass("active");
                            $("'.$returnDiv.'").html(LOADING_S_);
                            $.post("'.$file.'",
                                   "indexes'.$extra.'="+indexes,
                                   function(rets){
                                $("'.$returnDiv.'").html(rets);
                            });
                        })
                    </script>';
        }
        // a function like the one above page but with ranndom name
        public static function paging($returnDiv,$extra,$total = 0 , $itemNum = 3, $pageNum = 3,$file = "index.php",$class=""){
            $theNames = PIP_Str::random(10);
            return [
                "html"=>template::pages($theNames,$total,$itemNum,$pageNum,$class),
                "js"=>template::pageScript($theNames,$file,$returnDiv,$pageNum,$extra)
            ];
        }
        // a function to produce an edit script 
        public static function editScript($name,$file = "index.php"){
            return '
                <script>
                    $("#'.$name.'").submit( function('.$name.'){
                        '.$name.'.preventDefault();
                        var '.$name.'_send = $(this).serialize();
                        $("#'.$name.'").html(LOADING_S_);
                        $.post("'.$file.'",
                                '.$name.'_send,
                               function(ret_'.$name.'_send){
                            $("#'.$name.'").html(ret_'.$name.'_send);
                        });
                    });
                </script>
            ';
        }
        // a function to genarate a pair of name value of attribute in form of array
        public static function attr($name,$value){
            return [
              "name"=>$name,
              "val"=>$value
            ];
        }
        //a function to decodes the string to be saved with eliminated resticted characters and html entities
        public static function decodeTosend($str){
            $str = str_replace("%%","&",$str);
            $str = str_replace("**","#",$str);
            $str = str_replace("^^","\n",$str);
            $str = str_replace("..","\t",$str);
            $str = str_replace("__","",$str);
            $str = str_replace("@@",">",$str);
            $str = str_replace("&quot;","'",$str);
            return $str;
        }
        // a function to save a certain text to a file with repetitive ajax request
        // $filename is the file url, and $nums is the times of ajax time, 
        public static function saveFiles($filename,$nums,$str){
            $rets = [
                "message"=>"saving in progress",
                "status"=>true
            ];
            if(!is_file($filename)){
                $fp = fopen($filename,'w');
                fclose($fp);
            } else if(intval($nums)==1){
                $fp = fopen($filename,'w');
                if(@fwrite($fp," ")){
                    $rets["message"] = " ::initializing process success:: ";
                    
                } else {
                    $rets["message"] = " ::initializing process failed:: ";
                    $rets["status"] = false;
                    return $rets;
                }
                fclose($fp);
            } else if(pipStr($nums)->str()=="lastOneSingle"){
                $fp = fopen($filename,'w');
                if(@fwrite($fp," ")){
                    $rets["message"] = " ::initializing process success:: ";
                    
                } else {
                    $rets["message"] = " ::initializing process failed:: ";
                    $rets["status"] = false;
                    return $rets;
                }
                $nums = "lastOne";
                fclose($fp);
            }
            if($fp = fopen($filename,'r')){
                $prev = fgets($fp);
                while(!feof($fp)){
                    $prev = $prev.fgets($fp); 
                }
                fclose($fp);
                $prev = $prev.template::decodeTosend($str);
                if($fp = fopen($filename,'w')){
                    if(@fwrite($fp,$prev)){
                        $rets["message"] = " :: Updating to a file success :: ";
                    } else {
                        $rets["message"] = " :: failed to save to the file, please check the directory permission or contact tech support :: ";
                        $rets["status"] = false;
                        return $rets; 
                    }   
                } else {
                    $rets["message"] = " :: Opening file $filename for appending failed new data :: ";
                    $rets["status"] = false;
                    return $rets; 
                }
            } else {
                $rets["message"] = " :: Opening file $filename for reading previous failed :: ";
                $rets["status"] = false;
                return $rets;
            }
            return $rets; 
        }
        public static function summernote(){
            return new class {
                // a variable the id of the summernote
                private $the_id = "summernote";
                private $the_save_button_name = "save_about_us";
                private $button_containers_divs = "button_containers_divs";
                private $progressive_container_saving = "progressive_container_saving";
                // a variable to keep all needed javascript
                private $js = "";
                // a variable to keep the id of the item we will be looking for in the db
                private $id = "";
                // a variable to keep the executor of the editor on the request
                private $executor = "";
                // a variable to keep a substring to be sent
                private $values_to_ss = "values_to_ss";
                // a variable to keep where we are on
                private $nums = 0;
                
                
                function __construct(){
                    $this->the_id = PIP_Str::random(20);
                    $this->the_save_button_name = PIP_Str::random(20);
                    $this->button_containers_divs = PIP_Str::random(20);
                    $this->progressive_container_saving = PIP_Str::random(20);
                }
                // a function to set the data id of this editor
                function id($id){
                    $this->id = $id;
                    return $this;
                }
                //a function to set the current value to be sent
                function string($name){
                    $this->values_to_ss = $name;
                    return $this;
                }
                //a function to set the num value
                function num($num){
                    $this->nums = $num;
                    return $this;
                }
                //a function to return the editor with $content inside
                function editor($contents){
                    return '<textarea id="'.$this->the_id.'">'.$contents.'</textarea>
                            <div id="'.$this->button_containers_divs.'">
                                <button type="button" class="btn btn-info btn-flat" id="'.$this->the_save_button_name.'">
                                    <i class="fas fa-save"></i> Save 
                                </button>
                            </div>
                            <div id="'.$this->progressive_container_saving.'" style="display:none">

                            </div>';
                }
                // a function to return all required js for the editor
                function js($data_id,$file = "index.php"){
                    if(!is_numeric($data_id)) $data_id = 0;
                    $theVarName = PIP_Str::random(10);
                    $decodeTosend = PIP_Str::random(20);
                    $shortRequest = PIP_Str::random(20);
                    $saveFile =  PIP_Str::random(20);
                    return '<script>
                                $("#'.$this->the_id.'").summernote();
                                $("#'.$this->the_save_button_name.'").click( function('.$theVarName.'){
                                    '.$theVarName.'.preventDefault();
                                    var '.$theVarName.' = $(\'#'.$this->the_id.'\').val();
                                    $("#'.$this->progressive_container_saving.'").show().html(LOADING_S_);
                                    $(\'#'.$this->button_containers_divs.'\').hide();
                                    var reqst = '.$shortRequest.'('.$decodeTosend.'('.$theVarName.'),'.$data_id.');
                                    var allRets = "";
                                    '.$saveFile.'(reqst,allRets);
                              });
                              
                              function '.$decodeTosend.'(str){
                                  return str
                                      .replace(/&/g,"%%")
                                      .replace(/#/g,"**")
                                      .replace(/\n/g,"^^")
                                      .replace(/\t/g,"..")
                                      .replace(/\b/g,"")
                                      .replace(/>/g,"@@");
                              }
                              
                              function '.$shortRequest.'(str = "",data_id,num = 0){
                                  var rets = {
                                      "contents":"",
                                      "num":num,
                                      "remaining":"",
                                      "request":"",
                                      "ends":false,
                                      "name":data_id
                                  }
                                  if(str.length==0){
                                      rets.contents = "";
                                      rets.ends = true;
                                      rets.remaining = "";
                                      rets.num = "lastOne"
                                  } 
                                  else if(str.length<=1200){
                                      rets.contents = str;
                                      rets.remaining = "";
                                      rets.num = "lastOne";
                                      if(num===0){
                                          rets.num = "lastOneSingle";
                                      }
                                  } 
                                  else {
                                      rets.contents = str.substring(0,1200);
                                      rets.remaining = str.substring(1200,str.length);
                                      rets.num++;
                                  }
                                  rets.request = "'.$this->values_to_ss.'="+rets.contents+"&'.$this->nums.'="+rets.num+"&'.$this->id.'="+data_id;
                                  return rets;
                              }
                              
                              function '.$saveFile.'(reqst,allRets){
                                      if(reqst.ends){
                                          $("#'.$this->progressive_container_saving.'").html(allRets);
                                          setTimeout( function(){
                                               $("#'.$this->progressive_container_saving.'").hide().html("");
                                               $(\'#'.$this->button_containers_divs.'\').show();
                                          },4000);
                                      } else {
                                          $.get("'.$file.'",
                                                reqst.request,
                                                function(rets){
                                                '.$saveFile.'('.$shortRequest.'(reqst.remaining,reqst.name,reqst.num),rets);
                                           });
                                      }
                              }
                            </script>';
                }
                // a function to check if all this value are set
                
                function set(){
                    return http($this->values_to_ss)->set()&&http($this->id)->set()&&http($this->nums)->set();
                }
                // a function to check the values of the id
                function id_val(){
                    return http($this->id)->val();
                }
                // a function to return the value of the current string
                function s_val(){
                    return http($this->values_to_ss)->val();
                }
                
                function n_val(){
                    return http($this->nums)->val();
                }
                
                function update($files){
                    $updates = template::saveFiles($files,$this->n_val(),$this->s_val());
                    $updates["done"] = false;
                    if(($this->n_val()=="lastOne")||($this->n_val()=="lastOneSingle")&&($updates["status"])){
                        $updates["done"] = true;
                    }
                    return $updates;
                }  
            };
        }
        // a function to return a hidden input to continue in saving some data
        public static function hiddenInput($name,$value,$type="number", $attr = []){
            if(is_array($type))
                return self::hiddenInput($name,$value,"number",$type);
            $rets = '<input type="'.$type.'" value="'.$value.'" name="'.$name.'" style="display:none" ';
            for($ii=0;$ii<sizeof($attr);$ii++){
                if(isset($attr[$ii]["name"])&&isset($attr[$ii]["val"])){
                    $rets .= $attr[$ii]["name"].'="'.$attr[$ii]["val"].'" ';
                }
            }
            return $rets.'>';
        }
        // a function to generate a bootstrap like tool tips
        public static function tooltips($text,$contents,$pos = "top", $class = "", $attr = ""){
            $attrs = "";
            if(is_array($attr)){
                if(isset($attr["name"])&&isset($attr["val"])){
                    return template::tooltips($text,$contents,$pos,$class,[$attr]);
                } else if(isset($attr[0])){
                    for($ii=0;$ii<sizeof($attr);$ii++){
                        $attrs .= " ".$attr[$ii]["name"].'="'.$attr[$ii]["val"].'"';
                    }
                }
            }
            
            $classes = "";
            if(pipStr($class)->length())
                $classes = "class=\"$class\"";
            
            return   '<a href="#" data-html="true" data-tooltip="tooltip" '.$classes.' data-placement="'.$pos.'" title="'.$contents.'" '.$attrs.' >'.$text.'</a>';
            
        }
        // a function to generate the font awasame like icon
        public static function Nicon($name,$general = "fa"){
            return "<i class=\"$general $name\"></i>";
        }
        // a function to produce some of the the continue buttons and cancel buttons with bootstrap link like
        public static function SmallContBtn($data_id,$diffName,$file,$retrivalKey,$retsDiv){
            return '<a href="#" class="text-danger" id="CONTINUE_'.$diffName.md5($data_id).'" data-id="'.$data_id.'"> Continue </a>
                    <a href="#" class="text-info" id="CANCEL_'.$diffName.md5($data_id).'"> Cancel </a>
                    <script>
                        $("#CONTINUE_'.$diffName.md5($data_id).'").off("click").click( function(CONTINUE_DIFF_NAME){
                            CONTINUE_DIFF_NAME.preventDefault();
                            $("'.$retsDiv.'").html(LOADING_S_);
                            var data_id = $(this).attr("data-id");
                            $.post("'.$file.'",
                                   "'.$retrivalKey.'="+data_id,
                                   function(rets){
                                $("'.$retsDiv.'").html(rets);
                            })
                        });
                        $("#CANCEL_'.$diffName.md5($data_id).'").off("click").click( function(CONTINUE_DIFF_NAME){
                            CONTINUE_DIFF_NAME.preventDefault();
                            document.location = "";
                        });
                    </script>';
        }
        // a function to produce some space like html entities
        public static function space($size = 1){
            $rets = "";
            for($ii = 0; $ii < $size; $ii++){
               $rets .= "&nbsp;"; 
            }
            return $rets;
        }
        // a public function to generate a console JavaScript like
        //a function to create an editing area for the editing
        public static function editor(){
            return new PIP_EDITOR();
        }
        // a function to create an empty div with generated id with specified script name
        public static function emptyDiv($name,$class= ""){
            $theId = PIP_Str::random(20);
            $classt = "";
            if(pipStr($class)->length())
               $classt = ' class="'.$class.'" '; 
            $html = '<div id="'.$theId.'"'.$classt."></div>";
            $js = '<script>
                        var '.$name.' = "'.$theId.'";
                  </script>';
            return [
              "html"=>$html,
              "js"=>$js
            ];
        }
        // a function to return the created file and the name with randomxname
        // where size is the size of the file
        public static function cfile($contents = "",$size = 20, $ext = '.html'){
            $the_files = PIP_Str::randomx($size);
            $fp = fopen($the_files.$ext,'w');
            fclose($fp);
            return [
              "name"=>$the_files,
              "type"=>$the_files.$ext
            ];
        }
        // a function to create a file with a provided name
        public static function _cfile_($locs_and_name, $ext = ".html"){
            $fp = fopen($locs_and_name,'w');
            $rets = true;
            if(!$fp){
               $rets = false; 
            }
            fclose($fp);
            return $rets;
        }
        // a function to clear a specified file
        public static function efile($file){
            if (!$fp = @fopen($file,'w')) {
               return false;
            }
            if (!@fwrite($fp," ")) {
                return false;
            }
            return true;
        }
        // a function to return html document of this file
        public function doc(){
            return $this->document;
        }
    }
    // a class that will return all information of the editor with html, js, css
    class PIP_EDITOR {
                 // a variable to hold all needed JavaScript to be used in the editing process
                 private $script;
                 // a variable to hold instance of html at the time off calling of button() and image() function
                 private $html;
                 // a variable that will hold the html class attribute of all needed inputs and thier holder
                 private $Class;
                 // a variable that will hold the needed css codes to presents all needed information
                 private $css;
                 // a variable to keep the current name when the form is submitted
                 private $currentName = "";
                 // a variable to keep the current type of the submitted field
                 private $currentType = "";
                 // a variable to keep data from database where we need to edit
                 private $data = "";
                 // a variable to keep all data to be appended while submitting
                 private $appending_data = [];
                 // a variable to keep number of data to append
                 private $data_to_append = 0;
                 // a variable to keep if the current index will be displayed in the field before being submitted
                 private $currentIndex = 0;
                 // the varibale to know $_POST index to keep the index to use while editing in the database
                 private $the_id_holder = "";
                 // a variable to holds all name in the form name of the table field and name and data type at the same time
                 private $alliases = [];
                 // a variable that will keep the name of the editor where CURR is the default
                 private $name;
                 // a variable to keep this image if the request sent is a file in array
                 private $imag_file;
                 // the constructor
                 function __construct($NAME = "CURR", $image_name = "The random name of the image to edit"){
                    $this->name = $NAME;
                    $this->imag_file = [NULL,false,$image_name];
                    $this->init();
                 }
                 // the append function to use when we need to append data to the http request to be sent as POST method
                 // $elemt->append(name,value) means $_POST[name] = value
                 function append($name,$value){
                     $this->data_to_append++;
                     array_push($this->appending_data,["name"=>$name,"value"=>$value]);
                     return $this;
                 }
                 //a function to read the values from appended data
                 function appendV($name){
                     return http($name)->val();
                 }
                 // a function to clear all appended data
                 function clear(){
                     $this->data_to_append = 0;
                     $this->appending_data = [];
                 }
                 // a function to initialize the element to the time off definition
                 function init(){
                     $this->Class = PIP_Str::random(15);
                     $this->script = '';
                     $this->html = "";
                     $this->data = pipArr();
                     $this->css = "";
                     $this->clear();
                     $this->currentIndex = 0;
                 }
                 // the function to call to set the current index to some value default is zero
                 function index($index = 0){
                     $this->currentIndex = $index;
                     return $this;
                 }
                 // a function to hardly return the $currentIndex
                function Tindex(){
                    if(is_string($this->currentIndex)) return true;
                    if($this->currentIndex>0) return true;
                    return false;
                    
                }
                // a function to set all aliases to be on the list of for the server and client
                 function alias($name,$value, $type = ""){
                     $this->alliases[$name]["name"] = $value;
                     if(pipStr($type)->length()>0){
                         $this->alliases[$name]["type"] = $type;  
                     }
                     return $this;
                 }
                 // a function to return the seted value from aliases
                
                 function val($name){
                     if(isset($this->alliases[$name]["name"])) return $this->alliases[$name]["name"];
                     else return 0;
                 }
                 // a function to know the name of the field
                
                 function field(){
                     return $this->val(http($this->name)->val());
                 }
                // a function to return the new value of the field
                
                 function value(){
                     if($this->imag_file[1]) return $this->imag_file[0]->src();
                     return http(http($this->name)->val())->val();
                 }
                
                 // a function to add an id holder value of the object
                
                 function id($value = "", $id = 0){
                     if(pipStr($value)->length()===0){
                         return http($this->the_id_holder)->val();
                     }
                     $this->the_id_holder = $value;
                     return $this->append($this->the_id_holder,$id);
                 }
                
                 // a function to return the type by given $name
                
                 function Ntype($name){
                     if(isset($this->alliases[$name]["type"])) return $this->alliases[$name]["type"];
                     else return 0;
                 }
                
                 // a function to check if input is a file with like the above Ntype function
                
                 function isFile($name = ""){
                     if(pipStr($name)->length()==0)
                         return $this->isFile(http($this->name)->val());
                     return ($this->Ntype($name)==="file");
                 }
                 // a function to compose the needed JavaScript 
                 function script($file = "index.php"){
                     $additionalIds = PIP_Str::random(15);
                     $toAppend = "";
                     if($this->data_to_append){
                         for($ii=0;$ii<sizeof($this->appending_data);$ii++){
                             $toAppend .= $additionalIds.".append(\"".$this->appending_data[$ii]["name"].'","'.$this->appending_data[$ii]["value"]."\");";
                         }
                     }
                     $displaying = 'if(data_type=="longtext")
                                        $("#'.$this->Class.'CONTS").html('.$this->Class.'DATA[0][data_index]);
                                    else $("#'.$this->Class.'CONTS").val('.$this->Class.'DATA[0][data_index]);';
                     if(!$this->Tindex()){
                         $displaying = "";
                     }
                     $this->script = '<script>
                                        var '.$this->Class.'DATA = '.$this->data->JV.';
                                        $(".'.$this->Class.'")
                                            .off("click")
                                            .click( function('.$this->Class.'){
                                                '.$this->Class.'.preventDefault();
                                                var data_type = $(this).attr("data-type");
                                                var data_name = $(this).attr("data-name");
                                                var data_index = $(this).attr("data-index");
                                                $(".'.$this->Class.'").hide();
                                                if(data_type=="longtext")
                                                    data_type = `<textarea class="form-control" 
                                                                    id="'.$this->Class.'CONTS" rows="5" required></textarea>`;
                                                else data_type = `<input type="${data_type}" 
                                                                        id="'.$this->Class.'CONTS" 
                                                                        class="form-control" name="'.$this->Class.'" required>`;
                                                $(this).after(`<div id="'.$additionalIds.'" class="editor-popup">
                                                                   <form>
                                                                      ${data_type}
                                                                      <button type="submit" class="pip-btn text-info">
                                                                            <i class="fa fa-save"></i> Save
                                                                      </button> &nbsp;
                                                                      <a href="#" class="text-danger" id="'.$additionalIds.'_CANCEL">
                                                                        <i class="fas fa-times-circle"></i> Close </a>
                                                                   </form>
                                                                </div>`);
                                                '.$displaying.'
                                                $("#'.$additionalIds.'_CANCEL")
                                                    .off("click")
                                                    .click( function('.$additionalIds.'_CANCEL){
                                                        '.$additionalIds.'_CANCEL.preventDefault();
                                                        $(this).parent().parent().remove();
                                                        $(".'.$this->Class.'").show();
                                                });
                                                $("#'.$additionalIds.'")
                                                    .find("form")
                                                    .off("submit")
                                                    .submit( function('.$additionalIds.'){
                                                        $("#'.$this->Class.'CONTS").attr("name",data_name);
                                                        '.$additionalIds.'.preventDefault();
                                                        '.$additionalIds.' = new FormData($("#'.$additionalIds.'").find("form")[0]);
                                                        '.$toAppend.';
                                                        '.$additionalIds.'.append("'.$this->name.'",data_name);
                                                        $(this).parent().html(LOADING_S_);
                                                        $.ajax({
                                                                 url : "'.$file.'",
                                                                 type : "POST",
                                                                 data : '.$additionalIds.',
                                                                 processData: false,
                                                                 contentType: false,
                                                                 enctype: "multipart/form-data",
                                                                 success : function('.$additionalIds.'){
                                                                      $("#'.$additionalIds.'").html('.$additionalIds.');
                                                                 }
                                                        });
                                                })
                                        });
                                    </script>';
                     return $this;
                 }
                 // a function to set the name of the html form on the current value
                 function name($name){
                     $this->currentName = $name;
                     $this->index($this->val($name));
                     $this->type($this->Ntype($name));
                     return $this;
                 }
                 // a function to set the current data type
                 function type($type){
                     $this->currentType = $type;
                      return $this;
                 }
                 // a function to create a clickable button while editing a text fields
                 function button($contents = "Edit", $class = ""){
                     $this->html =  '<span class="'.$class.' '.$this->Class.'" 
                                        data-name="'.$this->currentName.'" 
                                        data-type="'.$this->currentType.'"
                                        data-index="'.$this->currentIndex.'">
                                       '.$contents.'
                                    </span>';
                     $this->css = "<style> .".$this->Class."{cursor:pointer;color: #17a2b8!important;}</style>";
                     return $this;
                 }
                 // a function to generate an editable image of the editor
                 function image($url , $class = "", $attr = []){
                     $this->currentIndex = 0;
                     array_push($attr,template::attr("data-type","file"));
                     array_push($attr,template::attr("data-name",$this->currentName));
                     $this->html = template::image($url,"$class $this->Class",$attr);
                     return $this;
                 }
                 // a function to set data as a PIP_Array instance
                 function data($data){
                     if($data instanceof PIP_Array){
                         $this->data = $data;
                     } else{
                         throw new Exception(' Wrong datatype given as data when there was instanceof PIP_Array needed ');
                     }
                     return $this;
                 }
                 // a function to return generated JavaScript
                 function js(){
                     return $this->script;
                 }
                 // a function to return the generated css
                 function css(){
                     return $this->css;
                 }
                // a function to generate the generated html at current call
                 function html($display = ""){
                     $rets = "";
                     
                     if(pipStr($display)->length()==0){
                         if(!($this->data instanceof PIP_Array))
                             echo template::danger("The given data is not an instance of PIP_Array");
                         else if(!($this->currentIndex===0)) $rets = $this->data->AV[0][$this->currentIndex];  
                     } else $rets = $display;
                     return $rets.$this->html;
                 }
                 // a function to return true if all values of this fields was set
                 function set(){
                     if($this->isFile()){
                         $this->imag_file[0] = image(http($this->name)->val(),$this->imag_file[2]);
                         $this->imag_file[1] = true;
                     } 
                     for($ii=0;$ii<sizeof($this->appending_data);$ii++){
                         if(!http($this->appending_data[$ii]["name"])->set()) return false;
                     }
                     return (http($this->the_id_holder)->set())&&(http($this->name)->set());
                 }
        
                 // a function to return the image of this element
        
                function img(){
                    if($this->imag_file[1]){
                        return $this->imag_file[0];
                    }
                    else {
                        throw new Exeption("Trying to call the image while was not set in the editor");
                    }
                    return false;
                }
        
                /*
                    example of usage 
                    
                    $editor = new PIP_EDITOR("name","new image comment to be saved");
                    
                    ######to add some aliases with text a input type
                    
                    ==> $editor->alias("name_of_html_form_input","name_of_table_field");
                    output
                    <input type="text" name="name_of_html_form_input" value="$editor->data->AV[0]["name_of_table_field"]">
                    
                    ######to add some aliases with number a input type
                    
                    ==> $editor->alias("name_of_html_form_input","name_of_table_field","number");
                    
                    ######to add some aliases with file a input type
                    
                    ==> $editor->alias("name_of_html_form_input","name_of_table_field","file");
                    #######to add some aliases with textarea a input type
                    ==> $editor->alias("name_of_html_form_input","name_of_table_field","longtext");
                    <textarea name="name_of_html_form_input">
                        $editor->data->AV[0]["name_of_table_field"]
                    </textarea>
                    ####### to generate an html btn
                    ==> $editor->name("name_of_html_form_input")->button("button")->html();
                    ####### to generate an html image
                    ==> $editor->name("name_of_html_form_input")->image(Url,cssClasses,htmlAttributes)->html()
                    ####### getting css
                    ==> $editor->css()
                    ####### getting JavaScript
                    $editor->script()->js()
                    ####### to check if it is a file submited
                    if($editor->isFile()) 
                    ####### to upload an image
                    
                    $newImage = $editor->img()->locs($locs)->folder("img/uploads")->max(5000000)->min(100)->save();
                    
                    ####### to save changes
                    
                    ADMIN->edit($editor);
                */
        
                
            }

    // a class that will be manipulating pip script in presentable HTML format way
    class PIPScript{
        private $type;
        private $Class;
        private $name;
        
        // variables to keep colors of the all element to generate css
        private $bracketColor = "darkgreen";
        private $separatorColor = "red";
        private $namesColor = "blue";
        
        private $bracketClass;
        private $separatorClass;
        private $namesClass;
        private $var;
        function __construct($type = "var",$class = "temp",$name = "pip"){
            $this->type = $type;
            $this->Class = $class;
            $this->name = $name;
            
            $this->bracketClass = PIP_Str::random(20);
            $this->separatorClass = PIP_Str::random(20);
            $this->namesClass = PIP_Str::random(20);
            $this->var = PIP_Str::random(20);
            
        }
        function type($type){
            $this->type = $type;
            return $this;
        }
        
        function var($var){
           $this->var = $var;
           return $this;
        }
        
        function Class($class){
            $this->Class = $class;
            return $this;
        }
        function nameSpace($name){
            $this->name = $name;
            return $this;
        }
        // a functions to declare color of brackets, separator and namesColor respectively
        function blacketColor($color){
            $this->bracketColor = $color;
            return $this;
        }
        function separatorColor($color){
            $this->separatorColor = $color;
            return $this;
        }
        function namesColor($color){
            $this->namesColor = $color;
            return $this;
        }
        // a function to compose all about the opening tag 
        function compose(){
                return $this->brackets()
                        .$this->name($this->name)
                        .$this->separator()
                        .$this->name($this->Class)
                        .$this->separator()
                        .$this->name($this->type)
                        .$this->brackets(0)
                        .template::space(5)
                        .$this->var
                        .$this->brackets()
                        .$this->name($this->name)
                        .$this->separator()
                        .$this->name($this->Class)
                        .$this->separator()
                        .$this->name($this->type)."/"
                        .$this->brackets(0);
        }
        // a function to compose the <span> tag with content and class to be able to display of the opening/closeing bracket < and > 
        function brackets($closing = true){
            $conts = "&gt;";
            $start = "<br>";
            $end = "";
            if($closing){
              $start = "";
              $end = "<br>";
              $conts = "&lt;";  
            } 
            return $end.'<span class="'.$this->bracketClass.'">'.$conts.'</span>'.$start;
        }
        
        function separator(){
            return '<span class="'.$this->separatorClass.'">::</span>';
        }
        
        function name($name){
            return '<span class="'.$this->namesClass.'">'.$name.'</span>';
        }
        
        function css($tags = false){
            $rets = '';
            $later = '';
            if($tags) {
                $rets = "\n".'<style>';
                $later = "\n".'</style>';
            }
            $rets .= "\n".'.'.$this->bracketClass."{\n \t color: ".$this->bracketColor."\n}\n";
            $rets .= '.'.$this->separatorClass."{\n \t color: ".$this->separatorColor."\n}\n";
            $rets .= '.'.$this->namesClass."{\n \t color: ".$this->namesColor."\n}\n";
            return "\n".$rets.$later."\n";
        }
        
    }

    // a class for entire javascript handleling

    class JavaScript{
        private $script = "";
        private $html = "";
        function __construct(){
            $this->console = $this->console();
        }
        
        function console(){
            return new class {
                function log($text){
                    return '<script>
                                console.log(`'.$text.'`);
                            </script>';
                }
            };
        }
        
        function alert($message){
            return $this->funCalls("alert",$message);
        }
        
        function randStr($name,$size = 10){
            return '<script>
                        var '.$name.' = "'.PIP_Str::random($size).'";
                    </script>';
                    
        }
        
        function funCall($name , $args = []){
            $rets = pipStr($name)->remove("(")->remove(")")->str()."(";
            if(!is_array($args)) return $this->funCall($name,[$args]);
            for($ii=0;$ii<sizeof($args);$ii++){
                $rets .= $args[$ii];
                if($ii<sizeof($args)-1){
                   $rets .= ",";
                }
            }
            return $rets.")";
        }
        function funCalls($name , $args = []){
            return "<script>".$this->funCall($name,$args)."</script>";
        }
        
        // a function to close a modal and call another function (js)
        public function CloseModal($modal,$afterTime = 0,$fun = ""){
            return '<script>
                        
                        setTimeout(function(){
                            $("'.$modal.'").modal("hide");
                            '.$fun.'
                        },'.$afterTime.');
                    </script>';
        }
        
        // a function to change the input value
        
        public function input(){
            return new class {
              private $name = NULL;
              private $type = NULL;
              private $value = NULL;
              private $id = NULL;
              private $Class = "";
              function __construct(){
                  $this->name = PIP_Str::random(20);
              } 
              function name($name){
                  $this->name = $name;
                  return $this;
              }
                
              function type($name){
                  $this->type = $name;
                  return $this;
              }
              
              function value($name){
                  $this->value = $name;
                  return $this;
              }
                
              function id($name){
                  $this->id = $name;
                  return $this;
              }
                
              function Class($name){
                  $this->Class .= $name;
              }
              // a function that will change of a specified input with css selector
              static function val($value,$id){
                  return '<script>
                              $('.$id.').val('.$value.');  
                          </script>';
              }
                
            };
        }
        
        // a function to put script tag in string
        
        public function tag($val, $src = true){
            if($src)
                return '<script>'.$val.'</script>';
            else return '<script src="'.$val.'"></script>';
        }
        
        // a function to create a jquery post
        
        public function post($datas,$retsDiv,$file = "index.php"){
            $retsrets = PIP_str::random(10);
            return '
                    $("'.$retsDiv.'").html(LOADING_S_);
                    $.post('.$file.',
                          '.$datas.',
                           function('.$retsrets.'){
                        $("'.$retsDiv.'").html('.$retsrets.');   
                    });';
        }
        
        // a function to return the width of the screen 
        
        public function Swidth($varName){
            return 'var '.$varName.' = window.innerWidth;';
        }
        
        // a function to produce buttons script
        
        public function AjaxBtn(){
            return new class{
                private $HiddingCss = [];
                private $loadingCss = "";
                
                private $dataRetr = [];
                
                private $type = "post";
                
                private $funName = "funCalls";
                // the construcntor
                public function __construct(){
                    $this->dataRetr = pipArr();
                }
                // a function to specify all DOM to be hidden
                public function hide($name){
                    if(!contains_arr($this->HiddingCss,$name)){
                        array_push($this->HiddingCss,$name);
                    }
                    return $this;
                }
                // a function used to specify http request method post or get
                public function method($type){
                    $this->type = $type;
                    return $this;
                }
                // a function to specify the DOM to be displayed
                public function show($name){
                    $this->loadingCss = $name;
                    return $this;
                }
                public function data($htmlAttr,$postName){
                    if($this->dataRetr->height()>0)
                        $this->dataRetr = $this->dataRetr->_add_(pipArr([["html"=>$htmlAttr,"ajax"=>$postName]]));
                    else $this->dataRetr = pipArr([["html"=>$htmlAttr,"ajax"=>$postName]]);
                    $this->$postName = http($postName)->val();
                    return $this;
                }
                public function set(){
                    for($ii=0;$ii<$this->dataRetr->height();$ii++){
                        if(!http($this->dataRetr->JS($ii)->ajax)->set()) return false;   
                    }
                    return true;
                }
                public function val($name){
                    return http($name)->val();
                }
                public function script($buttonCss,$file="index.php"){
                    
                    $hider = "";
                    for($ii=0;$ii<sizeof($this->HiddingCss);$ii++){
                        $hider .= '$("'.$this->HiddingCss[$ii].'").slideUp(100);';
                    }
                    
                    $data_retriver = "";
                    $data_sends = "";
                    //$this->dataRetr->filterthis_distinct("html")->List();
                    $this->dataRetr = $this->dataRetr->filterthis_distinct("html");
                    for($ii=0;$ii<$this->dataRetr->height();$ii++){
                        $varName = PIP_str::random(10);
                        $data_retriver .= 'var '.$varName.' = $(this).attr("'.$this->dataRetr->JS($ii)->html.'");';
                        $data_sends .= $this->dataRetr->JS($ii)->ajax.'=${'.$varName.'}';
                        
                        if($ii<($this->dataRetr->height()-1)){
                            $data_sends .= "&";
                        }
                    }
                    
                    
                    $eventHand = PIP_str::random(10);
                    $var_inside = ' function '.$this->funName.'() {
                                    $("'.$buttonCss.'")
                                    .off("click")
                                    .click( function('.$eventHand.'){
                                        '.$eventHand.'.preventDefault();
                                        '.$hider.'
                                        $("'.$this->loadingCss.'").slideDown();
                                        $("'.$this->loadingCss.'").html(LOADING_S_);
                                        '.$data_retriver.';
                                        $.post("'.$file.'",
                                               `'.$data_sends.'`,
                                               function('.$eventHand.'){
                                                    $("'.$this->loadingCss.'").html('.$eventHand.');
                                        });
                                });
                               }
                               '.$this->funName.'();';
                    return js()->tag($var_inside);
                }
                // a function to initialize DOM
                public function init(){
                    $rets = "";
                    for($ii=0;$ii<sizeof($this->HiddingCss);$ii++){
                        $rets .= '$("'.$this->HiddingCss[$ii].'").slideDown(100);';
                    }
                    
                    $rets .= '$("'.$this->loadingCss.'").slideUp();
                              $("'.$this->loadingCss.'").html("");
                              '.$this->funName.'();';
                    return js()->tag($rets);
                    
                }
                
                public function function($name){
                    $this->funName = $name;
                    return $this;
                }
            };
        }
        // a function to produce a button with to cancel the process of ajax 
        public function cancelBtn(){
            return new class{
                private $HiddingCss = [];
                private $loadingCss = "";
                public function __construct(){
                    $this->id = PIP_str::random(10);
                }
                // a function to specify all DOM to be hidden
                public function hide($name){
                    if(!contains_arr($this->HiddingCss,$name)){
                        array_push($this->HiddingCss,$name);
                    }
                    return $this;
                }
                // a function to specify the DOM to be displayed
                public function show($name){
                    $this->loadingCss = $name;
                    return $this;
                }
                // a function to produce a button 
                public function html($name = "",$class = ""){
                    if(pipStr($name)->length()===0) $name = "Cancel";
                    return '<button class="btn btn-info btn-flat '.$class.'" id="'.$this->id.'"> '.$name.'</button>';
                }
                public function a($name = "",$class = ""){
                    if(pipStr($name)->length()===0) $name = "Cancel";
                    return '<a href="#" class="'.$class.'" id="'.$this->id.'"> '.$name.'</a>';
                }
                // a function to generate htmls
                public function js(){
                    $eventName = PIP_str::random(10);
                    $hider = "";
                    for($ii=0;$ii<sizeof($this->HiddingCss);$ii++){
                        $hider .= '$("'.$this->HiddingCss[$ii].'").slideDown(100);';
                    }
                    $insider = '$("#'.$this->id.'")
                                        .off("click")
                                        .click( function('.$eventName.'){
                                          '.$eventName.'.preventDefault();
                                          '.$hider.'
                                          $("'.$this->loadingCss.'").slideUp(100).html("");
                                });';
                    return js()->tag($insider);
                }
            };
        }
        
        public function pages(){
            return new class {
                // the division or any other jquery selector, but most of the time is a division
                private $returnDiv;
                // the main name of the key word that will be sent in the request as a the current index
                private $extra;
                // a variable to keep total number of items available
                private $total_items = 0;
                // a variable to keep a number of items on single page
                private $number_of_items = 3;
                // a variable to keep number of page number to display and others will be hidden
                private $number_of_pages = 3;
                // a variable for additional data to send from serialized template::ajaxdata 
                private $data_to_send = "";
                // values to use in data to send with name and values as arrays
                private $data_values = [];
                private $data_names = [];
                // a file that will recieve and handle the pagination request
                private $file = "index.php";
                private $class = "";
                private $codes = [];
                
                function __construct(){
                    $this->name = PIP_str::random(10);
                }
                
                function div($name){
                    $this->returnDiv = $name;
                    return $this;
                }
                
                function file($name){
                    $this->file = $name;
                    return $this;
                }
                
                function data($name,$value){
                    if(!contains_arr($this->data_names,$name)){
                        array_push($this->data_names,$name);
                        array_push($this->data_values,$value);
                        $this->$name = http($name)->val();
                    }
                    $this->data_to_send = template::AjaxData($this->data_names,$this->data_values);
                    return $this;
                }
                
                function TotalItems($number){
                    $this->total_items = $number;
                    return $this;
                }
                
                function itemPerpage($number){
                    $this->number_of_items = $number;
                    return $this;
                }
                
                function pageNumber($number){
                    $this->number_of_pages = $number;
                    return $this;
                }
                
                function index($extra){
                    $this->extra = $extra;
                    return $this;
                }
                
                function addClass($name){
                    $this->class .= $name;
                    return $this;
                }
                
                function removeClass($name){
                    $this->class = pipStr($this->class)->remove($name)->str();
                    return $this;
                }
                
                function html(){
                    // first checking if the given total items and number of items on the page are valid
                    if((!is_numeric($this->total_items))||(!is_numeric($this->number_of_items)))
                        return template::danger("??? ERROR DUE TO INVALID PAGINATION INPUT ???");
                    // to check whether given number of items are greater than number of items on the page then return non breaking space
                    else if(($this->total_items<=$this->number_of_items)||($this->number_of_items==0)||($this->total_items==0))
                        return template::space(1);
                    else {
                        $pageNums = intval(intval($this->total_items)/intval($this->number_of_items));
                        $rets = '<ul class="pagination '.$this->class.'">';
                        if(($pageNums+1)>$this->number_of_pages)
                            $rets .='<li class="page-item skip-pages-'.$this->name.'-back"><a href="#" class="page-link">«</a></li>';
                        $pages = 1;
                        for($ii=0;$ii<=$pageNums;$ii++){
                           $rets .= '<li class="page-item';
                            if(($ii+1)>$this->number_of_pages){
                               $rets .= ' hidden-page-num'.$this->name.'';
                            } else {
                               $rets .= ' visible-page-num'.$this->name.''; 
                            }

                            if($ii==0)
                               $rets .= ' active'; 

                            $rets .= ' pages-'.$this->name.' page-num-'.$this->name."-".$this->name.'" data-index="'.($ii+1).'" >
                                    <a href="#" class="page-link">'.($ii+1).'</a></li>'; 
                            if((($ii+1)%$this->number_of_pages)==0) $pages++;
                        }
                        if(($pageNums+1)>$this->number_of_pages)
                            $rets .= '<li class="page-item skip-pages-'.$this->name.'-fwd">
                                            <a href="#" class="page-link">»</a>
                                      </li>
                                </ul>';

                    }
                    return $rets;
                }
                
                function js(){
                    return '<script>
                        var active_'.$this->name.' = 1;
                        var page_num_'.$this->name.' = 1;
                        $(".skip-pages-'.$this->name.'-fwd").click( function(vb){
                            vb.preventDefault();
                            $(".page-num-"+page_num_'.$this->name.'+"-'.$this->name.'")
                                    .addClass("hidden-page-num'.$this->name.'")
                                    .removeClass("visible-page-num'.$this->name.'");
                            page_num_'.$this->name.'++;
                            $(".page-num-"+page_num_'.$this->name.'+"-'.$this->name.'")
                                    .removeClass("hidden-page-num'.$this->name.'")
                                    .addClass("visible-page-num'.$this->name.'");
                            $(".hidden-page-num'.$this->name.'").hide();
                            $(".visible-page-num'.$this->name.'").show();
                        });
                        
                        $(".skip-pages-'.$this->name.'-back").click( function(vb){
                            vb.preventDefault();
                            $(".page-num-"+page_num_'.$this->name.'+"-'.$this->name.'")
                                    .addClass("hidden-page-num'.$this->name.'")
                                    .removeClass("visible-page-num'.$this->name.'");
                            page_num_'.$this->name.'--;
                            $(".page-num-"+page_num_'.$this->name.'+"-'.$this->name.'")
                                    .removeClass("hidden-page-num'.$this->name.'")
                                    .addClass("visible-page-num'.$this->name.'");
                            $(".hidden-page-num'.$this->name.'").hide();
                            $(".visible-page-num'.$this->name.'").show();
                        });
                        
                        $(".hidden-page-num'.$this->name.'").hide();
                        $(".visible-page-num'.$this->name.'").show();
                        $(".pages-'.$this->name.'").off("click").click( function(bn){
                            bn.preventDefault();
                            $(".pages-'.$this->name.'").removeClass("active");
                            var indexes = $(this).attr("data-index");
                            active_'.$this->name.' = parseInt(indexes);
                            $(this).addClass("active");
                            $("'.$this->returnDiv.'").html(LOADING_S_);
                            $.post("'.$this->file.'",
                                   "'.$this->extra.'="+indexes+"&'.$this->data_to_send.'",
                                   function(rets){
                                $("'.$this->returnDiv.'").html(rets);
                            });
                        })
                    </script>';
                }
                
                function set(){
                    for($ii=0;$ii<sizeof($this->data_names);$ii++) if(!http($this->data_names[$ii])->set()) return false;
                    return http($this->extra)->set();
                }
                
                function val(){
                    $sents = http($this->extra)->val();
                    if(intval($sents)===0) return 0;
                    return (($sents-1)*$this->number_of_items);
                }
                
            };
        }
        
        public function variable($name,$value){
            if(is_array($value)) $value = json_encode($value);
            return $this->tag("let $name = $value");
        }
        
        public function var($name,$value){
            if(is_array($value)) $value = json_encode($value);
            return $this->tag("var $name = $value");
        }
        
        
    }

    function js(){
        return new JavaScript();
    }

    // a class that will deal with html representation of the data given
    class _HTML{
        private $theCurs = null;
        function __construct($EL){
            $this->theCurs = $EL;
        }
        // a function to return a list of options in html select element with value of the ids and display from $display given
        // and a current element of from $current
        public function options($display,$current = 0){
            $rets = "";
            $selected = "";
            
            while($this->theCurs->next()){
               if(intval($current)==intval($this->theCurs->id()))
                   $selected = "selected";
               $DISPL = "";
               if(is_array($display)){
                  for($ii=0;$ii<sizeof($display);$ii++){
                      $the_disp = $display[$ii];
                      $DISPL .= " ".$this->theCurs->JS()->$the_disp;
                  } 
               } else $DISPL = $this->theCurs->JS()->$display;
               $rets .= '<option value="'.$this->theCurs->id().'" '.$selected.' >'.$DISPL.'</option>';
            }
            return $rets;
        }
        // the same function with the above but with random value
        public function options_($display,$current,$value){
            $rets = "";
            $selected = "";
            while($this->theCurs->next()){
               if(intval($current)==intval($this->theCurs->JS()->$value))
                   $selected = "selected";
               $DISPL = "";
               if(is_array($display)){
                  for($ii=0;$ii<sizeof($display);$ii++){
                      $DISPL .= " ".$this->theCurs->JS()->$display[$ii];
                  } 
               } else $DISPL = $this->theCurs->JS()->$display;
               $rets .= '<option value="'.$this->theCurs->JS()->$value.'" '.$selected.' >'.$DISPL.'</option>';
            }
            return $rets;
        }
        // a function to deal with all possiblie html tags
        public function tags(){
            return new class{
                 private $EL = NULL;
                 function __construct(){
                     $$this->EL = [
                         "must"=>[
                             [
                                 "name"=>"html",
                                 "type"=>0,
                                 "parent"=>[],
                                 "index"=>0
                             ],
                             [
                                 "name"=>"head",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["must"][0]],
                                 "index"=>1
                             ],
                             [
                                 "name"=>"body",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["must"][0]],
                                 "index"=>2
                             ]
                         ],
                         "form"=>[
                             //form
                             [
                                 "name"=>"form",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["must"][2],
                                            $this->TAGS["layout"][0],
                                            $this->TAGS["layout"][1],
                                            $this->TAGS["layout"][2],
                                            $this->TAGS["layout"][3],
                                            $this->TAGS["layout"][4],
                                            $this->TAGS["layout"][5]
                                           ],
                                 "index"=>0
                             ],
                             //input
                             [
                                 "name"=>"input",
                                 "type"=>1,
                                 "parent"=>[$this->TAGS["must"][2],
                                            $this->TAGS["form"][0],
                                            $this->TAGS["layout"][0],
                                            $this->TAGS["layout"][1],
                                            $this->TAGS["layout"][2],
                                            $this->TAGS["layout"][3],
                                            $this->TAGS["layout"][4],
                                            $this->TAGS["layout"][5]
                                           ],
                                 "index"=>1
                             ],
                             // select
                             [
                                 "name"=>"select",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["must"][2],
                                            $this->TAGS["form"][0],
                                            $this->TAGS["layout"][0],
                                            $this->TAGS["layout"][1],
                                            $this->TAGS["layout"][2],
                                            $this->TAGS["layout"][3],
                                            $this->TAGS["layout"][4],
                                            $this->TAGS["layout"][5]
                                           ],
                                 "index"=>2
                             ],
                             // option
                             [
                                 "name"=>"option",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["form"][2]],
                                 "index"=>3
                             ],
                             //optgroup
                             [
                                 "name"=>"optgroup",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["form"][2]],
                                 "index"=>4
                             ],
                             //textarea
                             [
                                 "name"=>"textarea",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["must"][2],
                                            $this->TAGS["form"][0],
                                            $this->TAGS["layout"][0],
                                            $this->TAGS["layout"][1],
                                            $this->TAGS["layout"][2],
                                            $this->TAGS["layout"][3],
                                            $this->TAGS["layout"][4],
                                            $this->TAGS["layout"][5]
                                           ],
                                 "index"=>5
                             ],
                             //button
                             [
                                 "name"=>"button",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["must"][2],
                                            $this->TAGS["form"][0],
                                            $this->TAGS["layout"][0],
                                            $this->TAGS["layout"][1],
                                            $this->TAGS["layout"][2],
                                            $this->TAGS["layout"][3],
                                            $this->TAGS["layout"][4],
                                            $this->TAGS["layout"][5]
                                           ],
                                 "index"=>6
                             ],
                             //fieldset
                             [
                                 "name"=>"fieldset",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["must"][2],
                                            $this->TAGS["form"][0],
                                            $this->TAGS["layout"][0],
                                            $this->TAGS["layout"][1],
                                            $this->TAGS["layout"][2],
                                            $this->TAGS["layout"][3],
                                            $this->TAGS["layout"][4],
                                            $this->TAGS["layout"][5]
                                           ],
                                 "index"=>7
                             ],
                             //legend
                             [
                                 "name"=>"legend",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["form"][0],
                                            $this->TAGS["form"][7],
                                            $this->TAGS["layout"][0],
                                            $this->TAGS["layout"][1],
                                            $this->TAGS["layout"][2],
                                            $this->TAGS["layout"][3],
                                            $this->TAGS["layout"][4],
                                            $this->TAGS["layout"][5]
                                           ],
                                 "index"=>8
                             ],
                             //label
                             [
                                 "name"=>"label",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["form"][0],
                                            $this->TAGS["form"][7],
                                            $this->TAGS["layout"][0],
                                            $this->TAGS["layout"][1],
                                            $this->TAGS["layout"][2],
                                            $this->TAGS["layout"][3],
                                            $this->TAGS["layout"][4],
                                            $this->TAGS["layout"][5]
                                           ],
                                 "index"=>9
                             ]
                         ],
                         "layout"=>[
                             //div
                             [
                                 "name"=>"div",
                                 "type"=>0,
                                 "parent"=>[
                                     $this->TAGS["must"][2],
                                     $this->TAGS["form"][0],
                                     $this->TAGS["form"][6],
                                     $this->TAGS["form"][7],
                                     $this->TAGS["form"][8],
                                     $this->TAGS["form"][9],
                                     $this->TAGS["layout"][0],
                                     $this->TAGS["layout"][1],
                                     $this->TAGS["layout"][2],
                                     $this->TAGS["layout"][3],
                                     $this->TAGS["layout"][4],
                                     $this->TAGS["layout"][5]
                                 ],
                                 "index"=>0
                             ],
                             //span
                             [
                                 "name"=>"span",
                                 "type"=>0,
                                 "parent"=>[
                                     $this->TAGS["must"][2],
                                     $this->TAGS["form"][0],
                                     $this->TAGS["form"][6],
                                     $this->TAGS["form"][7],
                                     $this->TAGS["form"][8],
                                     $this->TAGS["form"][9],
                                     $this->TAGS["layout"][0],
                                     $this->TAGS["layout"][1],
                                     $this->TAGS["layout"][2],
                                     $this->TAGS["layout"][3],
                                     $this->TAGS["layout"][4],
                                     $this->TAGS["layout"][5]
                                 ],
                                 "index"=>1
                             ],
                             //nav
                             [
                                 "name"=>"nav",
                                 "type"=>0,
                                 "parent"=>[
                                     $this->TAGS["must"][2],
                                     $this->TAGS["form"][0],
                                     $this->TAGS["form"][6],
                                     $this->TAGS["form"][7],
                                     $this->TAGS["form"][8],
                                     $this->TAGS["form"][9],
                                     $this->TAGS["layout"][0],
                                     $this->TAGS["layout"][1],
                                     $this->TAGS["layout"][3],
                                     $this->TAGS["layout"][4],
                                     $this->TAGS["layout"][5]
                                 ],
                                 "index"=>2
                             ],
                             //header
                             [
                                 "name"=>"header",
                                 "type"=>0,
                                 "parent"=>[
                                     $this->TAGS["must"][2]
                                 ],
                                 "index"=>3
                             ],
                             //footer
                             [
                                 "name"=>"footer",
                                 "type"=>0,
                                 "parent"=>[
                                     $this->TAGS["must"][2]
                                 ],
                                 "index"=>4
                             ],
                             //section
                             [
                                 "name"=>"section",
                                 "type"=>0,
                                 "parent"=>[
                                     $this->TAGS["must"][2],
                                     $this->TAGS["form"][0],
                                     $this->TAGS["form"][6],
                                     $this->TAGS["form"][7],
                                     $this->TAGS["form"][8],
                                     $this->TAGS["form"][9],
                                     $this->TAGS["layout"][0],
                                     $this->TAGS["layout"][1],
                                     $this->TAGS["layout"][2],
                                     $this->TAGS["layout"][3],
                                     $this->TAGS["layout"][4],
                                     $this->TAGS["layout"][5]
                                 ],
                                 "index"=>5
                             ]
                         ],
                         "text"=>[
                             //p
                             [
                                 "name"=>"p",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["layout"]["*"]],
                                 "index"=>0
                             ],
                             //hn
                             [
                                 "name"=>["h1","h2","h3","h4","h5","h6"],
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["layout"]["*"]],
                                 "index"=>1
                             ],
                             //i
                             [
                                 "name"=>"i",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["layout"]["*"]],
                                 "index"=>2
                             ],
                             //b
                             [
                                 "name"=>"b",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["layout"]["*"]],
                                 "index"=>3
                             ],
                             //u
                             [
                                 "name"=>"u",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["layout"]["*"]],
                                 "index"=>4
                             ],
                             //s
                             [
                                 "name"=>"s",
                                 "type"=>0,
                                 "parent"=>[$this->TAGS["layout"]["*"]],
                                 "index"=>5
                             ],
                             //strike
                            [
                                "name"=>"strike",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>6
                            ],
                            //tt
                            [
                                "name"=>"tt",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>7
                            ],
                            //sup
                            [
                                "name"=>"sup",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>8
                            ],
                            // sub
                            [
                                "name"=>"sub",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>9
                            ],
                            //big
                            [
                                "name"=>"big",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>10
                            ],
                            //small
                            [
                                "name"=>"small",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>11
                            ],
                            //hr
                            [
                                "name"=>"hr",
                                "type"=>1,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>12
                            ],
                            //em
                            [
                                "name"=>"em",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>13
                            ],
                            // strong
                            [
                                "name"=>"strong",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>14
                            ],
                            //address
                            [
                                "name"=>"address",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>15
                            ],
                            //abbr
                            [
                                "name"=>"abbr",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>16
                            ],
                            //acronym
                            [
                                "name"=>"acronym",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>17
                            ],
                            //dfn
                            [
                                "name"=>"dfn",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>18
                            ],
                            //blockquote
                            [
                                "name"=>"blockquote",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>19
                            ],
                            //q
                            [
                                "name"=>"q",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>20
                            ],
                            //cite
                            [
                                "name"=>"cite",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>21
                            ],
                            //code
                            [
                                "name"=>"code",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>22
                            ],
                            //kbd
                            [
                                "name"=>"kbd",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>23
                            ],
                            //var
                            [
                                "name"=>"var",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>24
                            ],
                            //samp
                            [
                                "name"=>"samp",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>25
                            ],
                            //pre
                            [
                                "name"=>"pre",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>26
                            ],
                            //br
                            [
                                "name"=>"br",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>27
                            ],
                            // font
                            [
                                "name"=>"font",
                                "type"=>0,
                                "parent"=>[$this->TAGS["layout"]["*"]],
                                "index"=>28
                            ]
                        ],
                         "list"=>[
                            //ul
                            [
                                "name"=>"ul",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>0
                            ],
                            //ol
                            [
                                "name"=>"ol",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>1
                            ],
                            //li
                            [
                                "name"=>"li",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>2
                            ],
                            //dl
                            [
                                "name"=>"dl",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>2
                            ],
                            //dt
                            [
                                "name"=>"dt",
                                "type"=>0,
                                "parent"=>[$this->TAGS["list"][3]],
                                "index"=>2
                            ],
                            //dd
                            [
                                "name"=>"dd",
                                "type"=>0,
                                "parent"=>[$this->TAGS["list"][3]],
                                "index"=>2
                            ],
                        ],
                         "table"=>[
                            //table
                            [
                                "name"=>"table",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>0
                            ],
                            // thead
                            [
                                "name"=>"thead",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>1
                            ],
                            //tbody
                            [
                                "name"=>"tbody",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>2
                            ],
                            //tfoot
                            [
                                "name"=>"tfoot",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>3,
                            ],
                            // tr
                            [
                                "name"=>"tr",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>4
                            ],
                            // td
                            [
                                "name"=>"td",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>5
                            ]
                        ],
                         "media"=>[
                            //img
                            [
                                "name"=>"img",
                                "type"=>1,
                                "parent"=>[],
                                "index"=>0
                            ],
                            // video
                            [
                                "name"=>"video",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>1
                            ],
                            // audio
                            [
                                "name"=>"audio",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>2
                            ],
                            //canvas
                            [
                                "name"=>"canvas",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>3
                            ],
                            // source
                            [
                                "name"=>"source",
                                "type"=>1,
                                "parent"=>[$this->TAGS["media"][1],$this->TAGS["media"][2]],
                                "index"=>4
                            ],
                            //track
                            [
                                "name"=>"track",
                                "type"=>1,
                                "parent"=>[$this->TAGS["media"][1],$this->TAGS["media"][2]],
                                "index"=>5
                            ]
                        ],
                         "link"=>[
                            //a
                            [
                                "name"=>"a",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>0
                            ],
                            // link
                            [
                                "name"=>"link",
                                "type"=>1,
                                "parent"=>[],
                                "index"=>1
                            ]
                        ],
                         "external"=>[
                            //script
                            [
                                "name"=>"script",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>0
                            ],
                            // style
                            [
                                "name"=>"style",
                                "type"=>0,
                                "parent"=>[],
                                "index"=>1
                            ]
                        ]
                    ];
                 }
                 public function tag($name){
                     
                 }
            };
        }
        //a function to return the javascript version of the PIP_Array
        public function JavaScript($name,$type = 0){
            $fields = $this->theCurs->el()->fields();
            if($type)
              return "var $name = new PIP_Array(".$this->theCurs->el()->JV.",$fields);";
            return "<script> var $name = new PIP_Array(".$this->theCurs->el()->JV.",$fields); </script>";
        }
        // a function for select instead of options
        public function select($name,$display,$current = 0){
            return '<select name="'.$name.'">'.$this->options($display,$current)."</select>";
        }
        
        public function select_($name,$display,$current,$value){
            return '<select name="'.$name.'">'.$this->options_($display,$current,$value)."</select>";
        }
    }
    
    // class designed for authentication of users
    class auth {
        public $found = false;
        private $messag = "the new authentication object";
        // the constructor from the submitted html form the header is the name to be found in the form
        function __construct($header, $value = ""){
            if(http($header)->set())
                if(pipStr($value)->length()&&http($header)->val()==$value){
                    $this->found = true;
                }
                    
        }
        // a function to set and return the message
        function Smessage($str = ""){
            if(pipStr($str)->length())
                $this->messag = $str;
            return $this->messag;
        }
        // a function to extend a message
        function Amessage($str){
            $this->messag .= " :: ".$str;
            return $this;
        }
        // a function to check the loged in user in the list
        public function users($userList){
            if(is_array($userList)){
                for($ii=0;$ii<sizeof($userList);$ii++){
                    if(isset($userList[$ii]))
                        if(!($userList[$ii] instanceof PIPCLENTS)){
                            throw new Exception('the user function is allowed only to accept an array of :: PIPCLENTS object :: on the '.PIP_number($ii)->Ordinal().' index of the array');
                            return -100;
                        } else {
                            if($userList[$ii]->logged_in){
                                return $ii;
                            }
                        }
                }
                return -1;
            } else {
                if($userList instanceof PIPCLENTS){
                    return $this->users([$userList]);
                } else {
                  throw new Exception('the user function is allowed only to accept a PIPCLENTS object only');
                  return -100;   
                }
            }
        }
        public function users_($userList,$fun){
            if(is_array($userList)){
                for($ii=0;$ii<sizeof($userList);$ii++){
                    if(isset($userList[$ii]))
                        if(!($userList[$ii] instanceof PIPCLENTS)){
                            throw new Exception('the user function is allowed only to accept an array of :: PIPCLENTS object :: on the '.PIP_number($ii)->Ordinal().' index of the array');
                            $fun(-100);
                            return -100;
                        } else {
                            if($userList[$ii]->logged_in){
                                $fun($ii);
                                return $ii;
                            }
                        }
                }
                $fun(-1);
                return -1;
            } else {
                throw new Exception('the user function is allowed only to accept an array only');
                $fun(-100);
                return -100;
            }
        }
        // a function to use for the user login
        public function login($db,$userList,$keyList,$inputList = "",$hash = true){
            if(!($db instanceof webApp)){
                $this->Smessage("System error: the given parameters must include the webApp object ");
                return false;
            }
            if(!is_array($userList)){
                return $this->login($db,[$userList],$keyList,$inputList,$hash);
            }
            else {
                for($ii=0;$ii<sizeof($userList);$ii++){
                    if(!($userList[$ii] instanceof PIPCLENTS)){
                       $this->Smessage("userlist must be a PIPCLENTS instance");
                       return false; 
                    }       
                }
            }
            if(is_array($keyList)&&is_array($inputList)){
                $conn =  $db->open();
                $INPUT_ARRAY = ["0"];
                
                for($ii=0;$ii<sizeof($keyList);$ii++){
                    
                    $keyList[$ii] = http($keyList[$ii]);
                    if(!$keyList[$ii]->set()){
                        $this->Smessage("System error: the given parameters(".$keyList[$ii]->name().") on the ".PIP_number($ii)->Ordinal()." index was not set");
                        return false;
                    } else {
                        
                       if(pipStr($keyList[$ii]->name())->comp("password")&&$hash){
                            $keyList[$ii]->val(md5($keyList[$ii]->val()));
                        }
                       $keyList[$ii]
                           ->val(stripslashes($keyList[$ii]->val()))
                           ->val(mysqli_real_escape_string($conn,$keyList[$ii]->val()));
                        array_push($INPUT_ARRAY,$keyList[$ii]->val());
                    }
                }
                $db->close();
                
                for($ii=0;$ii<sizeof($userList);$ii++){
                    if(isset($userList[$ii]))
                        if($userList[$ii]->count())
                            for($i=0;$i<sizeof($keyList);$i++){
                                if($userList[$ii]->validMail($keyList[$i]->val())){
                                    if($userList[$ii]->_gets_(["email"],[$keyList[$i]->val()])){
                                        if($userList[$ii]->login($INPUT_ARRAY)){
                                            $this->Smessage(" logged in success!! ");
                                            return true;
                                        } else {
                                            $this->Smessage($userList[$ii]->message);
                                            break;
                                        }
                                    } else {
                                        $this->Smessage("the credentials doesn't exist");
                                    }
                                } else {
                                    $this->Smessage("invalid email address");
                                }
                            }
                       else $this->Smessage("there was no user found !!");
                }
                return false;
            } 
            else if((pipStr($inputList)->length()==0)&&is_array($keyList)){
                return $this->login($db,$userList,$keyList,[0,1,1],$hash);
            } 
            else if((pipStr($inputList)->length()==0)&&(is_string($keyList))){
                return $this->login($db,$userList,[$keyList],[0,1,1],$hash);
            }
            else if(is_array($inputList)&&(is_string($keyList))){
                return $this->login($db,$userList,[$keyList],$inputList,$hash);
            }
            else {
                $this->Smessage(" invalid input ");
            }
        }
        //a public function of the accronym of login with non input list and hash
        public function loginH($db,$userList,$keyList){
            return $this->login($db,$userList,$keyList,"",1);
        } 
        // a function to use while logging out and the locs is the path to load after the logout succeded
        public function logout($userList,$locs=""){
            if(is_array($userList)){
               for($ii=0;$ii<sizeof($userList);$ii++){
                   if(!isset($userList[$ii])){
                       template::goto($locs);
                       break;
                   }
                   
                   if($userList[$ii] instanceof PIPCLENTS){
                       if($userList[$ii]->logged_in){
                           if($userList[$ii]->logout()){
                               template::goto($locs);
                               return true;   
                           } else return false;
                       }  
                   } else {
                       $this->Smessage("the element must be an instance of PIPCLIENTS oject");
                       return false;
                   }
                }
            } 
            else if($userList instanceof PIPCLENTS){
                return $this->logout([$userList],$locs);
            } 
            else {
                $this->Smessage("the element must be an instance of PIPCLIENTS oject");
                return false;
            }
        }
    }
    
    class page{
        
        private $name;
        private $locs;
        private $templates = [];
        private $auths;
        private static function locs_comp($locs){
            if($locs){
                if(is_numeric($locs)){
                    if(intval($locs)>0){
                        $comp = ""; 
                        for($ii=0;$ii<intval($locs);$ii++){
                           $comp .= "../";
                        }
                        return $comp;
                    } else page::locs_comp(false);   
                } else {
                    return page::locs_comp(false);
                }
            } else return "";
        }
        // the constuctor where $name is the name of the page and $locs is the number of subfolder to the root dir
        function __construct($name, $locs = 0){
            $this->name = $name;
            $this->locs = $locs;
        }
        function init(){
            if(!is_dir($this->name))
                mkdir($this->name);
            else {
                echo template::danger("the page $this->name you are trying to create there is a folder with similar name");
                return false; 
            }
            if(!is_file($this->name."/index.php")){
                if(!is_file($this->name."/$this->name.php")){
                    $fp = fopen($this->name."/$this->name.php",'w');
                    $locs = page::locs_comp($this->locs);
                    $headerFile = "modules/header.php";
                    
                    $constructargs = new  Arguments();
                    
                    $onCreateArgs = new functionContents();
                    $onCreateArgs->funCalls("onCreate","USERS",["name"=>"parent","call"=>"->","pref"=>""])
                                 ->funCalls("login",null,"this")
                                 ->var("names","test")
                                 ->var("welcome",12);
                    
//                    $constructargs->add("template_name","unkown")
//                        ->add("loginkey","login")
//                        ->add("loginValue","loginme");
                    
                    
                    $contents = $this->privateVar("locs",$locs)
                            .$this->privateVar("temp")
                            .$this->privateVar("auth")
                            .$this->privateVar("auth_name")
                            .$this->privateVar("auth_value")
                            .$this->privateVar("temp_name")
                            //.$this->constructor($constructargs)
                            .$this->publicFun("run")
                            .$this->publicFun("login")
                            .$this->publicFun("logout")
                            .$this->publicFun("visit",[],$onCreateArgs);
                    $data = $this->startScript()
                        .$this->createClass($contents,"pageCreator","pageRoot");
                    if (!@fwrite($fp,$data)) {
                       echo template::danger("Could not write to the file $this->name.php");
                       return false;
                    }
                    fclose($fp);
                    $fp = fopen($this->name."/index.php","w");
                    $data = $this->startScript()
                        .$this->grobalVar("locs",$locs)
                        ."include(\"$locs$headerFile\");page::runFile(\"$this->name\");"
                        .$this->call()
                        .$this->endScript();
                    if (!@fwrite($fp,$data)) {
                       echo template::danger("Could not write to the file index.php");
                       return false;
                    }
                    fclose($fp);
                    return true;
                } else {
                    echo template::danger("the page you are trying to create contain file $this->name.php that will not be overwritten");
                    return false;
                }
            }
            else {
                echo template::danger("the page you are trying to create contain file index.php that will not be overwritten");
                return false;
            }
        }
        // a function to add all template names for the purpose of defining here
        function  template($name){
            if(contains_arr($this->templates,$name)){
                $this->templates[index_arr($this->templates,$name)] = $name;
                return $this;
            } else array_push($this->templates,$name);
            return $this;
        }
        function auth($name,$value){
            if($this->auths instanceof PIP_Array){
                $this->auths->_add_(["$name","$value"],["name","value"]);
            }
            else {
                $this->auths = pipArr([
                    ["name"=>"$name",
                     "value"=>"$value"
                    ]
                ]);
            }
            return $this;
        }
        // a function of a class creator where contents are contents inside the class, parent is the parent class and extention are interfaces to extends from
        private function createClass($contents, $extentions = [],$parent = ""){
            $rets = "\n class $this->name";
            if(!($parent==""))
                $rets .= " extends $parent";
            if(is_array($extentions))
                for($ii=0;$ii<sizeof($extentions);$ii++){
                    if($ii)
                        $rets .= " implements ". $extentions[$ii];
                    else $rets .= " ,". $extentions[$ii];
                }
            else $rets .= " implements $extentions";
            return $rets."{\n $contents \n }";
        }
        private function privateVar($name,$value = ""){
            $rets = "\n \t private $$name";
            if($value==""){
                return $rets.";"; 
            }
            if(!is_numeric($value))
                return $rets." = \"$value\" ;";
            else return $rets." = $value;";
        }
        private function publicVar($name,$value = ""){
            $rets = "\n \t public $$name";
            if($value==""){
                return $rets.";"; 
            }
            if(!is_numeric($value))
                return $rets." = \"$value\" ;";
            else return $rets." = $value;";
        }
        
        private function publicFun($name,$args = [], $contents = []){
            if(is_array($args)&&is_array($contents))
                return "\n \t public function $name(){\n \t }";
            else if(($args instanceof Arguments)&&is_array($contents)){
                return "\n \t public function $name(".$args->compose()."){\n \t }";
            } 
            else if(($args instanceof Arguments)&&($contents instanceof functionContents)){
                return "\n \t public function $name(".$args->compose()."){\n \t ".$contents->compose()." \n }";
            }
            else if(is_array($args)&&($contents instanceof functionContents)){
                return "\n \t public function $name(){\n \t ".$contents->compose()." \n }";
            }
        }
        private function privateFun($name, $args = [], $contents = []){
            return "\n \t public function $name(){\n \t}";
        }
        
        private function constructor($args =  [], $contents = []){
            return $this->publicFun("__construct",$args,$contents);
        }
        
        private function call(){
            $rets = "\n $$this->name = "."new $this->name(";
            if(sizeof($this->templates)>1){
                
                for($ii=0;$ii<sizeof($this->templates);$ii++){
                    if($ii==0)
                        $rets .= "[";
                    $rets .= "\"".$this->templates[$ii]."\"";
                    if($ii<(sizeof($this->templates)-1))
                        $rets .= ",";
                    else $rets .= "]";
                }
            } else if(sizeof($this->templates)==1){
                $rets .= $this->templates[0];
            }
            
            if($this->auths instanceof PIP_Array){
                $rets .= ",";
                if($this->auths->height()>1){
                    $namesL = "";
                    $valuesL = "";
                    for($ii=0;$ii<$this->auths->height();$ii++){
                        if($ii==0){
                            $namesL .= "[";
                            $valuesL .= "[";
                        }
                       $namesL .= "\"".$this->auths->JS($ii)->name."\"";
                       $valuesL .= "\"".$this->auths->JS($ii)->value."\"";
                       if($ii<$this->auths->height()-1){
                           $namesL .= ",";
                           $valuesL .= ",";
                       } else {
                           $namesL .= "]";
                           $valuesL .= "]";
                       }
                    }
                    $rets .= $namesL.",".$valuesL;
                } else {
                   $rets .= '"'.$this->auths->JS()->name.'","'.$this->auths->JS()->value.'"'; 
                }
            }
            
            $rets .= "); "."  $$this->name"."->run()";
            return $rets;
        }
        
        private function startScript(){
            return "<?php \n";
        }
        
        private function endScript(){
            return "\n ?>";
        }
        
        private function grobalVar($name,$value){
            $rets = "\n $$name";
            if(is_numeric($value)){
                $rets .= " = $value;";
            } else {
                $rets .= " = \"$value\";";
            }
            return $rets; 
        }
        
        public static function runFile($fileName){
            $fp = fopen("$fileName.php","r");
            $all = fgets($fp); 
             while(!feof($fp)){ 
                 $all = $all.fgets($fp);
             }
             $all = str_replace("<?php"," ",$all);
             $all = str_replace("?>"," ",$all);
             $obj = eval($all);
        }
        
    }
    // a function to help in composition of the function arguments
    class Arguments{
        private $default;
        private $name;
        private $size;
        function __construct(){
            $this->name = [];
            $this->default = [];
            $this->size = 0;
        }
        
        function name(){
            return $this->name;
        }
        
        function compose(){
            $rets = "";
            for($ii=0;$ii<$this->size;$ii++){
                $rets .= " $".$this->name[$ii];
                if($this->default[$ii]!=null){
                    $rets .= " = \"".$this->default[$ii]."\"";
                }
                if($ii<($this->size-1))
                    $rets .= ",";
            }
            return $rets;
        }
        
        function add($name,$value = null){
            if(contains_arr($this->name,$name)){
                $this->default[index_arr($this->name,$name)] = $value;
                return $this;
            }
            array_push($this->name,$name);
            array_push($this->default,$value);
            $this->size++;
            return $this;
        }
        function remove($name = null){
            if($name==null){
                return $this->remove($this->name[$this->size-1]);
            }
            $index = index_arr($this->name,$name);
            if($index<0){
                return $this;
            }
            $this->name = remove_arr($this->name,$name);
            $this->default = Arguments::remove_arr_index($this->default,$index);
            return $this;
        }
        
        public static function remove_arr_index($arr,$index = null){
                if($index==null){
                    return remove_arr_index($arr,sizeof($arr)-1);
                }
                $new_arr = [];
                for($ii=0;$ii<sizeof($arr);$ii++){
                    if($ii!=$index){
                        array_push($new_arr,$arr[$ii]);
                    }
                }
                return $new_arr;
        }
    }
    // a function to help for composition of function contents
    class functionContents {
        // a local variable to mantain all variables
        private $variables = null;
        // a local variable to maintain all functions call 
        private $functionCalls = null;
        // a local variable to maintain all loops within a function
        private $loops = null;
        // a local variable to maintain all conditions like if 
        private $conditions = null;
        // a local variable to maintain all objects creation and its initiation
        private $objects = null;
        
        function __construct(){
            
        }
        
        function compose(){
            $rets = "";
            if(!($this->variables==null))
                for($ii=0;$ii<sizeof($this->variables);$ii++){
                    $rets .= "\n \t \t $".$this->variables[$ii]["name"];
                    if(!($this->variables[$ii]["value"]==null)){
                        if(is_numeric($this->variables[$ii]["value"])){
                           $rets .= " = ".$this->variables[$ii]["value"]; 
                        } else {
                            $rets .= " = \"".$this->variables[$ii]["value"]."\"";
                        }
                    }
                    $rets .= ";";
                }
            if(!($this->functionCalls==null))
                for($ii=0;$ii<sizeof($this->functionCalls);$ii++){
                    $rets .= "\n \t";
                    if(!($this->functionCalls[$ii]["object"]==null)){
                        if(is_array($this->functionCalls[$ii]["object"])){
                            if(!isset($this->functionCalls[$ii]["object"][0])){
                                $this->functionCalls[$ii]["object"] = [$this->functionCalls[$ii]["object"]];
                            }
                            for($i=0;$i<sizeof($this->functionCalls[$ii]["object"]);$i++){
                                $objsName;
                                $objsOps;
                                $prefix;
                                if(isset($this->functionCalls[$ii]["object"][$i]["call"])){
                                    $objsName = $this->functionCalls[$ii]["object"][$i]["name"];
                                    $objsOps = $this->functionCalls[$ii]["object"][$i]["call"];
                                    $prefix = $this->functionCalls[$ii]["object"][$i]["pref"];
                                } else {
                                    $objsName = $this->functionCalls[$ii]["object"][$i];
                                    $objsOps = "->";
                                    $prefix = "$";
                                }
                                if($i==0){
                                    $rets .="\n \t \t ".$prefix;
                                }
                                $rets .= $objsName.$objsOps;
                            }
                        }
                        else $rets .= "\n \t \t $".$this->functionCalls[$ii]["object"]."->";
                    }
                    $rets .= $this->functionCalls[$ii]["name"]."(";
                    if(!($this->functionCalls[$ii]["args"]==null)){
                        if(is_array($this->functionCalls[$ii]["args"])){
                            if(!isset($this->functionCalls[$ii]["args"][0])){
                                $this->functionCalls[$ii]["args"] = [$this->functionCalls[$ii]["args"]];
                            }
                            for($i=0;$i<sizeof($this->functionCalls[$ii]["args"]);$i++){
                                if(isset($this->functionCalls[$ii]["args"]["direct"])){
                                    if(is_numeric($this->functionCalls[$ii]["args"][$i]["name"])){
                                        $rets .= $this->functionCalls[$ii]["args"][$i]["name"];
                                    } else $rets .= '"'.$this->functionCalls[$ii]["args"][$i]["name"].'"';
                                } else {
                                    $rets .= '$'.$this->functionCalls[$ii]["args"][$i]["name"];
                                }
                                
                                if($i<(sizeof($this->functionCalls[$ii]["args"])-1)){
                                    $rets .= ",";
                                }
                            }
                        } else {
                            if(is_numeric($this->functionCalls[$ii]["args"]))
                                $rets .= $this->functionCalls[$ii]["args"];
                            else $rets .= '"'.$this->functionCalls[$ii]["args"].'"';
                        }
                    }
                    $rets .= ");";
                }
            return $rets;
        }
        
        function funCalls($names,$args = null, $objects = null){
            if($this->functionCalls==null)
                $this->functionCalls = [];
            array_push($this->functionCalls,["name"=>$names,"args"=>$args,"object"=>$objects]);
            return $this;
        }
        function var($name,$value = null){
            if($this->variables==null)
               $this->variables = [];
            array_push($this->variables,["name"=>$name,"value"=>$value]);
            return $this;
        }
    }
    // definition of all needed public functions to be defined
    interface pageCreator{
        public function login();
        public function logout();
        public function visit();
        public function onCreate($users = null);
        //private function onSearch($keys = "");
        //private function onGet($keys = "");
        //private function onPost($keys = "");
    } 
    
    // a class that will initialize all the information needed to start a page
    class pageRoot {
        // a variable to keep all needed authentication can be an array or a single auth datatype
        private $auth = null;
        // a variable to deal with all needed template can be an array or a single template instance
        private $template = null;
        // variables to specify if it there is multiple template and multiple authentication
        private $multipleTemplate = false;
        private $multipleAuth = false;
        // a local variable to keep the user of the page
        private $users;
        function __construct( $template_name = "unkown", $loginkey = "login", $loginValue = "loginme"){
            $this->init($template_name,$loginkey);
        }
        
        protected function onCreate($users = null){
            $this->users = $users;
        }
        
        protected function  templatesNumber(){
            if($this->multipleTemplate){
                return 1;
            } else {
                return sizeof($this->template)/2;
            }
        }
        
        protected function authNumber(){
            if($this->multipleAuth){
                return 1;
            } else {
                return sizeof($this->template)/2;
            }
        }
        
        protected function  template($name = ""){
            if($this->multipleTemplate){
                if($name==""){
                    return $this->template[0];
                } else {
                    return $this->template[$name];
                }
            } else return $this->template;
        }
        
        protected function auth($name = ""){
            if($this->multipleAuth){
                if($name==""){
                    return $this->auth[0];
                }
                else {
                    return $this->auth[$name];
                }
            } else return $this->auth;
        }
        
        
        private function init($template_name,$loginkey){
             if(is_array($template_name)){
                $this->multipleTemplate = true;
                 $this->template = [];
                for($ii=0;$ii<sizeof($template_name);$ii++){
                   array_push($this->template,new template($template_name[$ii]));
                   $this->template[$template_name[$ii]] = $this->template[$ii];
                }
             } else {
                $this->template = new template($template_name);
             }
             if(is_array($loginkey)){
                 $this->multipleAuth = true;
             }
        }
    }
    
    // a class that will implement the visiting process browser
    // the instance of the class must follow the definition of the root db
    class Browser {
        // the type of browser like Chrome, Mozilla,...
        private $type;
        // the type of OS 
        private $os;
        // the time of the visitor browser (in numeric format)
        private $time;
        // a time that the user last visited
        private $last_time;
        // the height of the window in the browser
        private $height;
        // the width of the window in the browser
        private $width;
        // a variable to keep the geolocation of the visitor
        private $geoLoc;
        // private js to use in browser
        private $js = "";
        
        
        function __construct(){
            $Swidth_var = PIP_str::random(10);
            $this->js = js()->tag(js()->Swidth($Swidth_var).
                                  js()->post(template::AjaxData(["Swidth"],[$Swidth_var])));
            
            
        }
        
        public function receive(){
            
        }
        
        public function js(){
            
        }
    }
    // a class that will deal with DOM elements on the page according to the usage
    class DOMelementb {
        private $name;
    }
    // a class that will implement view to simplify all about table childs and parents
    class View{
        private $name;
        
    }


    
?>
