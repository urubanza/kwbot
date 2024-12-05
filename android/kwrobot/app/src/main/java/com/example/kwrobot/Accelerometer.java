package com.example.kwrobot;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Context;
import android.hardware.Sensor;
import android.hardware.SensorEvent;
import android.hardware.SensorEventListener;
import android.hardware.SensorManager;
import android.os.Bundle;
import android.util.Log;
import android.widget.TextView;
import android.widget.Toast;

import org.json.JSONException;
import org.json.JSONObject;

public class Accelerometer extends AppCompatActivity  implements SensorEventListener {
    private TextView showValues;

    private SensorManager senSensorManager;
    private Sensor senAccelerometer;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_accelerometer);
        showValues = findViewById(R.id.showValues);

        senSensorManager = (SensorManager) getSystemService(Context.SENSOR_SERVICE);
        senAccelerometer = senSensorManager.getDefaultSensor(Sensor.TYPE_ACCELEROMETER);
        senSensorManager.registerListener(this, senAccelerometer , SensorManager.SENSOR_DELAY_NORMAL);
    }

    private void getAccelerometer(SensorEvent event){
        // Movement
        float xVal = event.values[0];
        float yVal = event.values[1];
        float zVal = event.values[2];

        double angle,speed;

        showValues.setText("x:"+xVal+", yVal:"+yVal+", zVal"+zVal);
        String turn = "void";
        if(xVal>2.1){
            turn = "bottom";
        }
        else if(xVal<-2.1){
            turn = "up";
        }
        else if(yVal>2.1){
            turn = "right";
        }
        else if(yVal<-2.1){
            turn = "left";
        }
        JSONObject Sends = new JSONObject();
        try {
            Sends.put("xval",xVal);
            Sends.put("yval",yVal);
            Sends.put("zval",zVal);
            Sends.put("direction",turn);
        } catch (JSONException e){
            Toast.makeText(this,"Failed To sends",Toast.LENGTH_LONG).show();
        }


    }

    @Override
    public void onSensorChanged(SensorEvent event) {
        if (event.sensor.getType() == Sensor.TYPE_ACCELEROMETER) {
            getAccelerometer(event);
        }
    }

    @Override
    public void onAccuracyChanged(Sensor sensor, int i) {

    }

    protected void onPause() {
        super.onPause();
        senSensorManager.unregisterListener(this);
    }
    protected void onResume() {
        super.onResume();
        senSensorManager.registerListener(this, senAccelerometer, SensorManager.SENSOR_DELAY_NORMAL);
    }
}
