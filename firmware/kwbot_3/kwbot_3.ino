#include<ESP8266WiFi.h>
#include <SocketIoClient.h>
#include <ArduinoJson.h>
#include"modules/settup.h"


bool times = false;
bool connec = false;

//sensors sens = sensors();
motors *motorRight  = new motors();
motors motorLeft = motors();
path Kwbot = path();
SocketIoClient socket;

double currentTime = 0;



void setup() {
      Serial.begin(115200);


      /*##############################################################################################################
     * // start of network connectivity
     ###############################################################################################################*/
        //wifiCreate();
        //wifiConnect();
        connecting("CANALBOX-350A-2G","VpT4bsxuhs");
     /*##############################################################################################################
     *  // end of network connectivity
     ###############################################################################################################*/

     
      /*
        initialization of the sensor pin infrared and Ultrasonic
      */
      //sens.infraredAnalogPin() = A0;
      //sens.infraredMin() = 900;
      //sens.ultrasonicMin(15);
      //sens.UltrasonicPin(4,5);
      //sens.ultrasonicMin(20);
       /*
        initialization of the Right Motor Pins and settings
      */
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
      
      Kwbot.Motors(&motorLeft,motorRight).direction(90).speed(0).location(3).location(0,0,0).time(0);

  
    


     /*##############################################################################################################
     *  // Start of callback and event handler
     ###############################################################################################################*/

     socket.begin(socketServer, socketPort);     
     socket.on("connect",connecte);
     socket.on("event",event);
     socket.on("robots",robots);
     socket.on("forward",forward);
     socket.on("backward",backward);
     socket.on("stop",stopR);
     socket.on("turnleft",turnleft);
     socket.on("turnright",turnright);
     socket.on("play_test",play_test);
     socket.on("joystick",joystick);
     socket.on("joyStickStop",joyStickStop);
     socket.on("playingPaths", playPath);
     socket.on("rotate",rotate);
     socket.on("speeds",new_speed);

     /*##############################################################################################################
     *  // End of callback and event handler
     ###############################################################################################################*/
}

String inputString = ""; // A string to hold incoming data
bool stringComplete = false; // Whether the string is complete

void loop(){
      Kwbot.speed(2);
      if(Serial.available()){
        char inChar = (char)Serial.read();
        if(inChar == '\n'){
          stringComplete = true;
        }
        else {
            inputString += inChar; // Append character to string
        }
      }

      if (stringComplete) {
          if(inputString=="forward"){
             Kwbot.forward();
          }
          else Serial.println("Received: " + inputString); // For debugging
          decodeString(inputString); // Call decode function
          inputString = ""; // Clear the string for the next input
          stringComplete = false;
      }
      
      //pathTemplate();
//    Kwbot.speed(2).turnLeft();
//    delay(2000);
//    Kwbot.speed(2).turnRight();
//    delay(2000);
}
 /*##############################################################################################################
 *  // grobal function to convert from string to char pointer
 ###############################################################################################################*/

 void done(path nKwbot){
      Serial.println("done moving...");
     //Kwbot.speed(0).forward().Stop();
    //Kwbot.restartTimer(millis());
    //Serial.println(nKwbot.msg());
//    if(Kwbot.ended()){
//      Serial.println("done the movement");
//      Kwbot.speed(0).forward().Stop();
//    } else {
//      //playing(Kwbot.load(Kwbot.msg()).read(true));
//    }
    
 }
 void waiting(timer t){
    Serial.println("progress:");
    Serial.println(t.progress(millis() - currentTime));
 }
char* string2char(String command){
    if(command.length()!=0){
        char *p = const_cast<char*>(command.c_str());
        return p;
    }
}

void turnleft(const char *payload, size_t length){
  String subSpeed = String(payload).substring(0,4);
  if(!times&&connec)
    Kwbot.time(1).speed(subSpeed.toDouble()).TurnLeft();
  else if(times)
    Serial.println("there is a default path playing");
  else if(!connec)
    Serial.println("the robot is not connected");
  
}

