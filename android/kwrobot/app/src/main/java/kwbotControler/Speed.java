package kwbotControler;

import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;

public class Speed implements speedLister{
    // speed amount in meter/second
    private double amounts;
    private int ScreenHeight;
    private int ScreenWidth;
    private Paint big, small;

    private boolean isTouchInRange = false;

    private final static float SPEED_DISPLAY_PADDING = 50;
    private final static float SPEED_DISPLAY_PADDING_LEFT = 80;
    private final static double MIN_SPEED = 0.5f;

    private double RPM = 3000;
    // Wheel diameter in mm
    private double WheelDiameter = 90;
    public Speed(int ScreenH,int ScreenW){
        this.ScreenWidth = ScreenW;
        this.ScreenHeight = ScreenH;
        this.big = new Paint();
        this.small = new Paint();
        this.big.setColor(Color.argb(50,80,60,67));
        this.small.setColor(Color.BLACK);
    }
    public Speed rpm(double rpm){
        RPM = rpm;
        return this;
    }
    public Speed diameter(double d){
        WheelDiameter = d;
        return this;
    }
    public double getAmounts(){
        if((this.amounts-1)<0)
            return this.amounts;
        return this.amounts-1;
    }
    public Speed Paints(Paint big, Paint small){
        this.big = big;
        this.small = small;
        return this;
    }

    public Speed change(float x, float y){
        if(inxRange(x)&&inyRange(y)){
            this.amounts = ((Speed.SPEED_DISPLAY_PADDING*this.MaxSpeed())/y);
            this.onSpeedChanged(this);
        }
        return this;
    }
    public boolean inRange(){
        return isTouchInRange;
    }
    public Speed put(Canvas c){
        draw(c);
        return this;
    }
    public Speed init(Canvas c){
        this.amounts = Speed.MIN_SPEED;
        draw(c);
        return this;
    }
    private void draw(Canvas c){
        c.drawRect(this.ScreenWidth-Speed.SPEED_DISPLAY_PADDING_LEFT,this.ScreenHeight-Speed.SPEED_DISPLAY_PADDING,this.ScreenWidth-20,Speed.SPEED_DISPLAY_PADDING,big);
        c.drawRect(this.ScreenWidth-Speed.SPEED_DISPLAY_PADDING_LEFT,this.ScreenHeight-Speed.SPEED_DISPLAY_PADDING,this.ScreenWidth-20,this.mapY(),small);
    }
    private boolean inxRange(float x){
       return (x>=(this.ScreenWidth-Speed.SPEED_DISPLAY_PADDING_LEFT)&&(x<=this.ScreenWidth-20));
    }
    private boolean inyRange(float y){
        return ((y>=Speed.SPEED_DISPLAY_PADDING)&&(y<=(this.ScreenHeight-Speed.SPEED_DISPLAY_PADDING)));
    }

    private float mapY(){
        if(this.amounts<=Speed.MIN_SPEED){
            this.amounts = Speed.MIN_SPEED;
            return (float)(this.ScreenHeight - 100);
        } else if(this.MaxSpeed()<=this.amounts){
            this.amounts = this.MaxSpeed();
            return (float)50;
        } else {
            return this.ScreenHeight - (float)(this.ScreenHeight - (Speed.SPEED_DISPLAY_PADDING*this.MaxSpeed()/this.amounts));
        }
    }

    private double MaxSpeed(){
        return WheelDiameter*Math.PI*RPM/60000;
    }

    @Override
    public void onSpeedChanged(Speed s) {
        isTouchInRange = true;
    }
}
