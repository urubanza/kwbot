var express = require('express');
var app = express();
var server = require('http').createServer(app);
var http = require("http");
var io = require('socket.io')(server);
var mysql = require('mysql');
var kwbot = require("./lib/kwbot");

let grobalPath = kwbot.path();
let globalTimer = kwbot.timer();

console.log(globalTimer.elapsed());

let grobalCounter = 0;

var previous_dir = "";
var previous_angle = 0;

let current_receiving = false;

app.use(express.static(__dirname + '../realtime_client'));

app.get('/', function (req, res, next) {
  console.log(req.params)
  res.sendFile(__dirname + '/admin.html');
});


var message = {
       "cont":"",
       "type":0,
       "vehicles_id":0
};

function expoN(offset,Val){
    //return offset+Math.exp(Val);
    return offset;
}

function stopRobot(io){
    io.sockets.emit("joyStickStop","meme");
}

function mapping(currentDP){
    var dA0 = 0.25;
    var dP0 = 2.9;
    currentDP = (currentDP*-1);
    currentDP = (dA0/dP0)*currentDP;   
    currentDP = currentDP + 0.5;
    console.log(currentDP);
    return currentDP*expoN(0.55,currentDP);
}

function mapping_(currentDP){
    var dA0 = 0.25;
    var dP0 = 2.9;
    currentDP = (currentDP);
    currentDP = (dA0/dP0)*currentDP;   
    currentDP = currentDP + 0.3;
    return currentDP*expoN(0.55,currentDP);
}

function sendRealTimeMoving(path,io, position = 0){
    var thePath = kwbot.path();
    thePath.load(path);
    //console.log(thePath.angle());
    
    if(position<thePath.size()){
        console.log(thePath.angle()[position]);
        io.sockets.emit("joystick",thePath.angle()[position].values+"*"+thePath.SPEED()[position].values);
        setTimeout( function(){
           position++;
           sendRealTimeMoving(thePath.generate().string(),io,position);
        },thePath.angle()[position].time);   
    } else if(position==thePath.size()){
          io.sockets.emit("joyStickStop","fromPlay");
          console.log("the path has been ended success");
    }
    
}

function ScanForward(io){
    
}

var accelermtsTimes = 0;
var savedPath = {
    "eventName":"",
    "speed":""
}
var allPath = [];


function sendUiData(data, io, id = null){
    io.sockets.emit("ui_updates",data);
}


