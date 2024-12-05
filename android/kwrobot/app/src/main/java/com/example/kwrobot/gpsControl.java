package com.example.kwrobot;

import androidx.appcompat.app.AppCompatActivity;
import io.socket.client.IO;
import io.socket.client.Socket;

import android.content.Context;
import android.os.Bundle;
import android.widget.TextView;
import android.hardware.Sensor;
import android.hardware.SensorEvent;
import android.hardware.SensorEventListener;
import android.hardware.SensorManager;
import android.widget.Toast;

import java.net.URISyntaxException;


public class gpsControl extends AppCompatActivity   implements SensorEventListener{

    private TextView showValues;

    // the position according to the GPS values given
    private double xPos = 0;
    private double yPos = 0;
    private double zPos = 0;

    private Socket AgentSocket;

    private SensorManager mSensorManager;
    private Sensor mSensor;
    public Config config = new Config();

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_gps_control);
        showValues = findViewById(R.id.showValues);

        mSensorManager = (SensorManager) getSystemService(Context.SENSOR_SERVICE);
        mSensor = mSensorManager.getDefaultSensor(Sensor.TYPE_LINEAR_ACCELERATION);

        mSensorManager.registerListener(this, mSensor , SensorManager.SENSOR_DELAY_NORMAL);

        try {
            AgentSocket = IO.socket(config.MAIN_WEB_SOCKET_SERVER).connect();
        } catch (URISyntaxException e){
            Toast.makeText(this,"invalid url",Toast.LENGTH_LONG).show();
        }

    }

    @Override
    public void onSensorChanged(SensorEvent sensorEvent) {
        if(sensorEvent.sensor.getType()==Sensor.TYPE_LINEAR_ACCELERATION){
            getAcceleration(sensorEvent);
        }
    }

    private void getAcceleration(SensorEvent sensorEvent) {
        float xVal = sensorEvent.values[0];
        float yVal = sensorEvent.values[1];
        float zVal = sensorEvent.values[2];
        showValues.setText("x:"+xVal+" y:"+yVal+" z:"+zVal);
    }

    @Override
    public void onAccuracyChanged(Sensor sensor, int i) {

    }
}
