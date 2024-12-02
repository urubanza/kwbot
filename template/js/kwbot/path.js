class path{
    constructor(){
        // estimated time to be used by the path
        var estimatedTime = 0;
        // the used time of the path at current time
        var elapsedTime = 0;
        // the list of angles to use by the path in form of time in milleseconds and value
        var angles = [];
        // the list of speed to be used by the path in form of time and value
        var speed = [];
        // the length of the path in form of number of angles
        var length = 0;
        // the current position we are on
        var position = 0;
        // the list of cartesian coordinate to be used 
        var point = [{
          "x":0,
          "y":0
        }];
        
        var screenGroundScale = 1/1;
        
        this.anglesBorders = ["(",")"];
        this.speedBorders = ["{","}"];
        this.pointBorder = ["[","]"];
        this.grobals = ["<",">"];
        
        var thepath = "";
        
        this.scale = function(scal = null){
            if(scal){
                if(parseInt(scal)==NaN)
                    return screenGroundScale;
                else screenGroundScale = 1/screenGroundScale;
            }
            return screenGroundScale;
        }
        
        this.string = function(string = null){
            if(!(string==null)){
                thepath = string;
            }
            //console.log(string);
            return thepath;
        }
        
        this.time = function(){
            return estimatedTime;
        }
        
        this.usedTime = function(){
            return elapsedTime;
        }
        
        this.angle = function(value = null, time = null){
            if(Array.isArray(value))
                angles = [];
            else if(value&&time){
                angles.push({
                    "values":parseFloat(value),
                    "time":parseFloat(time)
                });
            }
            this.size(angles.length);
            return angles;
        }
        
        this.SPEED = function(value = null, time = null){
            if(Array.isArray(value))
                speed = [];
            else if(value&&time){
                speed.push({
                    "values":parseFloat(value),
                    "time":parseFloat(time)
                });
            }
            this.size(speed.length);
            return speed;
        }
        
        this.size = function(value = null){
            if(!(value==null))
                length = parseInt(value);
            return length;
        }
        
        this.pos = function(value = null){
            if(!(value==null))
                position = value;
            return position;
        }
        
        this.p = function(x = null, y = null){
            if(Array.isArray(x)){
                point = [{
                      "x":0,
                      "y":0
                    }];
            }
             else if(x&&y){
                point.push({
                   "x":x,
                   "y":y
                });
            }
            this.size(point.length-1);
            return point;
        }
    }
    valuePair(str,start,ends){
        var pos = 0;
        var f = "";
        var l = "";
        var endsx = 0;
        for(var ii = 0; ii < str.length; ii++){
            if(str.substring(ii,ii+1)==start){
                console.log(str.substring(ii,ii+1));
                break;
            } else if(str.substring(ii,ii+1)==ends){
                endsx = ii;
                break
            } else if(str.substring(ii,ii+1)=="-"){
                pos++;
            } else {
                //console.log(str);
                if(pos>0){
                    l = l+str.substring(ii,ii+1);
                } else {
                    f = f+str.substring(ii,ii+1);
                }
            }
        }
        return [f,l,endsx];
    }
    // a function to load the path from the database
    load(path = ""){
        this.string(path);
       // a variables to indicate what we are reading 
       // 1 for angle, 2 for speed, 3 for points and 0 for no thing
       var whatToRead = 0;
       // a variable to indicate the the subpath we are on
        
       var subPath = 0;
       for(var ii = 0; ii< path.length; ii++){
           if(this.anglesBorders[0]==path.substring(ii,ii+1)){
               whatToRead = 1;
           } else if(this.speedBorders[0]==path.substring(ii,ii+1)){
               whatToRead = 2;
           } else if(this.pointBorder[0]==path.substring(ii,ii+1)){
               whatToRead = 3;
           } else if(this.anglesBorders[1]==path.substring(ii,ii+1)){
               whatToRead = 0;
           } else if(this.speedBorders[1]==path.substring(ii,ii+1)){
               whatToRead = 0;
           } else if(this.pointBorder[1]==path.substring(ii,ii+1)){
               whatToRead = 0;
           } else if(this.grobals[0]==path.substring(ii,ii+1)){
               whatToRead = 0;
           } else if(this.grobals[1]==path.substring(ii,ii+1)){
               subPath++;
           } else {
               switch(whatToRead){
                   case 0:{
                       break;
                   }
                   case 1:{
                       var rets = this.valuePair(path.substring(ii,path.length),this.anglesBorders[0],this.anglesBorders[1]);
                       ii += rets[2];
                       this.angle(rets[0],rets[1]);
                       break;
                   }
                   case 2:{
                       var rets = this.valuePair(path.substring(ii,path.length),this.speedBorders[0],this.speedBorders[1]);
                       ii += rets[2];
                       this.putSpeed(rets[0],rets[1]);
                       break;
                   }
                   case 3:{
                       var rets = this.valuePair(path.substring(ii,path.length),this.pointBorder[0],this.pointBorder[1]);
                       ii += rets[2];
                       this.putPoint(rets[0],rets[1]);
                       break;
                   }
                   default:{
                       break;
                   }
               }
           }
       }
       this.size(subPath);
       return this;
    }
    // a function to generate the path to be saved in the database
    generate(){
       var strings = "<";    
       for(var ii = 0; ii< this.size(); ii++){
           if(ii<this.angle().length){
               strings +=this.anglesBorders[0]+this.angle()[ii].values+"-"+this.angle()[ii].time+this.anglesBorders[1];
           } else {
               strings +=this.anglesBorders[0]+"NaN-NaN"+this.anglesBorders[1];
           }
           
           if(ii<this.SPEED().length){
               
               strings +=this.speedBorders[0]+this.SPEED()[ii].values+"-"+this.SPEED()[ii].time+this.speedBorders[1];
           } else {
               strings +=this.speedBorders[0]+"NaN-NaN"+this.speedBorders[1];
           }
           
           if(ii<this.p().length){
               strings +=this.pointBorder[0]+this.p()[ii].x+"-"+this.p()[ii].x+this.pointBorder[1];
           } else {
               strings +=this.pointBorder[0]+"NaN-NaN"+this.pointBorder[1];
           }
           strings += ">";
           if(ii<this.size()-1){
               strings += "<";
           }
       }
       
       this.string(strings);
       return this;
    }
    putAngle(angle,time){
        var finalAngle = ""+angle;
        this.angle(finalAngle.substring(0,5),time);
        return this;
    }
    putSpeed(value,time){
        var finalvalue = ""+value;
        this.SPEED(finalvalue.substring(0,5),time);
        return this;
    }
    putPoint(value,time){
        var x = value+"";
        var y = time+"";
        this.p(x.substring(0,5),y.substring(0,5));
        return this;
    }
    //"<(angle-time){angle-time}[x-y]><(angle-time){angle-time}[x-y]>";
    sub(from,to = 0){
        var rets = new path();
        if(to==0)
            return this.sub(from,from+1);
        if(from>to){
            return this.sub(from,this.size());
        } else {
            for(var ii = from; ii< to; ii++){
                rets.angle(this.angle()[ii].values,this.angle()[ii].time);
                rets.SPEED(this.SPEED()[ii].values,this.SPEED()[ii].time);
                rets.angle(this.p()[ii].x,this.p()[ii].y);
                console.log(this.angle()[ii].time);
            }
            rets.size(rets.angle().length);
        }
        //console.log(rets.size());
        return rets;
    }
    init(){
        this.scale(1/1);
        this.string("");
        this.time(0);
        this.usedTime(0);
        this.angle([]);
        this.SPEED([]);
        this.size(0);
        this.pos(0);
        this.p([]);
        return this;
    }
    
    reverse(){
        
    }
}

