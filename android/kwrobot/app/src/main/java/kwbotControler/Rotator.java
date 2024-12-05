package kwbotControler;

import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.RectF;
import android.os.Build;

import androidx.annotation.RequiresApi;

public class Rotator {
    // the image data to be kept from the given image
    private double imgData[][];
    // the canvas to be drawing from
    private Canvas C;
    // the main circle to display all needed information
    private double mainCircle;
    private double smallCircle;

    // the color of the rotator

    private Paint color,mainScreenColor;

    private angles angle;

    private cord Coordinate;


    // a the constructor of the Rotator

    public Rotator(Canvas c){
        this.init(c);
        this.Coordinate = new cord(0,0);
    }

    private void init(Canvas c){
        this.C = c;
        this.color = new Paint();
        this.color.setColor(Color.BLACK);
        this.angle = new angles(0,0);
    }

    public Rotator setMainScreenColor(int c) {
        this.mainScreenColor = new Paint();
        this.mainScreenColor.setColor(c);
        return this;
    }

    public Rotator(Canvas c, double x, double y){
        this.init(c);
        this.Coordinate = new cord(x,y);
    }

    public Rotator setColor(Paint color) {
        this.color = color;
        return this;
    }

    // the function to put the direction roller


    public Rotator put(){
        this.put(this.C);
        return this;
    }

    //@RequiresApi(api = Build.VERSION_CODES.LOLLIPOP)
    public Rotator put(Canvas c){
        c.drawCircle((float)this.Coordinate.X(),
                (float)this.Coordinate.Y(),
                (float)this.mainCircle,
                this.color);
        c.drawCircle((float) this.Coordinate.X(),
                (float) this.Coordinate.Y(),
                (float) this.smallCircle,
                this.mainScreenColor);

        Paint p = new Paint();
        p.setColor(Color.RED);
        int y = c.getHeight();
        RectF rectf = new RectF(345, y - 605, 455, y-495);
        c.drawArc(rectf, 295 , -45, true, p);
        return this;
    }
    // a function to set all values of circles
    public Rotator circles(double s, double b){
        this.mainCircle = b;
        this.smallCircle = s;
        return this;
    }
    // the previous and current angle setted by the rotator
    private class angles {
        private double prev;
        private double curr;
        public angles(double p, double c){
            this.prev = p;
            this.curr = c;
        }

        public double getCurr() {
            return curr;
        }

        public double getPrev(){
            return prev;
        }

        public void setCurr(double curr) {
            this.curr = curr;
        }

        public void setPrev(double prev) {
            this.prev = prev;
        }

        public double diff(){
            return this.prev - this.curr;
        }
    }
    // the cordinates saved within a class
    private class cord{
        private double x,y;
        public cord(double X, double Y){
            this.x = X;
            this.y = Y;
        }

        public cord setX(double x) {
            this.x = x;
            return this;
        }

        public cord setY(double y) {
            this.y = y;
            return this;
        }

        public double X() {
            return x;
        }

        public double Y() {
            return y;
        }
    }
}
