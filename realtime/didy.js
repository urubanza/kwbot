var express = require('express');
var app = express();
var server = require('http').createServer(app);
var http = require("http");
var io = require('socket.io')(server);

app.use(express.static(__dirname + '/client'));

app.get('/', function (req, res, next) {
  console.log(req.params)
  res.sendFile(__dirname + '/didy.html');
});
app.get('/port', function (req, res, next) {
  console.log(req.params)
  res.send({port:8080});
});
io.on('connection', function (client) {
    console.log("Client Connected with id"+client.id);
    client.on("notifyPhone", function(msg){
        console.log(msg);
        io.sockets.emit("notifications",msg);
    })
})

server.listen(8080, () => {
  console.log('Kwrobot server ready on 8080');
});