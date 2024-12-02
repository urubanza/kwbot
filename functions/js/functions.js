class webApp{
   constructor(Database){
       this.database = Database;
       this.message = "nothing wrong yet";
       this.err = false;
       this.hostName = "localhost";
       this.password_db = "";
       this.username_db = "root";
       var WEBAPP = this;
   }
   Smessage(message){
       this.message = message;
   }
   ShostName(hostname){
       this.hostName = hostname;
   }
   Spassword(password){
       this.password_db = password;
   }
   Susername(username){
       this.username_db = username;
   }
   _connection(){
       
   }
   connection(){
       
   }
}
class pipChat{
    constructor(iconUrl,topPos){
        // initially create 3 basic elements
        this.containerdiv = document.createElement("div");
        this.menudiv = document.createElement("div");
        this.taskbardiv = document.createElement("div");
        this.minimize = document.createElement("div");
        this.minimizeBtn = document.createElement("button");
        this.menudivIcon = new Image();
        this.menudivIcon.src = iconUrl;
        this.parentWidth = window.innerWidth;
        this.parentHeight = window.innerHeight;
        this.numberOfTasks = 0;
        this.totalTaskWidth = 0;
        this.minimizedTask = 0;
        this.taskbarVisibility = false;
        this.minimizedIds = new Array();
        this.nonminimizedIds = new Array();
        this.allelementsId = new Array();
        
        // container css
        this.containerdiv.style.position = 'fixed';
        this.containerdiv.style.width = (this.parentWidth)-100+"px";
        this.containerdiv.style.height = (this.parentHeight * 0.11)+'px';
        this.containerdiv.style.zIndex = '1000';
        this.containerdiv.style.top = topPos+'%';
        //menu div css
        this.menudiv.style.width = (this.parentWidth*0.05)+'px';
        this.menudiv.style.height = (this.parentHeight * 0.11)+'px';
        this.menudiv.style.boxShadow = '10px 0 30px black';
        this.menudiv.style.border = '2px solid white';
        this.menudiv.style.borderLeft = '0';
        this.menudiv.style.borderRadius =  '0 10px 10px 0';
        this.menudiv.style.padding = '20px';
        this.menudiv.style.display = 'inline';
        this.menudiv.style.opacity = '0.5';
        this.menudiv.style.cursor = 'pointer';
        //task bar css
        this.taskbardiv.style.display = 'inline';
        this.taskbardiv.style.padding = '9px';
        this.taskbardiv.style.boxShadow = '0 0 10px #1e7145';
        this.taskbardiv.style.width = '0px';
        this.taskbardiv.style.overflow = 'hidden';
        this.taskbardiv.style.opacity = '0';
        this.taskbardiv.style.zIndex = '1';
        //minimize bar css
        this.minimize.style.width = (this.parentWidth*0.26)+"px";
        this.minimize.style.position = 'absolute';
        this.minimize.style.boxShadow = "0 0 10px black";
        this.minimize.style.zIndex = "2000";
        this.minimize.style.display = "none";
        
        var minimizediv = $(this.minimize);
        minimizediv.addClass('w3-metro-darken');
        $(this.minimizeBtn).attr('class','w3-bar-item w3-button');
        $(this.minimizeBtn).html('<i class="fa fa-bars" style="font-size: 20px"></i>');
        $(this.minimizeBtn).click( function(cvv){
            cvv.preventDefault();
            minimizediv.fadeToggle();
            $('.messagewindows').hide();
        })
        
        // linking all elements
        this.menudiv.appendChild(this.menudivIcon);
        this.containerdiv.appendChild(this.menudiv);
        this.taskbardiv.appendChild(this.minimizeBtn);
        this.taskbardiv.appendChild(this.minimize);
        this.containerdiv.appendChild(this.taskbardiv);
        $('body').prepend(this.containerdiv);
    }
    setTaskbarWidth(widths){
        this.taskbardiv.style.width = widths;
    }
    getWhole(){
        return $(this.containerdiv);
    }
    getMenu(){
        return $(this.menudiv);
    }
    getTaskbar(){
        return $(this.taskbardiv);
    }
    MenuAddClass(Classes){
        $(this.menudiv).addClass(Classes);
    }
    taskBarAddClass(Classes){
        $(this.taskbardiv).addClass(Classes);
    }
    taskBarRemClass(Classes){
        $(this.taskbardiv).removeClass(Classes);
    }
    MenuRemClass(Classes){
        $(this.taskbardiv).removeClass(Classes);
    }
    taskbarRemoveElement(){   
    }
    taskBarAddElement(fileUrl,Icon,text,bagde){
        var parentsD = document.createElement("div");
        var contents = document.createElement("div");
        var button = document.createElement("button");
        parentsD.style.display = 'inline';
        $(parentsD).addClass('w3-metro-darken');
        $(contents).addClass('w3-metro-darken');
        $(contents).addClass('messagewindows');
        contents.style.position =  "absolute";
        contents.style.boxShadow = "0 0 10px black";
        $(button).addClass('w3-bar-item');
        $(button).addClass('w3-button');
        $(button).attr('id','buttons'+this.numberOfTasks);
        $(contents).attr('id','container'+this.numberOfTasks);
        $(parentsD).prepend(button);
        $(parentsD).prepend(contents);
        
        $(button).css("display","none");
        $(contents).css("display","none");
        
        
        $(this.taskbardiv).append(parentsD);
        var btnID = '#buttons'+this.numberOfTasks;
        var containerId = '#container'+this.numberOfTasks;
        $('#container'+this.numberOfTasks).load(fileUrl, function(){
             var realHEIGHT = $(containerId).height();
             contents.style.top = -(realHEIGHT-15)+"px";
        });
        $('#buttons'+this.numberOfTasks).html(Icon+text+bagde);
        contents.style.width = $('#buttons'+this.numberOfTasks).width()+"px";
        var minimized = this.minimize;
        $(btnID).click( function(ed)
                                { 
            ed.preventDefault();
            $(containerId).fadeToggle(200);
            $(minimized).fadeOut(200);
                                });
        var leftPos = ($(button).width()*(this.numberOfTasks)*1.1);
        contents.style.left = ((this.parentWidth*0.11)+leftPos)+"px";
          for(var xx=0; xx<=this.numberOfTasks;xx++){
             this.totalTaskWidth += $('#buttons'+xx).width();
          }
        this.allelementsId.push(this.numberOfTasks);
        if(this.totalTaskWidth>$(this.containerdiv).width()){
             $('#buttons'+this.minimizedTask).hide();
             $('#container'+this.minimizedTask).hide();
             var currentsMinimize = $(this.minimize).html();
             currentsMinimize += "<button class='w3-button pipWin' id='pipWin"+this.minimizedTask+"'>"+$('#buttons'+this.minimizedTask).html()+"</button>";
             $(this.minimize).html(currentsMinimize);
             var zzz = this.numberOfTasks-2
             this.minimize.style.top = - $(this.minimize).height()+"px";
             for(var tt = 0; tt<=this.numberOfTasks; tt++){
                 var ttt = tt+1;
                 var tttt = (tt - zzz);
                 var leftPos = ($('#buttons'+ttt).width()*(tttt)*1.1);
                 $('#container'+(ttt)).css('left',((this.parentWidth*0.11)+leftPos)+"px");
             }
             this.minimizedIds.push(this.minimizedTask);
             this.minimizedTask++;
             
            
        }
        var ddd = new Array();
        for(var k = this.minimizedIds.length; k < this.allelementsId.length; k++){
            ddd.push(this.allelementsId[k]);
        }
        this.nonminimizedIds = ddd;
        this.numberOfTasks++;
        this.taskbarFunctionality();
        this.taskbarSwitch();  
    }
    taskbarSwitch(){
        var hideIds = this.nonminimizedIds[0];
        var minimizedStuff = this.minimize;
        var thisTaskBar = this;
        $(".pipWin").click( function(exevcc){
             exevcc.preventDefault();
             $(minimizedStuff).hide();
             var thisIDS = $(this).attr('id');
             thisIDS = thisIDS.substring(6,thisIDS.length);
             $(this).removeClass('pipWin');
             $(this).attr('id','deleted'+thisIDS);
             $(this).html(''); $(this).hide();
             var thisHTMLparent = $(this).parent().html();
             thisHTMLparent += "<button class='w3-button pipWin' id='pipWin"+hideIds+"'>"+$('#buttons'+hideIds).html()+"</button>";
             $(this).parent().html(thisHTMLparent);
             $('#buttons'+thisIDS).show();
             $('#container'+thisIDS).show();
             $('#container'+thisIDS).css("left",$('#container'+hideIds).css('left'));
             $('#buttons'+hideIds).hide();
             $('#container'+hideIds).hide();
             thisTaskBar.taskBarUpdate(hideIds,thisIDS,thisTaskBar);
             thisTaskBar.taskbarSwitch();
         });
    }
    taskBarUpdate(minimized,nonminimized,TaskBar){
        for(var k = 0; k < this.minimizedIds.length; k++){
            if(nonminimized==this.minimizedIds[k])
                 {
                     this.minimizedIds[k] = minimized;
                     break; 
                 }
        }
        
        for(var k = 0; k < this.nonminimizedIds.length; k++){
                this.nonminimizedIds[k] = this.nonminimizedIds[k+1];          
        }
        this.nonminimizedIds[this.nonminimizedIds.length-1] = nonminimized;
    }
    taskbarOrganize(){
        var ddd = new Array();
        for(var k = this.minimizedIds.length; k < this.allelementsId.length; k++){
            ddd.push(this.allelementsId[k]);
        }
        this.nonminimizedIds = ddd;
        return ddd;
    }
    taskbarFunctionality(){
        var taskbar = $(this.taskbardiv);
        var visibility = this.taskbarVisibility;
        var minimizeBtn = $(this.minimizeBtn);
        var nonminimizedIds = this.nonminimizedIds;
        $(this.menudiv).off("click");
        $(this.menudiv).click( function(cc){
            if(visibility){
               visibility = false;
               taskbar.css('opacity','0');
               taskbar.css('z-index','1');
               minimizeBtn.hide();
                for(var t=0;t<nonminimizedIds.length;t++){
                    $("#buttons"+nonminimizedIds[t]).hide();
                    $("#container"+nonminimizedIds[t]).hide();
                }
            
            }else {
                visibility = true;
                taskbar.css('opacity','1');
                taskbar.css('z-index','2000');
                minimizeBtn.show();
                for(var t=0;t<nonminimizedIds.length;t++){
                    $("#buttons"+nonminimizedIds[t]).show();
                    $("#container"+nonminimizedIds[t]).show();
                }
            }
        })
        $(this.menudiv).hover( function(){
            $(this).css("opacity","1");
        }, function(){
            $(this).css("opacity","0.5");
        })
    }
}
class PipEffects {
   constructor(element){
        this.elemnt = document.getElementById(element);
        this.elemnt.style.position = "relative";
        //this.elemnt.style.transition = "all 0.2s";
    }
   swipe_Go(SpeedTime,Values,NextElemn){
            var elemnt = this.elemnt;
            var CommingElemnt = document.getElementById(NextElemn);
            var timevar =0;
            var xx = setInterval( function(){
                elemnt.style.left = '-'+Values[timevar]+'px';
                if(timevar>Values.length){
                    clearInterval(xx);
                    elemnt.style.display = "none";
                    elemnt.style.left = "0px";
                    CommingElemnt.style.display = "block";
                }
                timevar++;
            },SpeedTime)
        }
   swipe_back(SpeedTime,Values,NextElemn){
        this.elemnt.style.left = -Values[Values.length-1]+"px";
        var elemnt = this.elemnt;
        var CommingElemnt = document.getElementById(NextElemn);
        CommingElemnt.style.display = "none";
        var timevar = Values.length-1;
        var xx = setInterval( function(){
                if(timevar>=0)
                   elemnt.style.left = -Values[timevar]+'px';
                if(timevar==0){
                    clearInterval(xx);
                    elemnt.style.display = "block";
                    elemnt.style.left = "0px";
                }
                //console.log(Values[timevar]);
                timevar--;
            },SpeedTime)
    }
}

