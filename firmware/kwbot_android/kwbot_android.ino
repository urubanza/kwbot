#include"modules/settup.h"


motors *motorRight  = new motors();
motors motorLeft = motors();
path Kwbot = path();

const int pingPin = 9; // Trigger Pin of Ultrasonic Sensor
const int echoPin = 10; // Echo Pin of Ultrasonic Sensor

const long min_distance = 40;
const long min_min_distance = 10; 
void setup() {
      Serial.begin(115200);

      Serial.begin(115200, SERIAL_8N1);

      //pinMode(LED_BUILTIN,OUTPUT);

      motorRight->speedPort(3);
      motorRight->input2(10);
      motorRight->input1(9);
      motorRight->diameter(90);
      motorRight->type(1);
      motorRight->minVoltage(0.68);
      motorRight->maxVoltage(10);
      motorRight->rpm(30);
      motorRight->init();
       /*
        initialization of the left Motor Pins and settings
      */
      motorLeft.speedPort() = 2;
      motorLeft.input1() = 5;
      motorLeft.input2() = 6;
      motorLeft.diameter() = 90;
      motorLeft.type(1);
      motorLeft.minVoltage(0.68);
      motorLeft.maxVoltage(10);
      motorLeft.rpm(30);
      motorLeft.init();
     
      Kwbot.Motors(motorRight,&motorLeft).direction(90).speed(0).location(3).location(0,0,0).time(0);
}

void loop(){

  int valueFound = readUltra();
  Serial.println(valueFound);
  if(valueFound==0){
    Kwbot.speed(1.13);
    Kwbot.forward();
    digitalWrite(2,HIGH);
    
  }

  else if(valueFound==1){
    Kwbot.speed(1.13);
    Kwbot.backward();
    digitalWrite(2,LOW);
    digitalWrite(3,LOW);
  }

  else if(valueFound==2){
    Kwbot.speed(0);
    Kwbot.Stop();
    digitalWrite(2,LOW);
    digitalWrite(3,LOW);
  }
  
} 


int readUltra(){
   long duration, inches, cm;
   pinMode(pingPin, OUTPUT);
   digitalWrite(pingPin, LOW);
   delayMicroseconds(2);
   digitalWrite(pingPin, HIGH);
   delayMicroseconds(10);
   digitalWrite(pingPin, LOW);
   pinMode(echoPin, INPUT);
   duration = pulseIn(echoPin, HIGH);
   inches = microsecondsToInches(duration);
   cm = microsecondsToCentimeters(duration);

   735691;

   int to_ret = 0;

   if(cm<min_min_distance){
      to_ret = 1;
   }
   else if(cm<min_distance){
      to_ret = 2; 
   }
   //Serial.print(inches);
   //Serial.print("in, ");
   //Serial.print(cm);
   //Serial.print("cm");
   //Serial.println();
   delay(100);
   return to_ret;
   //  
}
  

void phoneDetect(){
  if(Serial.available()){
     char inChar = Serial.read();
     if(inChar=='f'){
         Kwbot.speed(0.7);
         Kwbot.forward();
         //digitalWrite(LED_BUILTIN,HIGH);
     }
     else if(inChar=='x'){
        //digitalWrite(LED_BUILTIN,LOW);
        Kwbot.speed(0);
        Kwbot.turnRight(); 
     }

     else{
        Serial.println(inChar); 
     }
  }
}

void pathTemplate(){
    Kwbot.time(1000);
    Kwbot.speed(0.7);
    Kwbot.forward().time(1000).speed(0.8).backward().time(2000).speed(1.1).turnRight().time(2000).speed(1.1).turnLeft().speed(1.33).time(3000).TurnLeft().time(5000).speed(0.8).TurnRight();
    Kwbot.speed(0).forward();
}

long microsecondsToInches(long microseconds) {
   return microseconds / 74 / 2;
}

long microsecondsToCentimeters(long microseconds) {
   return microseconds / 29 / 2;
}
