var express = require('express');
var app = express();
var server = require('http').createServer(app);
var http = require("http");
var io = require('socket.io')(server);
var mysql = require('mysql');

class DistanceRSSI{
    constructor(size){
        // the total size of values to be saved
        var _size = size;
        // the values to be used 
        var _values = [];
        // the current index of the values
        var _index = 0;
        this.size =  function(){
            return _size;
        }
        this.values = function(){
            return _values;
        }
        this.index = function(){
            return _index;
        }
        
        for(var i = 0; i < _size; i++){
            _values[i] = 0;
        }
        
        this.push = function(val){
            if(!isNaN(val)){
                if(_size==_index){
                    _index = 0;
                } else if(_size<_index){
                    console.log("there was error adding some information about RSSI: the given value is greater than expected");
                    _index = 0;
                } else if(_size>_index){
                    _values[_index] = val;
                    _index++;
                }
            } else console.log("given data are not valid");
        }
        
        this.length = function(){
            return _values.length;
        }
        
    }
    
    
    full(){
        return (this.values().indexOf(0)<0);
    }
    mean(){
        return (this.sum()/this.size())
    }
    
    sum(){
        var rets = 0;
        for(var i = 0; i <this.length(); i++){
            rets += this.values()[i]; 
        }
        return rets;
    }
    SD(){
       var rets = 0;
       for(var i = 0; i < this.length(); i++){
           rets += (this.values()[i] - this.mean())*(this.values()[i] - this.mean());  
       }
       return rets;
    }
    // Sample standard deviation (s)
    SSD(){
        return (1/this.size()-1)*this.SD();
    }
    
    // population standard deviation (σ)
    
    PSD(){
        return (1/this.size())*this.SD();
    }
    
}

var RSSI = new DistanceRSSI(300);

app.use(express.static(__dirname + '/assets'));

app.get('/', function (req, res, next) {
  console.log(req.params)
  res.sendFile(__dirname + '/distance.html');
});
io.on('connection', function (client) {
    console.log('Client  with '+client.id+' id connected successfull');
    client.on("receivs", function(msg){
        RSSI.push(msg[0].value);
        console.log(msg[0].value);
        
        if(RSSI.full()){
            var vars = {
                "SSD":RSSI.SSD(),
                "PSD":RSSI.PSD(),
                "MEA":RSSI.mean(),
                "ALL":RSSI.values()
            }
            io.sockets.emit("vars",vars);
        }
        else {
            //console.log("the current index : "+ RSSI.index());
            //console.log("the first 0 : "+ RSSI.values().indexOf(0));
        }
    });
});

server.listen(8080, () => {
  console.log('Kwrobot server ready on 8080');
});