class PIP_DAT{
    constructor(CSS_SELECTOR){
        var PIP_ELEMEN = new PIP_ELEMENT(CSS_SELECTOR);
        var MONTHS = ["January","February","March","April","May","June","July","August","September","October","November","December"];
        var DAYS = ["Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday"];
        var today = new Date();
        this.element = () =>{
            return PIP_ELEMEN;
        }
        this.months = (index = 0) =>{
            var INDEX = index+1;
            if(INDEX>12){
                return "invalid input: Maximum value is 12";
            } else if(INDEX<0){
                return "invalid input: Minimum value is 1";
            } else return MONTHS[INDEX-1];
        }
        this.days = (index = 0) =>{
            var INDEX = index - 1;
            if(INDEX>7){
                return "invalid input: Maximum value is 7";
            } else if(INDEX<0){
                return "invalid input: Minimum value is 1";
            } else return DAYS[index];
        }
        this.now = (type = "year") =>{
            var STRS = today.getFullYear();
            switch(type){
                case "year":{
                    STRS = today.getFullYear();
                    break;
                }
                case "month":{
                    STRS = this.months(today.getMonth());
                    break;
                }
                case "date":{
                    STRS = today.getDate();
                    break;
                }
                case "hour":{
                    STRS = today.getHours();
                    break;
                }
                case "min":{
                    STRS = today.getMinutes();
                    break;
                }
                case "sec":{
                    STRS = today.getSeconds();
                    break;
                }
                
                case "msec":{
                    STRS = today.getMilliseconds();
                    break;
                }
                case "day":{
                    STRS = this.days(today.getDay());
                    break;
                }
                default :{
                    break;
                }
                
            }
            return STRS;
        }
        this.now_ = () =>{
            return today;
        }
    }
    display(type="none"){
        if(type=="none"){
           type = this.element().JS_ELEMENT().getAttribute("date-element");
        }
        this.element().HTML(this.now(type));
        function vbs(){
            //console.log("hhh");
        }
        vbs();
    }
    ego(DATE = "none"){
        if(DATE == "none"){
            DATE = this.element().JS_ELEMENT().getAttribute("date-data");
        }
        var countDownDate = new Date(DATE).getTime();
        var THIS = this;
        function getInt(){
            var distance = THIS.now_().getTime() - countDownDate;
              var seconds = Math.floor((distance % (1000 * 60)) / 1000);
              var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
              var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
              var days = Math.floor(distance / (1000 * 60 * 60 * 24));
              var months = Math.floor(days/30);
              var years = Math.floor(months/12);
              
              var finals = "";
              var ego_disp = true;
              if(years>0){
                  finals = years + " year";
                  if(years>1){
                      finals = finals + "s";
                  }   
              }
              else if(months>0){
                  finals = months + " month";
                  
                  if(months>1){
                      finals = finals + "s";
                  }      
              }
              else if(days>0){
                  finals = days + " day"
                  if(days>1){
                      finals = finals + "s";
                  }      
              }
              else if(hours>0){
                  finals = hours + " hour"
                  if(hours>1){
                      finals = finals + "s";
                  }      
              }
              else if(minutes>0){
                  finals = minutes + " minute"
                  if(minutes>1){
                      finals = finals + "s";
                  }      
              }
              else if(distance) {
                  finals = " Just Now";
                  ego_disp = false;
              }
              else {
                  finals = " just now !";
                  ego_disp = false;

              }
              if(ego_disp) 
                  finals = finals+" ago";
              THIS.element().HTML(finals);
              console.log(finals);
        }  
        var TIMER = setInterval( function(){
              getInt();
        },60000);
        getInt();
    }
    left(DATE = "none"){
        if(DATE == "none"){
            DATE = this.element().JS_ELEMENT().getAttribute("date-data");
        }
    }
    day(DATE = "none"){
       if(DATE == "none"){
            DATE = this.element().JS_ELEMENT().getAttribute("date-data");
        }
       DATE = new Date(DATE);
       var final = this.months(DATE.getMonth())+" "+OrdinaryNumber(DATE.getDate())+" "+DATE.getFullYear();
       this.element().HTML(final);
    }
    
