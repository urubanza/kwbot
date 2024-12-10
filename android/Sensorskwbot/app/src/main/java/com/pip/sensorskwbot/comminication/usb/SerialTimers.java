package com.pip.sensorskwbot.comminication.usb;

public class SerialTimers {
    private boolean timeToSend = true;

    public void update(){
        if(timeToSend){
            timeToSend = false;
            return;
        }
        timeToSend = true;
    }

    public boolean ready(){
        return timeToSend;
    }

}