io.on('connection', function (client) {
    console.log('Client  with '+client.id+' id connected successfull');
    
    client.on("savePathText", function(msg){
        console.log(grobalPath.SPEED());
        kwbot.pathxx.add( function(ne){
            grobalPath.init();
            console.log();
        },["path_name","coordinates","user_id"],[msg,grobalPath.generate().string(),1],"creation_date");
    });
    
    client.on("connect_plate_reader", function(msg){
        console.log(msg);
    });
    
    client.on("playPathsQuik", function(msg){
        kwbot.pathxx._gets_(function(){
            console.log(kwbot.pathxx.records()[0].coordinates);
            sendRealTimeMoving(kwbot.pathxx.records()[0].coordinates,io);
            grobalPath.init();
            //io.sockets.emit("playingPaths",kwbot.pathxx.records()[0].coordinates);
        },["path_id"],[msg]);
    })
    
    client.on("turn", function(msg){
        console.log(msg);
        io.sockets.emit("turn",msg);
        io.sockets.emit("turn_str",msg.direction);
    });
    
    client.on("rotate", function(msg){
        console.log(msg);
        io.sockets.emit("rotate",msg);
    });
    
    client.on("speed", function(speeds){
        if(speeds==""){
           speeds = 0; 
        } else if(speeds==null){
            speeds = 0; 
        } else if(isNaN(speeds)){
            speeds = 0; 
        }
        
        console.log(" new speed: "+ speeds);
        io.sockets.emit("speeds",speeds);
		
    })
	
	client.on("join", function(){
		console.log("the browser is connected ");

        var AreaChartData = {
            'labels':["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            'data':[15000, 10000, 4000, 2000, 1000, 20000, 5000, 2100, 2200, 30000, 5500, 4000]
        };

        var pieChartData = {
            'labels':["Transport", "Multimedi", "Inactive"],
            'data':[55, 30, 15]
        };

        sendUiData({
            "AreaChartData":AreaChartData,
            "pieChartData":pieChartData
        }, io);
	});
    
    client.on("disconection", function(){
        console.log("a client is disconnected...");
    })
    
    client.on("play_test", function (msg) {
        io.sockets.emit("play_test", "1");
    });

    client.on("play_test", function (msg) {
        console.log(msg);
    });

    client.on("new message", function (msg) {
        console.log(msg);
    });
	
	client.on("accelomets", function(msg){
//              if(previous_dir==msg.direction){
//                  console.log("the same direction a previous was given");
        //              } else {
        io.sockets.emit("accelerometer", msg);
                  console.log(msg);
                  if(msg.direction=="void"){
                      io.sockets.emit("stop","0");
                  } else {
                       if((parseInt(msg.xval)<-1)&&(parseInt(msg.yval)==0)){
                            io.sockets.emit("forward",mapping(msg.xval));
                            savedPath.eventName = "forward";
                            savedPath.speed = ""+mapping(msg.xval)+"";
                            allPath.push(savedPath);
                        } else if((parseInt(msg.yval)<-1)&&(parseInt(msg.xval)==0)){
                            io.sockets.emit("turnleft",mapping(msg.yval));
                            savedPath.eventName = "turnleft";
                            savedPath.speed = ""+mapping(msg.yval)+"";
                            allPath.push(savedPath);
                        } else if((parseInt(msg.xval)>1)&&(parseInt(msg.yval)==0)){
                            io.sockets.emit("backward",mapping_(msg.xval));
                            savedPath.eventName = "backward";
                            savedPath.speed = ""+mapping_(msg.xval)+"";
                            allPath.push(savedPath);
                        } else if((parseInt(msg.yval)>1)&&(parseInt(msg.xval)==0)){
                            io.sockets.emit("turnright",mapping_(msg.yval));
                            savedPath.eventName = "backward";
                            savedPath.speed = ""+mapping_(msg.yval)+"";
                            allPath.push(savedPath);
                        } else {
                           io.sockets.emit("stop","0"); 
                            if(allPath.length>0)
                                io.sockets.emit("revealB","0");
                        } 
                  }
                  previous_dir = msg.direction;
              //}
	});
    
    client.on("joyStick", function(msg){
        current_receiving = true;
		io.sockets.emit("movemove","shdhjdfs");
        
        if(previous_angle==msg.dir){
            console.log("the same direction provided"+msg.dir);
        } else {
            if(grobalPath.size()==0){
                 globalTimer.clear();
                 globalTimer.paused = false;     
            }
            
            if(globalTimer.paused){
                globalTimer.clear();
                globalTimer.paused = false;
            } else {
                var time = globalTimer.elapsed();
                console.log(msg);
                grobalPath
                    .putAngle(msg.dir,time)
                    .putSpeed(msg.speed,time)
                    .putPoint(msg.speed,msg.speed);
                previous_angle = msg.dir;
                io.sockets.emit("joystick",msg.dir+"*"+msg.speed);
                globalTimer.clear();
                io.sockets.emit("the_PathSaverIndex","one");
            }
            
            //console.log(grobalPath.generate().string());
        }
        setTimeout( function(){
           current_receiving = false;
        },500);
    });
    
    client.on("shaking", function(msg){
        console.log("Shakings....");
        io.sockets.emit("shakk","SHAKE");
    })
    
//    setInterval( function(){
//        if(!current_receiving) stopRobot(io);
//    },100);
    
    
    
    client.on("joyStickStop", function(msg){
        //console.log(msg);
        globalTimer.paused = true;
        //console.log(globalTimer.elapsed());
        globalTimer.clear();
        
        io.sockets.emit("joyStickStop","sadafd");
    });
    
    client.on("play", function(msg){
        io.sockets.emit("play",msg);
    });
	
	io.sockets.emit("robots");
	client.on("new_robots", function(msg){
		//console.log(kwbot);
		kwbot.vehicles()._gets_(function(){
			
			if(kwbot.vehicles().PIP.size>0){
				   message.cont = "robot with "+msg[0].serial+"  serial number is connected success";
                   message.type = 1;
                   message.vehicles_id = kwbot.vehicles().PIP.JS(0).devices_id;
			} else {
				   message.cont = "robot with unkown serial number is trying to connect to the server";
			}
			
			kwbot.logs().add( function(newLog){
				io.sockets.emit("notify",message);
                io.sockets.emit("connect","1");
			},["cont","type","vehicles_id"],[message.cont,message.type,message.vehicles_id],"added");
			
			console.log(kwbot.vehicles().PIP);
		},["serial"],[msg[0].serial]);
	});
    
    client.on("Android_connect", function(msg){
        message.cont = "an android device with "+msg.username+ " is aconnected success";
        message.type = 1;
        message.vehicles_id = 0;
        io.sockets.emit("notify", message);
    });
    
    client.on("GPSVS", function(msg){
        console.log(msg);
    });
    client.on("UltrasonData", function(msg){
        io.sockets.emit("buzzer",30);
        console.log(msg);
    });
    
    client.on("UltraStop", function(msg){
        io.sockets.emit("joyStickStop",0);
    })

    client.on("newOTG", function(msg){
       console.log(msg);
    })
});

server.listen(8080, () => {
  console.log('Kwrobot server ready on 8080');
});

