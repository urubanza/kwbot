<?php
    class paths{
        private $first_path = NULL;
        private $path_list_format = "";
        function __construct(){
            $this->first_path = g("path")->last(4)->CR();
            $this->path_list_format = template::file("template/_paths/path_list.html");
            
        }
        
        function last(){
            $rets = pipStr();
            while($this->first_path->next()){
               $backs_u = pipStr($this->path_list_format);
               $rets->add($backs_u
                          ->replace("{{name}}",$this->first_path->JS()->path_name)
                          ->replace("{{ids}}",$this->first_path->id()));
            }
            return $rets->str();
        }
        
        function get($id){
            if($this->first_path->_gets_($id)->height()) return $this->first_path->_gets_($id);
            else return g("path")->_gets_($id);
        }
    }