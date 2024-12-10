package com.pip.sensorskwbot.utils;

import android.util.Log;

public class  pTimber {
    public static void e(String mess){
        Log.e("PIP Kwbot Error : ",mess);
    }

    public static void e(Exception e,String mess){
        Log.e("PIP Kwbot Error : ",mess + " : " + e.getMessage());
    }
    public static void i(String mess){
        Log.i("PIP Kwbot info : ",mess);
    }

    public static void d(String mess){
        Log.d("PIP Kwbot info : ",mess);
    }
    public static void d(String mess, Exception e){
        Log.d("PIP Kwbot info : ",mess + " : " + e.getMessage());
    }

    public static void d(Throwable t, String mess) {
        Log.d("PIP Kwbot info : ",mess + " : " + t.getMessage());
    }

    public static void d(String s, String mess) {
        Log.d(s,mess );
    }

    public static void e(String s, String mess) {
        Log.e(s,mess);
    }
}