    dayYear(DATE = "none"){
       if(DATE == "none"){
            DATE = this.element().JS_ELEMENT().getAttribute("date-data");
        }
       DATE = new Date(DATE);
       var final = this.months(DATE.getMonth())+" "+OrdinaryNumber(DATE.getDate());
       this.element().HTML(final);
    }
    day_time(DATE = "none"){
        if(DATE == "none"){
            DATE = this.element().JS_ELEMENT().getAttribute("date-data");
        }
       DATE = new Date(DATE);
       var hours = DATE.getHours();
        if(hours<10){
            hours = "0"+hours;
        }
        var minutes = DATE.getMinutes();
        if(minutes<10){
            minutes = "0"+minutes;
        }
       var final = this.months(DATE.getMonth())+" "+OrdinaryNumber(DATE.getDate())+" "+DATE.getFullYear() + " at "+hours+" : "+minutes;
       this.element().HTML(final);
    }
    time(){
        
    }
}
// class that will manipulate all functions about form

function PIP_DATE(CSS_SELECTOR){
    return new PIP_DAT(CSS_SELECTOR);
}
// a class to create user forms with connection to JS_API
class PIP_FORM {
    constructor(SELECTOR, datasInfo){
        this.Container = new PIP_ELEMENT(SELECTOR);
        this.numbers = {
            size:0,
            val:[],
            max:[],
            min:[]
        };
        this.emails = {
            size:0,
            val:[],
            restric:[],
            require:[]
        };
        this.tel_numbers = {
            size:0,
            val:[]
        };
        this.names = {
            size:0,
            val:[],
            // language of the name
            lang:[],
            // type of the name: 0 NATIVE  , 1 for Religious , 2 for surname
            type:[]
        };
        this.passwords = {
            size:0,
            val:[]
        };
        this.texts = {
            size:0,
            val:[]
        };
        this.btexts = {
            size:0,
            val:[]
        };
        this.form = document.createElement("form");
        this.form.setAttribute("name",datasInfo.name);
        this.form.setAttribute("class",datasInfo.Class);
        this.form.setAttribute("id",datasInfo.id);
        this.form.setAttribute("action",datasInfo.action);
        this.form.setAttribute("method", datasInfo.method);
        this.Container.JS_ELEMENT().appendChild(this.form);
    }
    
    SUBMIT(datasInfo){
        this.submitElement = document.createElement("button");
        this.submitElement.setAttribute("type","submit");
        this.submitElement.setAttribute("value",datasInfo.value);
        this.submitElement.setAttribute("id",datasInfo.id);
        this.submitElement.setAttribute("class",datasInfo.Class);
        this.submitElement.setAttribute("name",datasInfo.name);
        this.submitElement.appendChild(document.createTextNode(datasInfo.value));
        this.form.appendChild(this.submitElement);
        return this;
    }
    
    RESET(datasInfo){
        this.submitElement = document.createElement("button");
        this.submitElement.setAttribute("type","reset");
        this.submitElement.setAttribute("value",datasInfo.value);
        this.submitElement.setAttribute("id",datasInfo.id);
        this.submitElement.setAttribute("class",datasInfo.Class);
        this.submitElement.setAttribute("name",datasInfo.name);
        this.submitElement.appendChild(document.createTextNode(datasInfo.value));
        this.form.appendChild(this.submitElement);
        return this;
    }
    
    EMAIL(datasInfo){
        this.emails.val.push(document.createElement("input"));
        this.emails.val[this.emails.size].setAttribute("type","email");
        if(datasInfo.id)
            this.emails.val[this.emails.size].setAttribute("id",datasInfo.id);
        if(datasInfo.Class)
            this.emails.val[this.emails.size].setAttribute("class",datasInfo.Class);
        if(datasInfo.name)
            this.emails.val[this.emails.size].setAttribute("name",datasInfo.name);
        if(datasInfo.value)
            this.emails.val[this.emails.size].setAttribute("value",datasInfo.value);
        this.form.appendChild(this.emails.val[this.emails.size]);
        this.emails.size++;
        return this;
    }
    
    NUMBER(datasInfo){
        this.numbers.val.push(document.createElement("input"));
        this.numbers.val[this.numbers.size].setAttribute("type","number");
        if(datasInfo.id)
            this.numbers.val[this.numbers.size].setAttribute("id",datasInfo.id);
        if(datasInfo.Class)
            this.numbers.val[this.numbers.size].setAttribute("class",datasInfo.Class);
        if(datasInfo.name)
            this.numbers.val[this.numbers.size].setAttribute("name",datasInfo.name);
        if(datasInfo.value)
            this.numbers.val[this.numbers.size].setAttribute("value",datasInfo.value);
        this.form.appendChild(this.numbers.val[this.numbers.size]);
        this.numbers.size++;
        return this;
    }
    TELNUMBER(){
        this.tel_numbers.val.push(document.createElement("input"));
        this.tel_numbers.val[this.tel_numbers.size].setAttribute("type","number");
    }
    NAMES(){
        
    }
    PASSWORD(){
        
    }
    TEXT(){
        
    }
    BTEXT(){
        
    }
}
// a class to create a parent of PIP_ELEMENTS
class before{
    constructor(tag){
        this.elem = document.createElement(tag);
        
    }
}
var Pform = function(selector,format){
    return new PIP_FORM(selector,format);
}
class PIP_SERVER extends webApp{
    
}
class admin extends webApp{
    
}
class PIPCLENTS extends webApp{
    
}
// this is a class to manipulate html DOM and BOM similar to the Jquery $
class PIP_ELEMENT {
       constructor(CSS_SELECTOR){
           this.VISIBL = true;
           var FIRST = CSS_SELECTOR.substring(0,1);
           this.DisplayType = new Array();
           switch(FIRST){
               case "#":{
                   this._ELEMNT = Isokonline_Id(CSS_SELECTOR.substring(1,CSS_SELECTOR.length));
                   //console.log(CSS_SELECTOR.substring(1,CSS_SELECTOR.length));
                   this.TYPE = 0;
                   this.DisplayType.push(this._ELEMNT.style.display);
                   break;
               }
               case ".":{
                   this._ELEMNT = Isokonline_Class(CSS_SELECTOR.substring(1,CSS_SELECTOR.length));
                   this.TYPE = 1;
                   for(var xx=0; xx<this._ELEMNT.length;xx++){
                      this.DisplayType.push(this._ELEMNT[xx].style.display); 
                   }
                   break;
               }
               case ":":{
                   this._ELEMNT = Isokonline_Name(CSS_SELECTOR.substring(1,CSS_SELECTOR.length));
                   this.TYPE = 2;
                   for(var xx=0; xx<this._ELEMNT.length;xx++){
                      this.DisplayType.push(this._ELEMNT[xx].style.display); 
                   }
                   break;
               }
               default :{
                   this._ELEMNT = Isokonline_Element(CSS_SELECTOR);
                   this.TYPE = 3;
                   for(var xx=0; xx<this._ELEMNT.length;xx++){
                      this.DisplayType.push(this._ELEMNT[xx].style.display); 
                   }
                   break;
                   
               }
           }
       }
       hide(){
           switch(this.TYPE){
               case 0 :{
                   this._ELEMNT.style.display = "none";
                   this.VISIBL = false;
                   break;
               }
               default :{
                   for(var xx=0;xx<this._ELEMNT.length;xx++){
                       this._ELEMNT[xx].style.display = "none";
                       this.VISIBL = false;
                   }
                   break;
               }
           }
           
       }
       show(){
           switch(this.TYPE){
               case 0 :{
                       this._ELEMNT.style.display = "block";
                       break;
                   }
               default :{
                   for(var xx=0;xx<this._ELEMNT.length;xx++){
                       this._ELEMNT[xx].style.display = "block";
                   }
                   break;
               }
           }
           this.VISIBL = true;
       }
       show_hide(){
           if(this.VISIBL){
               switch(this.TYPE){
                   case 0:{
                       this._ELEMNT.style.display = "none";
                       break;
                   }
                   default :{
                       for(var xx=0; xx<this._ELEMNT.length;xx++){
                           this._ELEMNT[xx].style.display = "none";
                           
                       }
                       break;
                   }
               }
               this.VISIBL = false;
           } else {
               switch(this.TYPE){
                   case 0:{
                       this._ELEMNT.style.display = "block";
                       break;
                   }
                   default:{
                       for(var xx=0; xx<this._ELEMNT.length;xx++){
                           this._ELEMNT[xx].style.display = "block";
                       }
                       break;
                   }
               }
               this.VISIBL = true;
           }
       } 
       click(callBack){
           switch(this.TYPE){
               case 0:{
                   this._ELEMNT.addEventListener("click", function(edf){
                       edf.preventDefault();
                       callBack();
                   })
                   break;
               }
               default: {
                   for(var xx = 0; xx <  this._ELEMNT.length; xx++){
                       this._ELEMNT[xx].addEventListener("click", function(edf){
                           edf.preventDefault();
                           callBack();
                       })
                   }
                   break;
           }
        }   
      }
       HTML(STRING){
           switch(this.TYPE){
               case 0:{
                   this._ELEMNT.innerHTML = STRING;
                   break;
               }
               default:{
                   for(var xx = 0; xx< this._ELEMNT.length;xx++){
                       this._ELEMNT[xx].innerHTML = STRING;
                       
                   }
                  break;
               }
           }
       }
       JS_ELEMENT(){
           return this._ELEMNT;
       }
   }
