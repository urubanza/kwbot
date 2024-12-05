package kwbotControler;

import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.PorterDuff;
import android.util.Log;
import android.view.animation.AnimationUtils;

public class DirectionRoller implements angleLister {
    // the radius of the big cirlce
    private float MainCircleRadius;
    // the radius of the roller element , small circle
    private float RollerRadius;
    // the distance from the edge to the x position of the big circle
    private float Mainx;
    // the distance from the edge to the y position of the big circle
    private float Mainy;
    // the distance from the edge to the x position of the roller, small circle
    private float Rollerx;
    // the distance from the edge to the y position of the roller , small circle
    private float Rollery;
    // color of the big and small circle respectively
    private Paint big, small, mainScreenColor;

    private float ScreenArea;
    private float ScreenHeight, ScreenWidth;

    private double Angle = -1000;

    public DirectionRoller setAngle(double a){
        this.Angle = a;
        return this;
    }

    @Override
    public void onAngleChanged(DirectionRoller d) {
        d.setAngle(d.getAngle());
    }

    @Override
    public void onStop(DirectionRoller d) {
        d.setAngle(-1000);
    }

    public class anoth {

    }

    public double getAngle(){
        return Angle;
    }
    public double getAngleDeg(){
        return (getAngle()*180)/Math.PI;
    }


    public DirectionRoller(){
        MainCircleRadius = 0;
        RollerRadius = 0;
        Mainx = 0;
        Mainy = 0;
        Rollerx = 0;
        Rollery = 0;
        this.mainScreenColor = new Paint();
        this.mainScreenColor.setColor(Color.WHITE);
    }

    public DirectionRoller setMainScreenColor(int c) {
        this.mainScreenColor.setColor(c);
        return this;
    }

    private float getRollerx(){
        float d2d1 = MainCircleRadius - RollerRadius;
        float r2r1 = d2d1/2;
        return Mainx + r2r1;
    }

    private float getRollery(){
        float d2d1 = MainCircleRadius - RollerRadius;
        float r2r1 = d2d1/2;
        return Mainy + r2r1;
    }

    public DirectionRoller setScreenArea(int height, int width) {
        this.ScreenArea = height*width;
        ScreenHeight = height;
        ScreenWidth = width;
        return this;
    }

    public DirectionRoller Paints(Paint big, Paint small){
        this.big = big;
        this.small = small;
        return this;
    }


    public DirectionRoller(float Radius,float SRadius){
        if(Radius>SRadius){
            MainCircleRadius = Radius;
            RollerRadius = SRadius;
            Mainx = MainCircleRadius*2;
            Mainy = MainCircleRadius*2;
            Rollerx = getRollerx();
            Rollery = getRollery();
        } else {
            MainCircleRadius = SRadius;
            RollerRadius = Radius;
            Mainx = MainCircleRadius*2;
            Mainy = MainCircleRadius*2;
            Rollerx = getRollerx();
            Rollery = getRollery();
        }
    }

    public DirectionRoller size(float Radius, float SRadius){
        if(Radius>SRadius){
            MainCircleRadius = Radius;
            RollerRadius = SRadius;
            Mainx = MainCircleRadius*2;
            Mainy = MainCircleRadius*2;
            Rollerx = getRollerx();
            Rollery = getRollery();
        } else {
            MainCircleRadius = SRadius;
            RollerRadius = Radius;
            Mainx = MainCircleRadius*2;
            Mainy = MainCircleRadius*2;
            Rollerx = getRollerx();
            Rollery = getRollery();
        }
        return this;
    }

    public DirectionRoller position(float x, float y){
        Mainx = x;
        Mainy = y;
        Rollerx = Mainx + (((MainCircleRadius*2)-(RollerRadius*2)))/2;
        Rollery = Mainy + (((MainCircleRadius*2)-(RollerRadius*2)))/2;
        return this;
    }

    public DirectionRoller put(Canvas c){
        Rollerx = Mainx;
        Rollery = Mainy;
        c.drawCircle(Mainx,Mainy,MainCircleRadius,big);
        c.drawCircle(Rollerx,Rollery,RollerRadius,small);
        return this;
    }

    public DirectionRoller remove(Canvas c){
        c.drawColor(this.mainScreenColor.getColor(), PorterDuff.Mode.CLEAR);
        c.drawCircle(Mainx,Mainy,MainCircleRadius,this.mainScreenColor);
        return this;
    }

    public DirectionRoller move(Canvas c, float x, float y){
        remove(c);
        c.drawCircle(Mainx,Mainy,MainCircleRadius,this.big);
        c.drawCircle(Rollerx,Rollery,RollerRadius,this.big);
        c.drawCircle(x,y,RollerRadius,this.small);
        Rollerx = x;
        Rollery = y;
        if(RollerOutOfBound(x,y)){
            this.onStop(this);
            return this.remove(c).put(c);
        }

        else {
            float Cx = (x - Mainx) / MainCircleRadius;
            float Cy = (Mainy - y) / MainCircleRadius;
            if((Cx>0)&&(Cy>0)){
                Angle = Math.atan(Cy/Cx);
            } else if((Cy<0)&&(Cx<0)){
                Angle = 2.5*Math.PI - Math.abs(Math.atan(Cy/Cx) - (1.5*Math.PI));
            } else if((Cx<0)&&(Cy>0)){
                Angle = Math.PI + Math.atan(Cy/Cx);
            } else if((Cy<0)&&(Cx>0)){
                Angle = 2*Math.PI + Math.atan(Cy/Cx);
            }
            this.onAngleChanged(this);
            return this;
        }
    }

    public boolean inRange(float x, float y){
        boolean rets = true;
        if(x<Mainx)
            rets = false;
        else if(y<Mainy)
            rets = false;
        else {

        }
        return rets;
    }

    public boolean RollerOutOfBound(float x, float y){
        boolean rets = false;
        if(xSquareySquare(pointerXposLeft(x),pointerYposBottom(y))>(RadiusSquare())) {
            return true;
        } else {
            if(x<(Mainx-MainCircleRadius))
                return true;
        }
        return rets;
    }

    private double xSquareySquare(float x, float y){
        float newX = Math.abs(MainCircleRadius-Mainx) - x;
        float newY = Math.abs(MainCircleRadius-Mainy) - y;
        return Math.pow(newX,2) + Math.pow(newY,2);
    }

    private float pointerYposBottom(float y){
        return Math.abs(this.MainCircleRadius - y);
    }
    private float pointerXposLeft(float x){
        return Math.abs(this.MainCircleRadius - x);
    }
    private float pointerXposRight(float x){
        return ScreenWidth - (x+MainCircleRadius);
    }
    private float pointerYposTop(float y){
        return ScreenHeight - (y+MainCircleRadius);
    }

    private float RadiusSquare(){
        return (float)Math.pow(MainCircleRadius,2);
    }
}