class timer{
    constructor(){
        this.time = Date.now();
        this.paused = false;
        
    }
    
    elapsed(){
        return Date.now() - this.time;
    }
    
    clear(){
        this.time = Date.now();
    }
}

class map{
    
    constructor(id){
        // the maximum speed in m/s *10
        const MAX_SPEED = 13;
        this.height = 500;
        this.width = 1000;
        
        // the variables to keep on drawing path on the map
        
        var prevx =0, prevy = 0;
        this.prev = function(x = null,y = null){
            
            if((x===null)&&(y===null)){
                return{
                    x:prevx,
                    y:prevy
                }
            }
            
            prevx = x;
            prevy = y;
            return this;
        }
        
        var _2DROBOT = new Array();
        
        this.read = function(fun){
            var THIS = this;
            $.post("components/dm.txt",
                   "hds=sa",
                   function(rets){
                        var num = "";
                        var row = new Array(); 
                        for(var i = 0; i< rets.length; i++){
                            if(!isNaN(rets.substring(i,i+1))){
                                if(rets.substring(i,i+1)==' '){
                                    //console.log("space");
                                }
                                else {
                                    num += rets.substring(i,i+1);
                                };
                            }
                            else if(rets.substring(i,i+1)==';'){
                                row.push(parseInt(num));
                                num = "";
                                _2DROBOT.push(row);
                                row = new Array();
                            }
                            else if(rets.substring(i,i+1)==']'){
                                _2DROBOT.push(row);
                                row = new Array();
                            }
                            else if(rets.substring(i,i+1)==','){
                                row.push(parseInt(num));
                                num = "";        
                            }
                        }
                        fun(THIS);
            });
        }
        
        
        
        
        
        this.image = function(x = null, y= null){
            if(_2DROBOT.length){
                if((x===null)&&(y===null)){
                    return _2DROBOT;
                }
                return _2DROBOT[x][y];
            } return false;
        }
        
        this.rgb = function(){
            if(this.image()){
                var rets = new Array();
                for(var i = 0; i < this.image().length; i ++){
                    var start = 0;
                    var rgb = new Array();
                    var all_rgb = new Array();
                    for(var ii = 0 ;ii < this.image()[i].length; ii++){
                        rgb.push(this.image()[i][ii]);
                        start++;
                        if(start==3){
                            start = 0;
                            all_rgb.push(rgb);
                            rgb = new Array();
                        }
                        
                    }
                    rets.push(all_rgb);
                    all_rgb = new Array();
                }
                return rets;
                
            }
            else return false;
        }
        
        
        this.comv = document.getElementById(id);
        this.cont = this.comv.getContext('2d');
        let strokeStyle = "#f8d523";
        this.color = function(cl = false){
            if(cl){
                strokeStyle = cl;
                return this;
            }
            return strokeStyle;
        }
        this.speedColor = function(speed){
            strokeStyle = `rgb(${(speed/MAX_SPEED)*255},0,0)`;
            return this;
        }
    }
    text(Text, size = 38){
        //context.clearRect(context.width,context.height,context.width,context.height);
        this.cont.font = `${size}pt Arial`;
        this.cont.fillStyle = 'cornflowerblue';
        this.cont.strokeStyle = 'blue';
        this.cont.fillText(Text,this.comv.width/2 - 150,this.comv.height/2 + 15);
        this.cont.strokeText(Text,this.comv.width/2 - 150,this.comv.height/2 + 15 ); 
    }
        
