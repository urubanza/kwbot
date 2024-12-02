var express = require('express');
var app = express();
var server = require('http').createServer(app);
var http = require("http");
var io = require('socket.io')(server);


server.listen(8080, () => {
  console.log('Kwrobot server ready on 8080');
});

app.use(express.static(__dirname + '/assets'));

app.get('/', function (req, res, next) {
  console.log(req.params)
  res.sendFile(__dirname + '/visual.html');
});

io.on('connection', function(c){
    console.log("Connected with :" + c.id +" id");
});