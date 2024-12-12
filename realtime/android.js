const WebSocket = require('ws');




class android{
    constructor(Port, fun){
        var oldSocketIO;
        var wss = new WebSocket.Server({ port: Port });
        console.log("WebSocket server started on ws://localhost:8000");
        wss.on('connection', function(socket) {
            console.log('Android Robot connected only');
            fun(socket);
            // socket.on('message', (message) => {
            //     try{
            //         var rec = JSON.parse(message);
            //         console.log(rec);
            //     }
            //     catch(e){
            //         console.error("Failed to decode");
            //         console.log(`Received: ${message}`);
            //     }
            // });
        }); 
        this.IO = function(io){
            console.log("AHA");
            oldSocketIO = io;
        }

        this.SOCKET = function(){
            return oldSocketIO;
        }
    }


};

exports.server = function(Port, fun){
    new android(Port, fun);
}

