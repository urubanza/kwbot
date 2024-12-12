package com.pip.sensorskwbot.modes;

public class CommandGenerator {

    public String forward(float Speed){
        return "forward<"+Speed+">%";
    }

    public String stop(){
        return "stop%";
    }
}
