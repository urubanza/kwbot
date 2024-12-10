package com.pip.sensorskwbot.comminication.usb;

import android.os.Message;

public class SerialTimers {
    private boolean timeToSend = true;

    private boolean sendings = true;
    private String previousMessge = "";

    public void update(){
        if(timeToSend){
            timeToSend = false;
            return;
        }
        timeToSend = true;
    }

    public void message(String _message){
        if(_message.equals(previousMessge)){
            sendings = false;
            return;
        }
        sendings = true;
        previousMessge = _message;
    }

    public String message(){
        return previousMessge;
    }

    public boolean vlid(){
        return sendings;
    }

    public boolean ready(){
        return timeToSend;
    }

}
