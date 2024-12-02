#include <Arduino.h>
#include <ESP8266WiFi.h>
#include <ArduinoJson.h>
#include <WebSocketsClient.h>
#include <SocketIOclient.h>
#include <Hash.h>

#include"modules/settup.h"


bool times = false;
bool connec = false;

//sensors sens = sensors();
motors *motorRight  = new motors();
motors motorLeft = motors();
path Kwbot = path();
SocketIOclient socketIO;
double currentTime = 0;


void socketIOEvent(socketIOmessageType_t type, uint8_t * payload, size_t length) {
    switch(type) {
        case sIOtype_DISCONNECT:
            Serial.printf("[IOc] Disconnected!\n");
            break;
        case sIOtype_CONNECT:
            Serial.printf("[IOc] Connected to url: %s\n", payload);

            // join default namespace (no auto join in Socket.IO V3)
            socketIO.send(sIOtype_CONNECT, "/");
            break;
        case sIOtype_EVENT:
            Serial.printf("[IOc] get event: %s\n", payload);
            break;
        case sIOtype_ACK:
            Serial.printf("[IOc] get ack: %u\n", length);
            hexdump(payload, length);
            break;
        case sIOtype_ERROR:
            Serial.printf("[IOc] get error: %u\n", length);
            hexdump(payload, length);
            break;
        case sIOtype_BINARY_EVENT:
            Serial.printf("[IOc] get binary: %u\n", length);
            hexdump(payload, length);
            break;
        case sIOtype_BINARY_ACK:
            Serial.printf("[IOc] get binary ack: %u\n", length);
            hexdump(payload, length);
            break;
    }
}


void setup() {

    Serial.begin(115200);
     /*##############################################################################################################
     * // start of network connectivity
     ###############################################################################################################*/
     wifiConnect();
     /*##############################################################################################################
     *  // end of network connectivity
     ###############################################################################################################*/


     motorRight->speedPort(14);
      motorRight->input2(15);
      motorRight->input1(13);
      motorRight->diameter(90);
      motorRight->type(1);
      motorRight->minVoltage(0.68);
      motorRight->maxVoltage(10);
      motorRight->rpm(30);
      motorRight->init();
       /*
        initialization of the left Motor Pins and settings
      */
      motorLeft.speedPort() = 12;
      motorLeft.input1() = 0;
      motorLeft.input2() = 2;
      motorLeft.diameter() = 90;
      motorLeft.type(1);
      motorLeft.minVoltage(0.68);
      motorLeft.maxVoltage(10);
      motorLeft.rpm(30);
      motorLeft.init();
      
      Kwbot.Motors(motorRight,&motorLeft).direction(90).speed(0).location(3).location(0,0,0).time(0);


    String ip = WiFi.localIP().toString();
    Serial.printf("[SETUP] WiFi Connected %s\n", ip.c_str());
    socketIO.begin("192.168.1.71", 8880, "/socket.io/?EIO=4");
    socketIO.onEvent(socketIOEvent);
}

unsigned long messageTimestamp = 0;

void loop() {
    socketIO.loop();
    uint64_t now = millis();
    if(now - messageTimestamp > 2000) {
        messageTimestamp = now;
        // creat JSON message for Socket.IO (event)
        DynamicJsonDocument doc(1024);
        JsonArray array = doc.to<JsonArray>();
        // add evnet name
        // Hint: socket.on('event_name', ....
        array.add("event_name");
        // add payload (parameters) for the event
        JsonObject param1 = array.createNestedObject();
        param1["now"] = (uint32_t) now;

        // JSON to String (serializion)
        String output;
        serializeJson(doc, output);

        // Send event
        socketIO.sendEVENT(output);

        // Print JSON for debugging
        Serial.println(output);
    }

}

void wifiConnect(){
  Serial.setDebugOutput(true);
  Serial.println();
  Serial.println();
  Serial.println();

  for (uint8_t t = 4; t > 0; t--){
     Serial.printf("[SETUP] BOOT WAIT %d...\n", t);
     Serial.flush();
     delay(1000);
  }
    
  WiFi.mode(WIFI_STA);
  WiFi.begin(ssid, password);  
  while (WiFi.status() != WL_CONNECTED){
      delay(100);
      Serial.print(".");
  }

  Serial.println("");
  Serial.print("Connected to ");
  Serial.println(ssid);
  Serial.print("IP address: ");
  Serial.println(WiFi.localIP());
}
