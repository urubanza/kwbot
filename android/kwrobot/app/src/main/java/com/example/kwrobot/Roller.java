package com.example.kwrobot;

import android.content.Context;
import android.content.res.TypedArray;
import android.graphics.Bitmap;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Paint;
import android.graphics.Rect;
import android.graphics.drawable.Drawable;
import android.text.TextPaint;
import android.util.AttributeSet;
import android.util.Log;
import android.view.MotionEvent;
import android.view.View;
import android.graphics.Path;

import java.util.Random;

import kwbotControler.DirectionRoller;

/**
 * TODO: document your custom view class.
 */
public class Roller extends View {
    private String mExampleString; // TODO: use a default from R.string...
    private int mExampleColor = Color.RED; // TODO: use a default from R.color...
    private float mExampleDimension = 0; // TODO: use a default from R.dimen...
    private Drawable mExampleDrawable;

    private Paint pLine, pBg;
    private Path touchPath;
    private Bitmap b;

    private TextPaint mTextPaint;
    private float mTextWidth;
    private float mTextHeight;

    private Canvas c;

    public Roller(Context context) {
        super(context);
        init(null, 0);
    }

    public Roller(Context context, AttributeSet attrs) {
        super(context, attrs);
        pBg = new Paint();
        pBg.setColor(Color.WHITE);

        pLine = new Paint();
        pLine.setColor(Color.GREEN);
        pLine.setAntiAlias(true);
        pLine.setStyle(Paint.Style.STROKE);
        pLine.setStrokeWidth(12);

        touchPath = new Path();
        init(attrs, 0);
    }

    public Roller(Context context, AttributeSet attrs, int defStyle) {
        super(context, attrs, defStyle);
        init(attrs, defStyle);
    }

    private void init(AttributeSet attrs, int defStyle) {

    }

    private void invalidateTextPaintAndMeasurements() {
        mTextPaint.setTextSize(mExampleDimension);
        mTextPaint.setColor(mExampleColor);
        mTextWidth = mTextPaint.measureText(mExampleString);

        Paint.FontMetrics fontMetrics = mTextPaint.getFontMetrics();
        mTextHeight = fontMetrics.bottom;
    }

    @Override
    protected void onDraw(Canvas canvas) {
        canvas.drawBitmap(b, 0, 0, pBg);
        canvas.drawPath(touchPath, pLine);
//        int colors[] = {Color.RED,Color.argb(50,80,60,67),Color.BLACK,Color.GREEN,Color.WHITE};
//        this.c = canvas;
//        DirectionRoller roll = new DirectionRoller();
//        Paint paint1 = new Paint();
//        Paint paint2 = new Paint();
//        paint1.setColor(colors[1]);
//        paint2.setColor(colors[2]);
//
//
//        roll.size(100,40).position(200,200).put(this.c,paint1,paint2);
    }

    @Override
    protected void onSizeChanged(int w, int h, int oldw, int oldh) {
        super.onSizeChanged(w, h, oldw, oldh);
        b = Bitmap.createBitmap(w, h, Bitmap.Config.ARGB_8888);
        c = new Canvas(b);
    }





    /**
     * Gets the example string attribute value.
     *
     * @return The example string attribute value.
     */
    public String getExampleString() {
        return mExampleString;
    }

    /**
     * Sets the view's example string attribute value. In the example view, this string
     * is the text to draw.
     *
     * @param exampleString The example string attribute value to use.
     */
    public void setExampleString(String exampleString) {
        mExampleString = exampleString;
        invalidateTextPaintAndMeasurements();
    }

    /**
     * Gets the example color attribute value.
     *
     * @return The example color attribute value.
     */
    public int getExampleColor() {
        return mExampleColor;
    }

    /**
     * Sets the view's example color attribute value. In the example view, this color
     * is the font color.
     *
     * @param exampleColor The example color attribute value to use.
     */
    public void setExampleColor(int exampleColor) {
        mExampleColor = exampleColor;
        invalidateTextPaintAndMeasurements();
    }

    /**
     * Gets the example dimension attribute value.
     *
     * @return The example dimension attribute value.
     */
    public float getExampleDimension() {
        return mExampleDimension;
    }

    /**
     * Sets the view's example dimension attribute value. In the example view, this dimension
     * is the font size.
     *
     * @param exampleDimension The example dimension attribute value to use.
     */
    public void setExampleDimension(float exampleDimension) {
        mExampleDimension = exampleDimension;
        invalidateTextPaintAndMeasurements();
    }

    /**
     * Gets the example drawable attribute value.
     *
     * @return The example drawable attribute value.
     */
    public Drawable getExampleDrawable() {
        return mExampleDrawable;
    }

    /**
     * Sets the view's example drawable attribute value. In the example view, this drawable is
     * drawn above the text.
     *
     * @param exampleDrawable The example drawable attribute value to use.
     */
    public void setExampleDrawable(Drawable exampleDrawable) {
        mExampleDrawable = exampleDrawable;
    }

    @Override
    public boolean onTouchEvent(MotionEvent event) {
        float x = event.getX();
        float y = event.getY();


        switch (event.getAction()) {
            case MotionEvent.ACTION_DOWN:
                touchPath.moveTo(x, y);
                break;
            case MotionEvent.ACTION_MOVE:
                touchPath.lineTo(x,y);
                break;
            case MotionEvent.ACTION_UP:
                touchPath.lineTo(x, y);
                c.drawPath(touchPath, pLine);
                touchPath = new Path();
                break;
            default:
                return false;
        }
        invalidate();
        return false;
    }
}
