const EVENTS_SERVER = "192.168.1.77";


function connectRealTime(){
    var socket = io.connect(`http://${EVENTS_SERVER}:8080`);

    socket.on('connect', function (data) {
        console.log(data);
        socket.emit('join', 'Browser administration connected...');
    });
}

