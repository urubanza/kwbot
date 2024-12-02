var express = require('express');
var app = express();
var server = require('http').createServer(app);
var http = require("http");
var io = require('socket.io')(server);
var mysql = require('mysql');
var kwbot = require("./lib/kwbot");



app.use(express.static(__dirname + '/client'));

app.get('/', function (req, res, next) {
  console.log(req.params)
  res.sendFile(__dirname + '/admin.html');
});

io.on('connection', function (client) {
    console.log('Client  with '+client.id+' id connected successfull');
    client.on("turn", function(msg){
        console.log(msg);
        io.sockets.emit("turn",msg);
        io.sockets.emit("turn_str",msg.direction);
    });
	
	client.on("accelomets", function(msg){
		console.log(msg);
		io.sockets.emit("turn",msg);
        io.sockets.emit("turn_str",msg.direction);
	});
    
    client.on("play", function(msg){
        io.sockets.emit("play",msg);
    });
	
	io.sockets.emit("robots");
	client.on("new_robots", function(msg){
		robots_type._gets_(function(){
			console.log(robots_type.sql);
		}["serial"],[msg[0].serial]);
	})
});

server.listen(8080, () => {
  console.log('Kwrobot server ready on 8080');
});

