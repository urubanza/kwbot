package com.pip.sensorskwbot;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.Matrix;
import android.hardware.Sensor;
import android.hardware.SensorEvent;
import android.hardware.SensorEventListener;
import android.hardware.SensorManager;
import android.os.Bundle;
import android.os.PersistableBundle;
import android.util.Log;
import android.view.View;
import android.widget.TextView;
import android.widget.Toast;

import com.pip.sensorskwbot.tracking.MultiBoxTracker;
import com.pip.sensorskwbot.utils.MovingAverage;


import androidx.camera.core.ImageProxy;
import com.pip.sensorskwbot.databinding.ActivityMainBinding;

public class MainActivity extends CameraActivity {

    ActivityMainBinding binding;
    //Innertia navigation Sensors
    private SensorManager sensorManager;
    private Sensor Accelerometer;
    private Sensor Magnetometer;
    private Sensor Gyroscope;
    private SensorEventListener AccelerometerListener;
    private SensorEventListener GyroscopeListener;
    private SensorEventListener MagnetometerListener;
    private TextView messageView;

    static Context context;


    public static float MINIMUM_CONFIDENCE_TF_OD_API = 0.5f;
    private String classType = "person";

    private int numThreads = -1;
    private boolean computingNetwork = false;
    private Bitmap croppedBitmap;

    private Model model;
    private Network.Device device = Network.Device.CPU;

    private long processedFrames = 0;
    private final int movingAvgSize = 100;
    private MultiBoxTracker tracker;

    private MovingAverage movingAvgProcessingTimeMs = new MovingAverage(movingAvgSize);
    private Detector detector;
    private Matrix frameToCropTransform;
    private Matrix cropToFrameTransform;
    private int sensorOrientation;
    private long lastProcessingTimeMs = -1;

    public static Context getContext() {
        return context;
    }

    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        binding = ActivityMainBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());
        messageView = findViewById(R.id.display_data);

        View xc = getLayoutInflater().inflate(R.layout.fragment_camera, binding.getRoot());

        addCamera(xc);

        context = getApplicationContext();

        if(initSensors()){
            initListeners();
            AssignSensors();
            showMessage("Innertia sensors are ready to run");
        }
        else {
            showMessage("Sensors not working!");
        }
    }

    @Override
    protected void processControllerKeyData(String command) {

    }
    @Override
    protected void processUSBData(String data) {

    }

    @Override
    protected void processFrame(Bitmap image, ImageProxy imageProxy) {

    }

    private boolean initSensors(){
        sensorManager = (SensorManager) getApplicationContext().getSystemService(Context.SENSOR_SERVICE);
        Accelerometer = sensorManager.getDefaultSensor(Sensor.TYPE_ACCELEROMETER);
        Magnetometer = sensorManager.getDefaultSensor(Sensor.TYPE_MAGNETIC_FIELD);
        Gyroscope = sensorManager.getDefaultSensor(Sensor.TYPE_GYROSCOPE);
        if (Accelerometer == null) {
            Log.e("Sensors", "Accelerometer is not available");
        }
        if (Magnetometer == null) {
            Log.e("Sensors", "Magnetometer is not available");
        }
        if (Gyroscope == null) {
            Log.e("Sensors", "Gyroscope is not available");
        }
        return (Accelerometer!=null)
                &&(Magnetometer!=null)
                &&(Gyroscope!=null)
                ;
    }
    private void AssignSensors(){
        sensorManager.registerListener(AccelerometerListener,Accelerometer,SensorManager.SENSOR_DELAY_UI);
        sensorManager.registerListener(MagnetometerListener,Magnetometer,SensorManager.SENSOR_DELAY_UI);
        sensorManager.registerListener(GyroscopeListener,Gyroscope,SensorManager.SENSOR_DELAY_UI);

    }
    private void initListeners(){
        AccelerometerListener = new SensorEventListener() {
            @Override
            public void onSensorChanged(SensorEvent sensorEvent) {
                runOnUiThread(new Runnable() {
                    @Override
                    public void run() {
                        //messageView.setText(String.valueOf(sensorEvent.values[0]));
                    }
                });
            }
            @Override
            public void onAccuracyChanged(Sensor sensor, int accuracy) {

            }
        };

        GyroscopeListener = new SensorEventListener() {
            @Override
            public void onSensorChanged(SensorEvent event) {

            }

            @Override
            public void onAccuracyChanged(Sensor sensor, int accuracy) {

            }
        };

        MagnetometerListener = new SensorEventListener() {
            @Override
            public void onSensorChanged(SensorEvent event) {
                String disply = " X : "+  event.values[0]
                        + " Y : " + event.values[1]
                        + " Z : " + event.values[2];
                messageView.setText(disply);
            }

            @Override
            public void onAccuracyChanged(Sensor sensor, int accuracy) {

            }
        };
    }
    private void showMessage(String mess){
        Toast.makeText(this,mess, Toast.LENGTH_LONG).show();
    }
}
