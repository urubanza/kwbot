package com.pip.sensorskwbot.comminication.usb;


import android.util.Log;

public class KwbotSerial {
    public static enum MESSAGE_TYPE {NONE,SONAR,SOCKET};

    private MESSAGE_TYPE TheType = MESSAGE_TYPE.NONE;

    private static String SONAR_START = "#";
    private static String SONAR_END = "%";

    private static String SOCKET_START = "&";
    private static String SOCKET_ENDS = "*";

    private String _val = "";

    public KwbotSerial(String incoming){
        extrems Sonar = new extrems(incoming,SONAR_START,SONAR_END);
        extrems Socket = new extrems(incoming,SOCKET_START,SOCKET_ENDS);
        if(Sonar.found()){
            TheType = MESSAGE_TYPE.SONAR;
            _val = Sonar.val();
        }
        if(Socket.found()){
            TheType = MESSAGE_TYPE.SOCKET;
            _val = Socket.val();
        }

    }
    public MESSAGE_TYPE type(){
        return TheType;
    }

    public String val(){
        return _val;
    }

    public double ultrasonic(){
        if(TheType!=MESSAGE_TYPE.SONAR) return -1;
        double toRets;
        try{
            toRets = Double.parseDouble(val());
        }
        catch (Exception e){
            Log.e("COBNVERTINF",e.getMessage());
            return -1;
        }
        return toRets;
    }

    private class extrems{
        private int Start;
        private int Ends;

        private String mess;

        public extrems(String vl, String start, String ends){
            Start = vl.indexOf(start);
            Ends = vl.indexOf(ends);
            if(found()) mess = vl.substring(Start+1,Ends);
        }

        public boolean found(){
            return (Start>-1)&&(Ends>-1)&&(Start<Ends);
        }

        public String val(){
            return mess;
        }


    }
}
