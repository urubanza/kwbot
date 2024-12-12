const WebSocket = require('ws');
class bridge{
    constructor(url,port){
        this.address = `ws://${url}:${port}`;
        var serverClient = new WebSocket(this.address);
        this.server = function(){
            return serverClient;
        };
    }

    Connected( fun){
        var THIS = this;
        this.server().on("open", function(){
            fun(THIS);
        });
    }
    onMessage( fun){
        this.server().on("message", function(data){
            var the_message = data;
            if (Buffer.isBuffer(data)) {
                the_message = data.toString('utf-8');
            } 
            fun(the_message);
        });
    }
}

exports.proxy = function(url, port){
    return new bridge(url,port);
}