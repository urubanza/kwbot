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
exports.path = ()=>{
    return new path();
}

exports.timer = ()=>{
    return new timer();
}