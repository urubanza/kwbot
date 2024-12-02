<?php
    class template {
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
        
        private const pip_loop_key = "<pip::template::loops";
        private const pip_loop_end = "</pip::template::loops>";
        
        private const pip_files_key = "<pip::template::files";
        private const pip_files_end = "</pip::template::files>";
        
        private const pip_jsapi_key = "<pip::template::jsapi";
        private const pip_jsapi_end = "</pip::template::jsapi>";
        
        private const pip_form_key = "<pip::template::form";
        private const pip_form_end = "</pip::template::form>";
        
        private const dataStart = "{{{";
        private const dataEnds = "}}}";
        
        
        private $document;
        function __construct($title = NULL, $icon = NULL){
            if($title==NULL){
                $this->title = "NOT :: TITLED";
            } else {
                $this->title = $title;
            }
            $this->icon = $icon;
            $this->header_files = new PIP_Array([["name"=>$this->title]]);
            $this->footer_files = new PIP_Array([["name"=>$this->title]]);
            $this->body_files = new PIP_Array([["name"=>$this->title]]);
            $this->locs = "";
            $this->TAGS = [
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
                     $rets = "template file ".$this->locs.$this->dir."/".$this->header($ii)." not found!!";
                 }
                 
            }
            $rets = "\n\t<head >".$rets."</head>";
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
                     $new_rets = "template file ".$this->locs.$this->dir."/".$this->body($ii)." not found!!";
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
                    $new_rets = "template file ".$this->locs.$this->dir."/".$this->footer($ii)." not found!!";
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
                     $rets = "template file ".$this->locs.$this->dir."/".$this->header($ii)." not found!!";
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
                     $new_rets = "template file ".$this->locs.$this->dir."/".$this->body($ii)." not found!!";
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
                    $new_rets = "template file ".$this->locs.$this->dir."/".$this->footer($ii)." not found!!";
                }  
            }
            $this->document = $rets.$new_rets;
            $this->removeVariables();
            $this->removeLoops();
            return $this;
        }
        public function variables($index,$value){
            $this->variables[$index] = $value;
            return $this;
        }
        public function var($index,$value){
            return $this->variables($index,$value);
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
                 $rets = "template file ".$this->locs.$this->dir."/".$this->header($ii)." not found!!";
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
                        break;
                    }
                    default:{
                        return $this->realUrl(pipStr($str),$locs,3);
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
                    if($bounds["start"]->length()==0) return $str->str();
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
                        $new->add(template::danger($variable." variable not found"));
                    }
                    $ii = $i+$the_key_close->length()-1;
                } else {
                    $new->add($the_str->sub($ii));
                }
            }
            $this->document = $new->str();
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
        // a function to produce a continue button with functionality
        public static function continueButton($data_id,$file,$css_selector,$buttonName,$POSTname){
            return  
            '<button type="button" id="CONTINUE_REMOVES_'.$buttonName.$data_id.'" 
                     data-id="'.$data_id.'" 
                     class="btn btn-secondary btn-flat">
                     Continue
            </button>
            <script>
                $("#CONTINUE_REMOVES_'.$buttonName.$data_id.'").click( function(bn){
                    bn.preventDefault();
                    $("'.$css_selector.'").html(LOADING_S_);
                    var data_id = $(this).attr("data-id");
                    $.post("'.$file.'",
                           "'.$POSTname.'="+data_id,
                           function(retsxx){
                        $("'.$css_selector.'").html(retsxx);
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
        // a function to produce a pagination according to the given data
        public static function pages($name,$total = 0 , $itemNum = 3, $pageNum = 3, $size = ""){
            if((!is_numeric($total))||(!is_numeric($itemNum)))
                return "??";
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
        // a function to produce an edit script 
        public static function editScript($name,$file){
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
        // a function to return a hidden input to continue in saving some data
        public static function hiddenInput($name,$value,$type="number"){
            return '<input type="'.$type.'" value="'.$value.'" name="'.$name.'" style="display:none">';
        }
        // a function to return html document of this file
        public function doc(){
            return $this->document;
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
                throw new Exception('the user function is allowed only to accept an array only');
                return -100;
            }
            
        }
        // a function to use for the user login
        public function login($db,$userList,$keyList,$inputList = "",$hash = false){
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