var P = function(STR){
    return new PIP_ELEMENT(STR);
}
// START OF DOM SELECTOR AND MANIPULATOR CLASS #########################################################################################
//#########################################################################################################################
   var Isokonline_Id = function(id){
       return document.getElementById(id);
   }
   var Isokonline_Class = function(Class){
       return document.getElementsByClassName(Class);
   }
   var Isokonline_Element = function(Element){
       return document.getElementsByTagName(Element);
   }
   var Isokonline_Name = function(Name){
       return document.getElementsByName(Name);
   } 
   class Isokonline__ELEMNT {
       constructor(CSS_SELECTOR){
           this.VISIBL = true;
           var FIRST = CSS_SELECTOR.substring(0,1);
           this.DisplayType = new Array();
           switch(FIRST){
               case "#":{
                   this._ELEMNT = Isokonline_Id(CSS_SELECTOR.substring(1,CSS_SELECTOR.length));
                   this.TYPE = 0;
                   this.DisplayType.push(this._ELEMNT.style.display);
                   break;
               }
               case ".":{
                   this._ELEMNT = Isokonline_Class(CSS_SELECTOR.substring(1,CSS_SELECTOR.length));
                   this.TYPE = 1;
                   for(var xx=0; xx<this._ELEMNT.length;xx++){
                      this.DisplayType.push(this._ELEMNT[xx].style.display); 
                   }
                   break;
               }
               case ":":{
                   this._ELEMNT = Isokonline_Name(CSS_SELECTOR.substring(1,CSS_SELECTOR.length));
                   this.TYPE = 2;
                   for(var xx=0; xx<this._ELEMNT.length;xx++){
                      this.DisplayType.push(this._ELEMNT[xx].style.display); 
                   }
                   break;
               }
               default :{
                   this._ELEMNT = Isokonline_Element(CSS_SELECTOR);
                   this.TYPE = 3;
                   for(var xx=0; xx<this._ELEMNT.length;xx++){
                      this.DisplayType.push(this._ELEMNT[xx].style.display); 
                   }
                   break;
                   
               }
           }
       }
       hide(){
           switch(this.TYPE){
               case 0 :{
                   this._ELEMNT.style.display = "none";
                   this.VISIBL = false;
                   break;
               }
               default :{
                   for(var xx=0;xx<this._ELEMNT.length;xx++){
                       this._ELEMNT[xx].style.display = "none";
                       this.VISIBL = false;
                   }
                   break;
               }
           }
           
       }
       show(){
           switch(this.TYPE){
               case 0 :{
                       this._ELEMNT.style.display = "block";
                       break;
                   }
               default :{
                   for(var xx=0;xx<this._ELEMNT.length;xx++){
                       this._ELEMNT[xx].style.display = "block";
                   }
                   break;
               }
           }
           this.VISIBL = true;
       }
       show_hide(){
           if(this.VISIBL){
               switch(this.TYPE){
                   case 0:{
                       this._ELEMNT.style.display = "none";
                       break;
                   }
                   default :{
                       for(var xx=0; xx<this._ELEMNT.length;xx++){
                           this._ELEMNT[xx].style.display = "none";
                           
                       }
                       break;
                   }
               }
               this.VISIBL = false;
           } else {
               switch(this.TYPE){
                   case 0:{
                       this._ELEMNT.style.display = "block";
                       break;
                   }
                   default:{
                       for(var xx=0; xx<this._ELEMNT.length;xx++){
                           this._ELEMNT[xx].style.display = "block";
                       }
                       break;
                   }
               }
               this.VISIBL = true;
           }
       } 
       click(callBack){
           switch(this.TYPE){
               case 0:{
                   this._ELEMNT.addEventListener("click", function(edf){
                       edf.preventDefault();
                       this.ELE = this._ELEMNT;
                       callBack();
                   })
               }
               default: {
                   for(var xx = 0; xx < this._ELEMNT.length; xx++){
                       this._ELEMNT[xx].addEventListener("click", function(edf,yy=xx){
                           edf.preventDefault();
                           //this.ELE  = this._ELEMNT[yy];
                           callBack();
                       })
                   }
                   break;
           }
        }   
      }
       JS_ELEMENT(){
           return this._ELEMNT;
       }
   }
   var Isokonline_ = function(STR){
       return new Isokonline__ELEMNT(STR);
   }
 // END OF DOM SELECTOR AND MANIPULATOR CLASS #########################################################################################
