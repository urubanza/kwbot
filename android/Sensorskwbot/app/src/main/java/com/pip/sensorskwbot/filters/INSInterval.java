package com.pip.sensorskwbot.filters;

public class INSInterval {
    private double x = 0,y = 0,z = 0;
    private double sensitivity;

    public INSInterval(double sens){
        this.sensitivity = sens;
    }

    public boolean Sensed(double X, double Y, double Z){
        boolean Sensed = (abs(x-X) > sensitivity) && (abs(y-Y) > sensitivity ) && (abs(z-Z) > sensitivity);
        if(Sensed) update(X,Y,Z);
        return Sensed;
    }

    private void update(double X, double Y, double Z){
        x = X;
        y = Y;
        z = Z;
    }

    private static double abs(double v){
        if(v<0) return (v*-1);
        return v;
    }


}