    window(x, y){
        var bbox = this.comv.getBoundingClientRect();
        return { x: x - bbox.left * (this.comv.width / bbox.width),
         y: y - bbox.top * (this.comv.height / bbox.height)
        };
    }
    
    transparent(arr){
        return (arr[0]==255)&&(arr[1]==255)&&(arr[2]==255);
    }
    
    point(x,y,width = 1){
            y += this.height/2;
            x += this.width/2;
            this.cont.strokeStyle = this.color();
            this.cont.lineWidth = width;
            this.cont.lineCap = 'round';
            this.cont.beginPath();
            this.cont.moveTo(x,y);
            this.cont.lineTo(x,y);
            this.cont.stroke();
            this.cont.closePath();
    }
    
    load(el,i = 0){
        var ii = 0;
        var intelvals = el.angle()[i].time;
        var numberOfPixels = el.SPEED()[i].values*el.angle()[i].time*0.001;
        var THIS = this;
        
        if(!isNaN(numberOfPixels)){
            var x = setInterval( function(){
                var nx = THIS.prev().x + Math.cos(el.angle()[i].values);
                var ny = THIS.prev().y + Math.sin(el.angle()[i].values);
                THIS.speedColor(el.SPEED()[i].values).point(nx,ny);
                ii++;
                THIS.prev(nx,ny);
                if(ii>numberOfPixels) clearInterval(x);
            },(el.angle()[i].time/numberOfPixels));
            i++;
            if(i<(el.size()-1)){
                setTimeout( function(){
                    THIS.load(el,i);
                },el.angle()[i].time);
            }
        }
        
    }
    
    clear(){
        this.cont.clearRect(0, 0, this.comv.width, this.comv.height);
    }
    move(data_turn){
        switch(data_turn){
                case "up-left":{
                    data_point.x = data_point.x-1;
                    data_point.y = data_point.y-1;
                    break;
                }
                case "up":{
                    data_point.y = data_point.y-1;
                    break;
                }
                case "up-right":{
                    data_point.x = data_point.x+1;
                    data_point.y = data_point.y-1;
                    break;
                }
                case "left":{
                    data_point.x = data_point.x-1;
                    break;
                }
                case "void":{

                    break;
                }
                case "right":{
                    data_point.x = data_point.x+1;
                    break;
                }
                case "bottom-left":{
                    data_point.x = data_point.x-1;
                    data_point.y = data_point.y+1;
                    break;
                }
                case "bottom":{
                    data_point.y = data_point.y+1;
                    break;
                }
                case "bottom-right":{
                    data_point.x = data_point.x+1;
                    data_point.y = data_point.y+1;
                    break;
                }
        }
        if(!(currentPath===-100))
            eval("PATH_THIS"+currentPath+'.push({"x":'+data_point.x+',"y":'+data_point.y+'})');
        putApoint(data_point);
    }
}