//######################################################################################################################### 
class PIP_SLIDERS{
    constructor(ISOKONLINE_ELEMENT_CLASS,ARRAY_VALUES){
        this.POSITION = 0;
        this.valuesTochange = ARRAY_VALUES;
        this.ELEMENTS = ISOKONLINE_ELEMENT_CLASS;
        this.ELEMENTS.JS_ELEMENT()[this.POSITION].style.display = "block";
        
    }
    getPosition(){
        return this.POSITION;
    }
    addNavigations(CLASS_NAME){
        this.Navigations = new Isokonline__ELEMNT(CLASS_NAME);
        var THIS = this;
        this.Navigations.JS_ELEMENT()[THIS.POSITION].style.position = "relative";
        this.Navigations.JS_ELEMENT()[THIS.POSITION].style.top = "-2px";
        this.Navigations.JS_ELEMENT()[THIS.POSITION].style.border = "2px solid #f39c12";
        this.Navigations.click( function(){
           // alert(this.ELE);
        })
        
    }
    addAllowNavigation(CLASS_NAME,ID_NAME,POSITION){
        this.AllowNavigation = new Isokonline__ELEMNT(CLASS_NAME);
        var THIS = this;
        switch(POSITION){
                case "LEFT":{
                   this.LeftAllow =  new Isokonline__ELEMNT(ID_NAME);
                   this.LeftAllow.click( function(){
                       if(!THIS.POSITION){
                          console.log("End of Slides");
                          THIS.POSITION = 0;
                       } else {
                           var timevar =0;
                            var xx = setInterval( function(){
                                console.log(THIS.valuesTochange[timevar]);
                                THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION].style.left = '-'+THIS.valuesTochange[timevar]+'px';
                                //THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION].style.opacity = '';
                                if(timevar>THIS.valuesTochange.length){
                                    clearInterval(xx);
                                    THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION].style.display = "none";
                                    THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION].style.left = "0px";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION].style.position = "relative";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION].style.top = "0";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION].style.border = "0 solid #f39c12";
                                    THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION-1].style.display = "block";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION-1].style.position = "relative";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION-1].style.top = "-2px";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION-1].style.border = "2px solid #f39c12";
                                    THIS.POSITION--;
                                }
                                timevar++;
                            },1);  
                       }
                   });
                  break;
                }
                case "RIGHT":{
                   this.RightAllow = new Isokonline__ELEMNT(ID_NAME);
                   this.RightAllow.click( function(){
                       if(THIS.POSITION>=(THIS.ELEMENTS.JS_ELEMENT().length-1)){
                           console.log("End of Slides");
                           THIS.POSITION = THIS.ELEMENTS.JS_ELEMENT().length-1;
                       } else {
                            var timevar =0;
                            var xx = setInterval( function(){
                                THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION].style.left = THIS.valuesTochange[timevar]+'px';
                                if(timevar>THIS.valuesTochange.length){
                                    clearInterval(xx);
                                    THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION].style.display = "none";
                                    THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION].style.left = "0px";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION].style.position = "relative";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION].style.top = "0";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION].style.border = "0 solid #f39c12";
                                    THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION+1].style.display = "block";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION+1].style.position = "relative";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION+1].style.top = "-2px";
                                    THIS.Navigations.JS_ELEMENT()[THIS.POSITION+1].style.border = "2px solid #f39c12";
                                    console.log(THIS.POSITION+1);
                                    THIS.POSITION++;
                                }
                                timevar++;
                            },1);
                           
                       }
                   });
                    break;
                    
                }
                default:{
                    this.RightAllow = new Isokonline__ELEMNT(ID_NAME);
                    this.RightAllow.click( function(){
                           if(THIS.POSITION>(THIS.ELEMENTS.JS_ELEMENT().length-1)){
                               console.log("End of Slides");
                               THIS.POSITION = THIS.ELEMENTS.JS_ELEMENT().length-1;
                           } else {
                              THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION].style.display = "none";
                              THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION+1].style.display = "block";
                              THIS.POSITION++; 
                           }
                       });
                    break;
                }
        }
        
        
    }
    addAutomaticSliding(TIME_BTWN){
        this.focused = true;
    
        var THIS = this;
        this.TIMER = setInterval( function(){
            if(THIS.focused){
                window.onfocus = function(){
                    this.focused = true;
                }

                window.onblur = function(){
                    this.focused = false;
                }
                
                var timevar =0;
                var xx = setInterval( function(){
                    THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION].style.left = THIS.valuesTochange[timevar]+'px';
                    if(timevar>THIS.valuesTochange.length){
                        clearInterval(xx);
                        var THIS_ELEMENTS = 0;
                        if(THIS.POSITION>=(THIS.ELEMENTS.JS_ELEMENT().length-1)){
                            var THIS_ELEMENTS = 0;
                        } else {
                            THIS_ELEMENTS = THIS.POSITION+1;
                        }
                        THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION].style.display = "none";
                        THIS.ELEMENTS.JS_ELEMENT()[THIS.POSITION].style.left = "0px";
                        THIS.Navigations.JS_ELEMENT()[THIS.POSITION].style.position = "relative";
                        THIS.Navigations.JS_ELEMENT()[THIS.POSITION].style.top = "0";
                        THIS.Navigations.JS_ELEMENT()[THIS.POSITION].style.border = "0 solid #f39c12";
                        THIS.ELEMENTS.JS_ELEMENT()[THIS_ELEMENTS].style.display = "block";
                        THIS.Navigations.JS_ELEMENT()[THIS_ELEMENTS].style.position = "relative";
                        THIS.Navigations.JS_ELEMENT()[THIS_ELEMENTS].style.top = "-2px";
                        THIS.Navigations.JS_ELEMENT()[THIS_ELEMENTS].style.border = "2px solid #f39c12";
                        THIS.POSITION++;
                        if(!THIS_ELEMENTS){
                           THIS.POSITION = 0;
                        }
                    }
                    timevar++;
                },1);
            } else {
                alert(THIS.TIMER);
                clearInterval(THIS.TIMER);
            }
        },TIME_BTWN);
        
        
    }  
}
// class that will communicate with JS_API of php and node.js
class JS_API{
        constructor(SPECS, DATA_TYPE="php",STRUCTURE_TYPE="php"){
            // a private variable to keep the current index of the iteration, this will be initialized at the call of this.receive.
            var indexes = 0;
            // a private variable to keep number of total alement of this on the page after the call of receive 
            var numbers = 0;
            // a private array of number to keep all available ids to prevent from redundance.
            var universal_id = Array();
            // a public method to push an element in the universal id array.
            this.new_universal_id = (num) =>{
                universal_id.push(num);
            }
            // a public method to check if the given universal_id exist
            this.exist = (num) =>{
                var rets = false;
                for(var xx=0; xx< universal_id.length; xx++){
                    if(num==universal_id[xx]){
                        rets = true;
                        break;
                    }
                }
                return rets;
            }
            // a public method to return the entire universal_id array
            this.univ = ()=>{
                return universal_id;
            }
            
            // a variable to keep all additional parameters tsend
            // if reset is 0 the values will be added to the current ,1 is to overwrite current value, 2 is to add the whole given string to the current parameters and 3 to overwrite the values with the whole string given 
            var params = "";
            this.AddParm = (name,value,reset = 0)=>{
                if(reset===0){
                    params += "&"+name+"="+value;
                } else if(reset===1){
                    params = "&"+name+"="+value;
                } else if(reset===2){
                    params += "&"+name;
                } else if(reset===3){
                    params = "&"+name;
                } else {
                    params = "";
                }
                return this;
            }
            this.mmoreP = ()=>{
                return params;
            }
            
            
             
            
            // public variable to describe a data type backend technology (php, js, python,...)
            this.dataType = DATA_TYPE;
            // public variable for data structure file format ex(html, php, ...)
            this.structureType = STRUCTURE_TYPE;
            // public variable to keep datastructure of this element
            this.structure;
            // a public variable to desctribe current page data of this element
            this.data;
            // a public variable to keep a field of data form database and JSON format
            this.field;
            // a public varibale to keep all possible exeptions for this element
            this.exept;
            // a variable to keep some extra data outside the looped data
            this.extra;
            // a public variable to keep last fecthed data position in the database;
            this.lastFetch = 0;
            // a public variable to keep total number of pages 
            this.pageNumbers = {
                currentPage:1,
                currentPageGroup:1,
                numberOfpage:0,
                numberOfpageGroup:0
            }
            //a public variable to keep the description of pagination of the page
            this.pages = {
                display:0,
                parentClasses:"",
                itemsClasses:"",
                activeItemClasses:"",
                interLinkClass:"",
                leftOverload:"",
                rightOverload:""
            }
            this.paginations = false;
            var specifications = {
                   // name of the file and name of the object to load datas from the database and the id of the element to display in
                   name : SPECS.name,
                   // url of the structure of this element in the backend
                   structureUrl : SPECS.structureUrl,
                   // url of data that will be provided from database
                   dataUrl : SPECS.dataUrl,
                   // parameter name to be passed as http post data and to be in emit() function
                   dataParameter : SPECS.dataParameter,
                   // data parent to be added on the top of every new data default is "div"
                   dataParent : "div",
                   // list of attributes to be added on the dataParent
                   dataParentAttribute:{
                       "class":"JS_API_PARENT"
                   }
            }
            
            
            if(!(SPECS.dataParent==null))
                specifications.dataParent = SPECS.dataParent
            if(!(SPECS.dataParentAttribute==null)){
                specifications.dataParentAttribute = SPECS.dataParentAttribute
            }
            
            
            this.j = $("#"+specifications.name);
            this.P = P("#"+specifications.name);
            // a JQuery object to keep all static DOM element from the main element
            this.statics = this.j.find(".js_api_static");
            this.jA = (index) =>{
                return $("#"+specifications.name+index)
            }
            this.PA = (index)=>{
                return P("#"+specifications.name+index);
            }
            this.index = () =>{
                return indexes;
            }
            this.more = () =>{
                indexes++;
                numbers++;
            }
            this.specs = () => {
                return specifications;
            }
            this.indexing = ()=>{
                indexes = 0;
            }
            // a function to return a number of data to the page
            this.total = ()=>{
                return numbers;
            }
            // a function initialize the number of data to the page
            this.empty = ()=>{
                universal_id = new Array();
                numbers = 0;
                return this;
            }
            
            // a variable to keep the html format as a string to prevent repetitive task of requesting data
            this.makeUp = "";
            
            var THIS = this;
            $.post(this.specs().structureUrl+"."+this.structureType,
                  "indexes="+this.index()+"&name="+this.specs().name,
                  function(HTMLS){
                        THIS.makeUp = HTMLS;
            });
        }
        // a function to receive set of data from database
        receive(FUN,last = 0){
            var THIS_JS_API = this;
            if(this.lastFetch<1){
                  $.post(this.specs().dataUrl+"."+this.dataType,
                       this.specs().dataParameter+"=struct",
                       function(struct){
                       THIS_JS_API.structure = JSON.parse(struct);
                       $.post(THIS_JS_API.specs().dataUrl+"."+THIS_JS_API.dataType,
                              THIS_JS_API.specs().dataParameter+"=fields",
                              function(fields){
                              THIS_JS_API.field = JSON.parse(fields);
                              $.post(THIS_JS_API.specs().dataUrl+"."+THIS_JS_API.dataType,
                                     THIS_JS_API.specs().dataParameter+"=exeption",
                                     function(exeption){
                                     THIS_JS_API.exept = JSON.parse(exeption);
                                     $.post(THIS_JS_API.specs().dataUrl+"."+THIS_JS_API.dataType,
                                            THIS_JS_API.specs().dataParameter+"=extra",
                                            function(extra){
                                            THIS_JS_API.extra = JSON.parse(extra);
                                            THIS_JS_API.get_data(FUN,last);
                                     })
                              })
                       })
                })  
            }
            else {
                this.get_data(FUN,last);
            }
        }
        // a function to fetch all data from the database, this have the difference with the above one because it fecth in single request
        fetch(FUN, what = "all", last = 0, updates = 0){
            var THIS_JS_API = this;
            if(this.lastFetch<1){
                $.post(this.specs().dataUrl+"."+this.dataType,
                       this.specs().dataParameter+"="+what+"&last="+last+"&start=0"+this.mmoreP(),
                       function(rets){
                            switch(what){
                                case "struct":{
                                    THIS_JS_API.structure = JSON.parse(rets);
                                    break;
                                }
                                case "fields":{
                                    THIS_JS_API.field = JSON.parse(rets);
                                    break;
                                }
                                case "exeption":{
                                    THIS_JS_API.exept = JSON.parse(rets);
                                    break;
                                }
                                case "extra":{
                                   THIS_JS_API.extra = JSON.parse(rets);
                                   break; 
                                }
                                case "data":{
                                    THIS_JS_API.data = JSON.parse(rets);
                                    break;
                                }
                                case "all":{
                                    var allV = JSON.parse(rets);
                                    THIS_JS_API.structure = allV.struct;
                                    THIS_JS_API.field = allV.fields;
                                    THIS_JS_API.exept = allV.exeption;
                                    THIS_JS_API.extra = allV.extra;
                                    THIS_JS_API.data = allV.data;
                                    break;
                                }
                                case "data_nd_extra":{
                                    var allV = JSON.parse(rets);
                                    THIS_JS_API.extra = allV.extra;
                                    THIS_JS_API.data = allV.data;
                                    break;
                                }
                                default :{
                                    var allV = JSON.parse(rets);
                                    THIS_JS_API.structure = allV.struct;
                                    THIS_JS_API.field = allV.fields;
                                    THIS_JS_API.exept = allV.exeption;
                                    THIS_JS_API.extra = allV.extra;
                                    THIS_JS_API.data = allV.data;
                                    break;
                                }
                            }
                    THIS_JS_API.indexing();
                    THIS_JS_API.put_datas(THIS_JS_API.makeUp, FUN, updates);
                });
            } else {
                this.fetch(FUN,"data_nd_extra",last);
            }
        }
        // a function to define pagination in all received data displayed
        pagination(file){
            this.paginations = true;
            this.pages = {
                display:file.display,
                parentClasses:file.parentClasses,
                itemsClasses:file.itemsClasses,
                activeItemClasses:file.activeItemClasses,
                interLinkClass:file.interLinkClass,
                leftOverload:file.leftOverload,
                rightOverload:file.rightOverload,
                pageGroup: file.pageGroup
            }
            if(file.Container){
               this.pages.Container = file.Container;
            }
        }
        // a function to get structure of a JS_API data defined in a separate file
        get_structre(FUN){
           var THIS_JS_API = this;
           $.post(this.specs().structureUrl+"."+this.structureType,
                  "indexes="+this.index()+"&name="+this.specs().name,
                  function(HTMLS){
                 THIS_JS_API.put_datas(HTMLS,FUN);
                 
           });
        }
        // a function to get next data set to fetch
        get_data(FUN,last){
            this.indexing();
            var data_send = this.specs().dataParameter+"=data&last=0&start=0";
            if(last){
                data_send = this.specs().dataParameter+"=data&last="+last+"&start=0";
            }
            var THIS_JS_API = this;
            this.lastFetch = last;
            $.post(this.specs().dataUrl+"."+this.dataType,
                   data_send,
                   function(datax){
                     THIS_JS_API.data = JSON.parse(datax);
                     THIS_JS_API.get_structre(FUN); 
                     
            })
        }
        // a function to put all data in specified HTML element
        put_datas(HTMLS,FUN, updates = 0){
            var CURR = this.data.length - this.index() - 1;
           if(this.index()<this.data.length){
               var displayOk = false;
               if(!this.exist(this.data[CURR].universal_id)){
                   displayOk = true;
               }
               
               if(updates>0){
                   displayOk = true;
               }
               
               if(displayOk){
                     var MainCont = document.createElement(this.specs().dataParent);
                     $(MainCont)
                         .attr("id",this.specs().name+this.data[CURR].universal_id)
                         .addClass(this.specs().name+"-container")
                         .addClass(this.specs().dataParentAttribute.class)
                         .html(HTMLS)
                         .hide();
               for(var tt=0; tt<this.field.length;tt++){
                          for(var ttt = 0; ttt<this.exept.length; ttt++){
                                if(this.field[tt].name==this.exept[ttt].field)
                                    if(eval("this.data[CURR]."+this.field[tt].name)==this.exept[ttt].val)
                                        if(this.exept[ttt].type=="class"){
                                           
                                         $(MainCont).find(".JS_API").addClass(this.exept[ttt].attr); 
                                        } else {
                                           $(MainCont).find(".JS_API").attr(this.exept[ttt].type,this.exept[ttt].attr); 
                                        }
                                     
                          }
                          if(eval('this.structure.'+this.field[tt].name+'.data_html')){
                            $(MainCont)
                                .find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']")
                                .html(eval("this.data[CURR]."+this.field[tt].name))
                                .addClass(eval("this.structure."+this.field[tt].name+".classes"));
                          } 
                          else {
                              $(MainCont)
                                  .find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']")
                                  .addClass(eval("this.structure."+this.field[tt].name+".classes"));
                          }
                          for(var y = 0; y<eval('this.structure.'+this.field[tt].name+'.EXEPTION.length');y++)
                                
                            if(!(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].val')=="*")){ 
                              // alert(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].val'));
                                if(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].val')==eval("this.data[CURR]."+this.field[tt].name)){
                                if(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].type')=="class"){
                                  $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").addClass(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].attr')); 
                               } else {
                                   $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").attr(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].type'),eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].attr'));
                               }
                              } else if(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].val')=="#"){
                                  $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").attr(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].type'),eval("this.data[CURR]."+this.field[tt].name));
                              }
                            } else if((eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].val')=="*")&&((eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].type')=="id"))) {
                                $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").attr("id",eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].attr')+this.specs().name+this.data[CURR].universal_id);
                            } else if((eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].val')=="*")){
                                if(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].type')=="class"){
                                   $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").addClass(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].attr'));
                                } else if(!(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].type')=="*")) {
                                   $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").attr(eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].type'),eval('this.structure.'+this.field[tt].name+'.EXEPTION['+y+'].attr'));
                                }
                            }
                            
                          if(eval('this.structure.'+this.field[tt].name+'.EXEPTION_'))
                            for(var y =0 ;y<eval('this.structure.'+this.field[tt].name+'.EXEPTION_.length');y++)
                                
                               if(!(eval("this.structure."+this.field[tt].name+".EXEPTION_["+y+"].field")=="+"))
                               if(!(eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].val')=="*")){ if(eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].val')==eval("this.data[CURR]."+eval("this.structure."+this.field[tt].name+".EXEPTION_["+y+"].field"))){
                                 if(eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].type')=="class"){
                                       if(eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].attr')=="#")
                                          $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").addClass(eval("this.data[CURR]."+eval("this.structure."+this.field[tt].name+".title"))); 
                                       else 
                                       $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").addClass(eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].attr')); 
                                    } else {
                                        if(eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].attr')=="#")
                                          $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").addClass(eval("this.data[CURR]."+eval("this.structure."+this.field[tt].name+".title")));
                                        else 
                                        $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").attr(eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].type'),eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].attr'));
                                    }
                                } else if((eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].val')=="#")){
                                 //alert();
                                 $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").attr(eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].type'),eval("this.data[CURR]."+eval("this.structure."+this.field[tt].name+".EXEPTION_["+y+"].field")));
                               }
                             } else if((eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].val')=="*")&&((eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].type')=="id"))){
                                 $(MainCont).find("[data-display='"+eval('this.structure.'+this.field[tt].name+'.data_display')+"']").attr("id",eval('this.structure.'+this.field[tt].name+'.EXEPTION_['+y+'].attr'+eval("this.data[CURR]."+eval("this.structure."+this.field[tt].name+".EXEPTION_["+y+"].field")))+this.specs().name+this.data[CURR].universal_id);
                             }
                          }
                      
                      var previousDatas = $("#"+this.specs().name+" ."+this.specs().name+"-container");
                      
                      if(this.pages.display>0)
                      $(MainCont)
                          .attr("data-length",previousDatas.length)
                          .attr("page-init",parseInt(CURR/this.pages.display)+1)
                     else 
                         $(MainCont)
                          .attr("data-length",previousDatas.length)
                          .attr("page-init",0)
                      
                      $("#"+this.specs().name).prepend(MainCont);
                      $("#"+this.specs().name).prepend(this.statics);
                      if(CURR < this.pages.display){
                          $(MainCont).fadeIn(500);
                      }
                      this.more();
                      this.put_datas(HTMLS,FUN,updates);
                      this.new_universal_id(this.data[CURR].universal_id);
              } 
               else {
                 console.log("This "+this.specs().name+" already exist !");
              }
           }
           else {
               this.paginate();
               FUN(this);
           }
       }
        // a function to do the pagination of data
        paginate(){
           var previousDatas = $("#"+this.specs().name+" ."+this.specs().name+"-container");
               $("#"+this.specs().name+"_pagination").remove();
               var paging = 0;
               for(var xx = 0; xx<previousDatas.length; xx++){
                   if((xx%this.pages.display)==0){
                       paging++;
                   }
                   $(previousDatas[xx]).attr("data-page",paging);
               }
               
               for(var xx = 0; xx<previousDatas.length; xx++){
                   var data_pages = $(previousDatas[xx]).attr("data-page");
                   if(data_pages>1){
                       $(previousDatas[xx]).hide();
                   }
               }

               if(this.paginations){
                  var mainNav = document.createElement("ul");
                  var leftShift = document.createElement("li");
                  var rightShift = document.createElement("li");
                  var leftShift_a = document.createElement("a");
                  var rightShift_a = document.createElement("a");
                  var THIS_JS_API = this;
                   $(leftShift_a)
                       .addClass(this.pages.interLinkClass)
                       .attr("href","#"+this.specs().name)
                       .html(this.pages.leftOverload);
                   $(rightShift_a)
                       .addClass(this.pages.interLinkClass)
                       .attr("href","#"+this.specs().name)
                       .html(this.pages.rightOverload);
                   
                   $(leftShift).html(leftShift_a)
                       .off("click")
                       .on("click", function(cv){
                       cv.preventDefault();
                       if(THIS_JS_API.pageNumbers.currentPage>1){
                           
                           THIS_JS_API.pageNumbers.currentPage--;
                           $("#"+THIS_JS_API.specs().name+"_pagination")
                             .find("li."+THIS_JS_API.specs().name+"_paginationItems").hide();
                           $("#"+THIS_JS_API.specs().name+"_pagination")
                               .find("[page-group="+THIS_JS_API.pageNumbers.currentPage+"]").show();
                       }
                   });
                   $(rightShift)
                       .html(rightShift_a)
                       .off("click")
                       .on("click", function(cv){
                       cv.preventDefault();
                       if(THIS_JS_API.pageNumbers.currentPage<THIS_JS_API.pageNumbers.numberOfpageGroup){
                           THIS_JS_API.pageNumbers.currentPage++;
                           $("#"+THIS_JS_API.specs().name+"_pagination")
                             .find("li."+THIS_JS_API.specs().name+"_paginationItems").hide();
                           $(mainNav).find("[page-group="+THIS_JS_API.pageNumbers.currentPage+"]").show();
                       }
                       
                   });
                   
                  $(mainNav)
                      .addClass(this.pages.parentClasses)
                      .attr("id",this.specs().name+"_pagination")
                      .append(leftShift);
                  
                  
                   this.pageNumbers.numberOfpage = parseInt(this.total()/this.pages.display);
                   if((this.total()%this.pages.display)>0){
                      this.pageNumbers.numberOfpage++; 
                   }
                    for(var x =1; x<=this.pageNumbers.numberOfpage; x++){
                        if((x-1)%this.pages.pageGroup==0){
                            this.pageNumbers.numberOfpageGroup++;
                        }
                        var item = document.createElement("li");
                        var itemL = document.createElement("a");
                        $(itemL).addClass(this.pages.interLinkClass).attr("href","#"+this.specs().name).html(x);
                        if(x>this.pages.pageGroup)
                            $(item).hide();
                        $(item).addClass(this.pages.itemsClasses).attr("id",this.specs().name+"_page_items").attr("data-page",x).append(itemL).addClass(this.specs().name+"_paginationItems").attr("page-group",this.pageNumbers.numberOfpageGroup);
                        if(x==1){
                          $(item).addClass(this.pages.activeItemClasses);  
                        }
                        $(mainNav).append(item);
                    }
                     $(mainNav).append(rightShift);
                    if(this.pages.Container)
                        $(this.pages.Container).append(mainNav);
                    else $("#"+this.specs().name).append(mainNav);
                    
                     $("#"+this.specs().name+"_pagination")
                         .find("li."+this.specs().name+"_paginationItems")
                         .off("click")
                         .on("click", function(cv){
                         cv.preventDefault();
                         var DispPages = $(this).attr("data-page");
                         $("#"+THIS_JS_API.specs().name+"_pagination")
                             .find("li."+THIS_JS_API.specs().name+"_paginationItems")
                             .removeClass(THIS_JS_API.pages.activeItemClasses)
                         $(this).addClass(THIS_JS_API.pages.activeItemClasses)
                         previousDatas.hide();
                         $("#"+THIS_JS_API.specs().name).find("div[data-page="+DispPages+"]").show();
                     });
                   
                  } else {
                      $("#"+this.specs().name).find("."+this.specs().name+"-container").show();
                     console.log("no pages");  
                  }
       }
        // afunction to loop between given id executing all of the given functions and methods
        // and VALUES is the parameter to be given in funt
        loop(VALUES,FUNCTION,METHODS){
           var all = this.univ();
           //console.log(all);
           var THIS = this;
           setTimeout( function(){
             for(var b =0; b<all.length; b++){
               eval(FUNCTION+"(\"#"+VALUES+THIS.specs().name+all[b]+"\")."+METHODS+"()");
             }  
           },10)
           
           
       }
        // a function to put a loading effect to the container while waiting the main server
        loading(size = 0){
            if(size===1){
                this.j.html(LOADING_S_);
            } else {
                this.j.html(LOADING_N_);
            }
            return this;
        }
        // a function to remove the loading effect while all the request is ready to go
        finish(){
            this.j.find(".loading-and-waiting-img").remove();
            return this;
        }
        // a function to define where extra will go after the the request
        putExtra(jqSelector, extraName, index = 0){
            if(this.extra.length){
                $(jqSelector).html(this.extra[index][extraName]);
            }
            return this;
        }
        // a function to remove a set of specified data
        remove(Info){
            THIS_JS_API = this;
            var inform = {
                // a single item to be removed from the JS_API
                SingleItem:0,
                // the starting point of a list of item to be removed
                FromItem:0,
                // the ending point of a list of item to be removed
                UpToItem:0,
                // the url of a file to define a structure of item to be removed 
                Furl:THIS_JS_API.specs().dataUrl
            }
            
            if(Info.SingleItem)
                inform.SingleItem = Info.SingleItem
            if(info.FromItem)
                inform.FromItem = Info.FromItem
            if(info.UpToItem)
                inform.UpToItem = Info.UpToItem
            if(info.Furl)
                inform.Furl = Info.Furl
            
            $.post(this.inform.Furl,
                   "single="+inform.SingleItem+"&start="+inform.FromItem+"&end="+inform.UpToItem,
                   function(RETS){
                try{
                    
                } catch(e){
                    
                }
            })
        }
        // a function that will emit data to the server
        emit(){
            
        }
        // a function that will edit specified data in the database
        edit(){
            
        }
}

