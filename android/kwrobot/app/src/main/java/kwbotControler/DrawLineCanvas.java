package kwbotControler;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.Path;
import android.util.AttributeSet;
import android.util.DisplayMetrics;
import android.view.Display;
import android.view.MotionEvent;
import android.view.View;
import android.view.WindowManager;

public class DrawLineCanvas extends View implements speedLister, angleLister {

    private Canvas c;

    private Paint pLine, pBg;
    private Path touchPath;

    private Bitmap b;

    private DirectionRoller theRoller;
    private Rotator rotator;
    private Speed speed;

    private int height,width;

    private void getScreenResolution(Context context){
        WindowManager wm = (WindowManager) context.getSystemService(Context.WINDOW_SERVICE);
        Display display = wm.getDefaultDisplay();
        DisplayMetrics metrics = new DisplayMetrics();
        display.getMetrics(metrics);
        this.width = metrics.widthPixels;
        this.height = metrics.heightPixels;
    }
    public DrawLineCanvas(Context context) {
        super(context);
        getScreenResolution(context);
    }
    public DrawLineCanvas(Context context, AttributeSet attrs) {
        super(context, attrs);
        getScreenResolution(context);
        pBg = new Paint();
        pBg.setColor(Color.WHITE);
        pLine = new Paint();
        pLine.setColor(Color.GREEN);
        pLine.setAntiAlias(true);
        pLine.setStyle(Paint.Style.STROKE);
        pLine.setStrokeWidth(12);
        touchPath = new Path();
        theRoller = new DirectionRoller();
        Paint paint1 = new Paint();
        Paint paint2 = new Paint();
        paint1.setColor(Color.argb(50,80,60,67));
        paint2.setColor(Color.BLACK);
        theRoller.Paints(paint1,paint2)
                .setScreenArea(height,width)
                .size(100,40)
                .position(200,this.height-200)
                .setMainScreenColor(Color.WHITE);
        speed = new Speed(height,width).Paints(paint1,paint2);
        rotator = new Rotator(c,400,this.height-550);

        rotator.circles(60,100)
                .setMainScreenColor(Color.WHITE);

    }
    public DrawLineCanvas(Context context, AttributeSet attrs, int defStyleAttr) {
        super(context, attrs, defStyleAttr);
        getScreenResolution(context);
    }
    @Override
    protected void onSizeChanged(int w, int h, int oldw, int oldh) {
        super.onSizeChanged(w, h, oldw, oldh);
        b = Bitmap.createBitmap(w, h, Bitmap.Config.ARGB_8888);
        c = new Canvas(b);
    }
    @Override
    public boolean onTouchEvent(MotionEvent event) {
        float touchX = event.getX();
        float touchY = event.getY();
        switch (event.getAction()) {
            case MotionEvent.ACTION_DOWN:
                //touchPath.moveTo(touchX, touchY);
                speed.change(touchX,touchY).put(c);
                ShowSpeed(String.valueOf(speed.getAmounts()),c);
                ShowAngleD(String.valueOf(theRoller.getAngleDeg()),c);
                ShowAngleR(String.valueOf(theRoller.getAngle()),c);
                break;
            case MotionEvent.ACTION_MOVE:
                //touchPath.lineTo(touchX, touchY);
                theRoller.remove(c).move(c,touchX,touchY);
                speed.change(touchX,touchY).put(c);
                ShowSpeed(String.valueOf(speed.getAmounts()),c);
                ShowAngleD(String.valueOf(theRoller.getAngleDeg()),c);
                ShowAngleR(String.valueOf(theRoller.getAngle()),c);
                break;
            case MotionEvent.ACTION_UP:
                //touchPath.lineTo(touchX, touchY);
                //c.drawPath(touchPath, pLine);
                theRoller.remove(c).move(c,touchX,touchY);
                speed.change(touchX, touchY).put(c).inRange();
                ShowSpeed(String.valueOf(speed.getAmounts()),c);
                ShowAngleD(String.valueOf(theRoller.getAngleDeg()),c);
                ShowAngleR(String.valueOf(theRoller.getAngle()),c);
                touchPath = new Path();
                break;
            default:
                return false;
        }
        invalidate();
        return true;
    }
    @Override
    protected void onDraw(Canvas canvas) {
        super.onDraw(canvas);
        theRoller.put(canvas);
        speed.put(canvas);
        rotator.put(canvas);
        ShowSpeed(String.valueOf(speed.getAmounts()),canvas);
        ShowAngleD(String.valueOf(theRoller.getAngleDeg()),canvas);
        ShowAngleR(String.valueOf(theRoller.getAngle()),canvas);
        canvas.drawBitmap(b, 0, 0, pBg);
        canvas.drawPath(touchPath, pLine);
    }
    public DrawLineCanvas ShowSpeed(String speed,Canvas canvas){
        Paint p = new Paint();
        p.setColor(Color.BLACK);
        canvas.drawText("Speed: "+speed,this.width-(this.width/2),this.height-(this.height/2),p);
        return this;
    }
    public DrawLineCanvas ShowAngleD(String Angle,Canvas canvas){
        Paint p = new Paint();
        p.setColor(Color.BLACK);
        canvas.drawText("Angle(Degrees): "+Angle,this.width-(this.width/2),this.height-(this.height/2)+100,p);
        return this;
    }
    public DrawLineCanvas ShowAngleR(String Angle,Canvas canvas){
        Paint p = new Paint();
        p.setColor(Color.BLACK);
        canvas.drawText("Angle(Radians): "+Angle,this.width-(this.width/2),this.height-(this.height/2)+200,p);
        return this;
    }

    public double getAngle(){
        return this.theRoller.getAngle();
    }
    public double getSpeed(){
        return this.speed.getAmounts();
    }

    public void changes(speedAndDir s){

    }

    @Override
    public void onAngleChanged(DirectionRoller d) {
        d.onAngleChanged(theRoller);
    }

    @Override
    public void onStop(DirectionRoller d) {
        d.onStop(theRoller);
    }

    @Override
    public void onSpeedChanged(Speed s) {
        s.onSpeedChanged(speed);
    }
}