void turnright(const char *payload, size_t length){
    String subSpeed = String(payload).substring(0,4);
    if(!times&&connec)
      Kwbot.time(1).speed(subSpeed.toDouble()).TurnRight();
    else if(times)
      Serial.println("there is a default path playing");
    else if(!connec)
      Serial.println("the robot is not connected");
}

void forward(const char *payload, size_t length){
  String subSpeed = String(payload).substring(0,4); 
  if(!times&&connec)
    Kwbot.time(1).speed(subSpeed.toDouble()).forward();
  else if(times)
    Serial.println("there is a default path playing");
  else if(!connec)
    Serial.println("the robot is not connected");
    
}
void backward(const char *payload, size_t length){
  String subSpeed = String(payload).substring(0,4);
  if(!times&&connec)
    Kwbot.time(1).speed(subSpeed.toDouble()).backward();
  else if(times)
    Serial.println("there is a default path playing");
  else if(!connec)
    Serial.println("the robot is not connected");
}

void stopR(const char *payload, size_t length){
    String subSpeed = String(payload).substring(0,4);
    Kwbot.time(1).speed(0).forward().Stop();
}

void event(const char *payload, size_t length){
  
}

void robots(const char *payload, size_t length){
    DynamicJsonDocument card_info(1024);
    JsonArray array = card_info.to<JsonArray>();
    JsonObject param1 = array.createNestedObject();
    param1["serial"] = String(robot_serial);
    param1["status"] = String(connec);
    String data_to_send;
    serializeJson(card_info,data_to_send);
    socket.emit("new_robots",string2char(data_to_send));
}

void connecte(const char *payload, size_t length){
   connec = true;
   Serial.println("the robot was registered success!!!");
}

void play_test(const char *payload, size_t length){
    Serial.println("playing the testing move...");
    pathTemplate();
    
}
void joyStickStop(const char *payload, size_t length){
    Kwbot.time(1).speed(0).forward().Stop();
    
}

void joystick(const char *payload, size_t length){
    
    String PAYLOAD = String(payload);
    String Speeds = "";
    String dir = "";
    bool started = false;
    for(unsigned int ii = 0; ii<PAYLOAD.length(); ii++){
          if(started){
             Speeds = Speeds + String(PAYLOAD[ii]);
          } else {
              if(String(PAYLOAD[ii])==String("*")){
                  started = true;  
              } else {
                  dir = dir + String(PAYLOAD[ii]);  
              }
          }
    }
    dir = String(dir.substring(0,5));
    Speeds = String(Speeds.substring(0,5));
    //Serial.println();
    Serial.println(Speeds);
    Kwbot.time(1).speed(Speeds.toFloat()).turn(dir.toFloat());
}

void playPath(const char *payload, size_t length){
    Kwbot.speed(0);
    playing(Kwbot.load(String(payload)).read(true));
}

void playing(pathread bb){
  Kwbot.speed(bb.speed).time(bb.angleTime).turn(bb.angle).ready(millis() - currentTime,done,waiting);
}

void rotate(const char *payload, size_t length){
  Kwbot.hindukira(true);
}

void new_speed(const char *payload, size_t length){
   double newSpeed = 0;
   newSpeed = String(payload).toDouble();
   Kwbot.speed(newSpeed);
}


void pathTemplate(){
    times = true;
    Kwbot.time(1000);
    Kwbot.speed(0.7);
    Kwbot.forward().time(1000).speed(0.8).backward().time(2000).speed(1.1).turnRight().time(2000).speed(1.1) .turnLeft().speed(1.33).time(3000).TurnLeft().time(5000).speed(0.8).TurnRight();
    Kwbot.speed(0).forward();
    times = false;
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

void wifiCreate(){
   WiFi.softAP(ssid, password);
}

void connecting(char* ssid, char* password){  
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