function arrayRemove(arr, value) {
   return arr.filter(function(ele){
       return ele != value;
   });

}
function OrdinaryNumber(num){
    var STR = num;
    switch(parseInt(num%10)){
        case 1:{
            STR = STR + "st";
            break;
        }
        case 2:{
            STR = STR + "nd";
            break;
        }
        case 3:{
            STR = STR + "rd";
            break;
        }
        default:{
            STR = STR + "th";
        }
    }
    return STR;
}


class PIP_Array {
    constructor(DATAS, fieldsN = []){
        this.AV = new Array(DATAS.length);
        this.RV = new Array(DATAS.length);
        this.message = "";
        this.fields = new Array(fieldsN.length);
        var i = DATAS.length;
        for(var ii = 0; ii < DATAS.length;ii++){
            this.AV[ii] = DATAS[ii];
            this.RV[i-1] = DATAS[ii];
            i--;
        }
        
        for(var ii = 0; ii< fieldsN.length; ii++){
            if(fieldsN[ii].name)
                this.fields[ii] = fieldsN[ii].name;
            else this.fields[ii] = fieldsN[ii];
        }
        this.size = DATAS.length;
        for(var ii = 0; ii< this.fields.length; ii++){
            for(var iii=0;iii<this.size;iii++){
                this.AV[iii][ii] = this.AV[iii][this.fields[ii]];
                this.RV[iii][ii] = this.RV[iii][this.fields[ii]];
            }
        }
        
    }
    _gets_(ind,val = "",type="REMOVE"){
       var DATS = [];
        if(parseInt(ind)){
            return this._gets_(this.fields[0],ind,"O");
        } else {
        for(var t =0 ; t<this.size;t++){
            if(type==="REMOVE"){
               if(!(this.AV[t][ind]==val)){
                    DATS.push(this.AV[t]);
                }
            } else {
               if(this.AV[t][ind]==val){
                    DATS.push(this.AV[t]);
                }
            }
        }
        return new PIP_Array(DATS, this.fields);
       }
    }
    height(){
        return this.size;
    }
    width(){
        return this.fields.length;
    }
    getsD(INDEX){
        var rets = new Array();
        for(var ii=0;ii<this.size;ii++){
            var exist = false;
            for(var iii=0;iii<ii;iii++){
                if(this.AV[ii][INDEX]==this.AV[iii][INDEX]){
                    exist = true;
                }
            }
            if(!exist) rets.push(this.AV[ii]);
        }
        return new PIP_Array(rets);
    }
    getsM(theArray2,ind1,ind2,type="REMOVE"){
        if(theArray2.size>0){
             for(var ii=0;ii<all.size;ii++){
                   ARR = ARR._gets_(ind2,all.AV[ii][ind1],type);
             }
             return new PIP_Array(ARR.AV, this.fields);
        } else return this.empty();
         
    }
    getsF(theArray2,ind1,ind2,type="REMOVE"){
        var ARR = this.getsD(ind2);
        if(theArray2.size>0){
             var all = theArray2.getsD(ind1);
             for(var ii=0;ii<all.size;ii++){
                   ARR = ARR._gets_(ind2,all.AV[ii][ind1],type);
             }
             return new PIP_Array(ARR.AV, this.fields);
        } else return this.empty();
    }
    empty(){
        return new PIP_Array(new Array(), this.fields);
    }
    lowest(INDEX = 0){
         var lowest = this.AV[0][INDEX];
         for(var ii=0; ii<this.size; ii++){
             if(this.AV[ii][INDEX]<lowest){
                 lowest = this.AV[ii][INDEX];
             }
         }
        return lowest;
    }
    lowestP(INDEX = 0){
         return this._gets_(INDEX,this.lowest(INDEX),"O");
    }
    highest(INDEX = 0){
         var highest = this.AV[0][INDEX];
         for(var ii=0;ii<this.size;ii++){
             if(this.AV[ii][INDEX]>highest){
                 highest = this.AV[ii][INDEX];
             }
         }
        return highest;
     }
    highestP(INDEX = 0){
         return this._gets_(INDEX,this.highest(INDEX),"O");
    }
    included(INDEX,VALUE){
         var rets = false;
         for(var ii=0;ii<this.size;ii++){
             if(this.AV[ii][INDEX]){
                 if(this.AV[ii][INDEX]==VALUE){
                     rets = true;
                     break;
                 }
             }
         }
         return rets;
    }
    lessThan(value,index = 0){
        var rets = new Array();
        for(var ii=0;ii<this.size;ii++){
            if(parseInt(value)>parseInt(this.AV[ii][index])){
               rets.push(this.AV[ii]);
            }
        }
        return new PIP_Array(rets,this.fields);
     }
    JS(index = 0){
        if(this.height()==0){
            this.message = "this PIP_Array is empty";
            console.log(this.message);
            return null; 
        }
        else if(index>this.height()){
            this.message = `the ${index} is out of bound in the size of ${this.height()}`;
            console.log(this.message);
            return null;
        }
        else return this.AV[index];
    }
    id(index = 0){
        if(this.height()==0){
            this.message = "this PIP_Array is empty";
            console.log(this.message);
            return null; 
        }
        else if(index>this.height()){
            this.message = `the ${index} is out of bound in the size of ${this.height()}`;
            console.log(this.message);
            return null;
        }
        else return this.AV[index][0];
    }
}



