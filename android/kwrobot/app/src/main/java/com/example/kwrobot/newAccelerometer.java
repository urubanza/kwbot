package com.example.kwrobot;

import androidx.appcompat.app.AppCompatActivity;
import io.socket.client.IO;
import io.socket.client.Socket;
import kwbotControler.gauge.GaugeAcceleration;
import kwbotControler.gauge.GaugeRotation;
import kwbotControler.plot.DynamicPlot;
import kwbotControler.plot.PlotColor;
import kwbotControler.sensor.AccelerationSensor;
import kwbotControler.sensor.GravitySensor;
import kwbotControler.sensor.GyroscopeSensor;
import kwbotControler.sensor.LinearAccelerationSensor;
import kwbotControler.sensor.MagneticSensor;
import kwbotControler.sensor.observer.AccelerationSensorObserver;
import kwbotControler.sensor.observer.LinearAccelerationSensorObserver;

import android.graphics.Color;
import android.hardware.Sensor;
import android.hardware.SensorEvent;
import android.hardware.SensorManager;
import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.MotionEvent;
import android.view.View;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.androidplot.xy.XYPlot;

import org.json.JSONException;
import org.json.JSONObject;

import java.net.URISyntaxException;
import java.text.DecimalFormat;
import java.util.List;

public class newAccelerometer extends AppCompatActivity implements Runnable,
        View.OnTouchListener, LinearAccelerationSensorObserver,
        AccelerationSensorObserver {

    private Socket AgentSocket;
    public Config config = new Config();
    private static final String tag = newAccelerometer.class
            .getSimpleName();
    // Decimal formats for the UI outputs
    private DecimalFormat df;
    // Graph plot for the UI outputs
    private DynamicPlot dynamicPlot;
    // Outputs for the acceleration and LPFs
    private float[] acceleration = new float[3];
    private float[] linearAcceleration = new float[3];
    // Touch to zoom constants for the dynamicPlot
    private float distance = 0;
    private float zoom = 1.2f;
    // The Acceleration Gauge
    private GaugeRotation gaugeAccelerationTilt;
    // The LPF Gauge
    private GaugeRotation gaugeLinearAccelTilt;
    // The Acceleration Gauge
    private GaugeAcceleration gaugeAcceleration;
    // The LPF Gauge
    private GaugeAcceleration gaugeLinearAcceleration;
    // Icon to indicate logging is active
    private ImageView iconLogger;
    // Plot keys for the acceleration plot
    private int plotAccelXAxisKey = 0;
    private int plotAccelYAxisKey = 1;
    private int plotAccelZAxisKey = 2;
    // Plot keys for the LPF Wikipedia plot
    private int plotLinearAccelXAxisKey = 3;
    private int plotLinearAccelYAxisKey = 4;
    private int plotLinearAccelZAxisKey = 5;
    // Color keys for the acceleration plot
    private int plotAccelXAxisColor;
    private int plotAccelYAxisColor;
    private int plotAccelZAxisColor;
    // Color keys for the LPF Wikipedia plot
    private int plotLinearAccelXAxisColor;
    private int plotLinearAccelYAxisColor;
    private int plotLinearAccelZAxisColor;
    // Plot colors
    private PlotColor color;
    private AccelerationSensor accelerationSensor;
    private GravitySensor gravitySensor;
    private GyroscopeSensor gyroscopeSensor;
    private MagneticSensor magneticSensor;
    private LinearAccelerationSensor linearAccelerationSensor;
    // Acceleration plot titles
    private String plotAccelXAxisTitle = "AX";
    private String plotAccelYAxisTitle = "AY";
    private String plotAccelZAxisTitle = "AZ";
    // LPF Wikipedia plot titles
    private String plotLinearAccelXAxisTitle = "lAX";
    private String plotLinearAccelYAxisTitle = "lAY";
    private String plotLinearAccelZAxisTitle = "lAZ";
    // Output log
    private String log;
    // Acceleration UI outputs
    private TextView xAxis;
    private TextView yAxis;
    private TextView zAxis;
    private Handler handler;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_new_accelerometer);

        View view = findViewById(R.id.plot_layout);
        view.setOnTouchListener(this);
        SensorManager manager = (SensorManager) getSystemService(SENSOR_SERVICE);
        List<Sensor> sensorList = manager.getSensorList(Sensor.TYPE_ALL);


        try {
            AgentSocket = IO.socket(config.MAIN_WEB_SOCKET_SERVER).connect();
        } catch (URISyntaxException e){
            Toast.makeText(this,"invalid url",Toast.LENGTH_LONG).show();
        }


        TextView accelerationLable = (TextView) view
                .findViewById(R.id.label_acceleration_name_0);
        accelerationLable.setText("Acceleration");

        TextView lpfLable = (TextView) view
                .findViewById(R.id.label_acceleration_name_1);
        lpfLable.setText("Fused");

        // Create the graph plot
        XYPlot plot = (XYPlot) findViewById(R.id.plot_sensor);
        plot.setTitle("Acceleration");
        dynamicPlot = new DynamicPlot(plot);
        dynamicPlot.setMaxRange(11.2);
        dynamicPlot.setMinRange(-11.2);

        // Create the acceleration UI outputs
        xAxis = (TextView) findViewById(R.id.value_x_axis);
        yAxis = (TextView) findViewById(R.id.value_y_axis);
        zAxis = (TextView) findViewById(R.id.value_z_axis);


        // Format the UI outputs so they look nice
        df = new DecimalFormat("#.##");

        linearAccelerationSensor = new LinearAccelerationSensor();
        accelerationSensor = new AccelerationSensor(this);
        gravitySensor = new GravitySensor(this);
        gyroscopeSensor = new GyroscopeSensor(this);
        magneticSensor = new MagneticSensor(this);

        // Initialize the plots
        initColor();
        initPlot();
        initGauges();

        handler = new Handler();
    }

    @Override
    public void onPause() {
        super.onPause();

        accelerationSensor.removeAccelerationObserver(this);
        accelerationSensor.removeAccelerationObserver(linearAccelerationSensor);
        gravitySensor.removeGravityObserver(linearAccelerationSensor);
        gyroscopeSensor.removeGyroscopeObserver(linearAccelerationSensor);
        magneticSensor.removeMagneticObserver(linearAccelerationSensor);

        linearAccelerationSensor.removeLinearAccelerationObserver(this);

        handler.removeCallbacks(this);
    }

    @Override
    public void onResume() {
        super.onResume();
        handler.post(this);
        accelerationSensor.registerAccelerationObserver(this);
        accelerationSensor
                .registerAccelerationObserver(linearAccelerationSensor);
        gravitySensor.registerGravityObserver(linearAccelerationSensor);
        gyroscopeSensor.registerGyroscopeObserver(linearAccelerationSensor);
        magneticSensor.registerMagneticObserver(linearAccelerationSensor);

        linearAccelerationSensor.registerLinearAccelerationObserver(this);
    }

    @Override
    public boolean onTouch(View view, MotionEvent e) {
        // MotionEvent reports input details from the touch screen
        // and other input controls.
        float newDist = 0;

        switch (e.getAction()) {

            case MotionEvent.ACTION_MOVE:

                // pinch to zoom
                if (e.getPointerCount() == 2) {
                    if (distance == 0) {
                        distance = fingerDist(e);
                    }

                    newDist = fingerDist(e);

                    zoom *= distance / newDist;

                    dynamicPlot.setMaxRange(zoom * Math.log(zoom));
                    dynamicPlot.setMinRange(-zoom * Math.log(zoom));

                    distance = newDist;
                }
        }

        return false;
    }

    @Override
    public void run() {
        handler.postDelayed(this, 100);
        plotData();
    }

    @Override
    public void onAccelerationSensorChanged(float[] acceleration, long timeStamp) {
        // Get a local copy of the sensor values
        System.arraycopy(acceleration, 0, this.acceleration, 0,
                acceleration.length);
    }

    @Override
    public void onLinearAccelerationSensorChanged(float[] linearAcceleration, long timeStamp) {
        // Get a local copy of the sensor values
        System.arraycopy(linearAcceleration, 0, this.linearAcceleration, 0,
                linearAcceleration.length);
    }

    private void initColor() {
        color = new PlotColor(this);

        plotAccelXAxisColor = color.getDarkBlue();
        plotAccelYAxisColor = color.getDarkGreen();
        plotAccelZAxisColor = color.getDarkRed();

        plotLinearAccelXAxisColor = color.getMidBlue();
        plotLinearAccelYAxisColor = color.getMidGreen();
        plotLinearAccelZAxisColor = color.getMidRed();
    }

    private void getAccelerometer(SensorEvent event) {
        // Movement
        float xVal = event.values[0];
        float yVal = event.values[1];
        float zVal = event.values[2];

        double angle, speed;
        String turn = "void";
        if (xVal > 2.1) {
            turn = "bottom";
        } else if (xVal < -2.1) {
            turn = "up";
        } else if (yVal > 2.1) {
            turn = "right";
        } else if (yVal < -2.1) {
            turn = "left";
        }
        JSONObject Sends = new JSONObject();
        try {
            Sends.put("xval", xVal);
            Sends.put("yval", yVal);
            Sends.put("zval", zVal);
            Sends.put("direction", turn);
        } catch (JSONException e) {
            Toast.makeText(this, "Failed To sends", Toast.LENGTH_LONG).show();
        }


    }

    /**
     * Create the output graph line chart.
     */
    private void initPlot() {
        addPlot(plotAccelXAxisTitle, plotAccelXAxisKey, plotAccelXAxisColor);
        addPlot(plotAccelYAxisTitle, plotAccelYAxisKey, plotAccelYAxisColor);
        addPlot(plotAccelZAxisTitle, plotAccelZAxisKey, plotAccelZAxisColor);

        addPlot(plotLinearAccelXAxisTitle, plotLinearAccelXAxisKey,
                plotLinearAccelXAxisColor);
        addPlot(plotLinearAccelYAxisTitle, plotLinearAccelYAxisKey,
                plotLinearAccelYAxisColor);
        addPlot(plotLinearAccelZAxisTitle, plotLinearAccelZAxisKey,
                plotLinearAccelZAxisColor);
    }

    /**
     * Create the RMS Noise bar chart.
     */
    private void initGauges() {
        gaugeAccelerationTilt = (GaugeRotation) findViewById(R.id.gauge_rotation_0);
        gaugeLinearAccelTilt = (GaugeRotation) findViewById(R.id.gauge_rotation_1);

        gaugeAcceleration = (GaugeAcceleration) findViewById(R.id.gauge_acceleration_0);
        gaugeLinearAcceleration = (GaugeAcceleration) findViewById(R.id.gauge_acceleration_1);
    }

    /**
     * Add a plot to the graph.
     *
     * @param title The name of the plot.
     * @param key   The unique plot key
     * @param color The color of the plot
     */
    private void addPlot(String title, int key, int color) {
        dynamicPlot.addSeriesPlot(title, key, color);
    }

    /**
     * Get the distance between fingers for the touch to zoom.
     *
     * @param event
     * @return
     */
    private final float fingerDist(MotionEvent event) {
        float x = event.getX(0) - event.getX(1);
        float y = event.getY(0) - event.getY(1);
        return (float) Math.sqrt(x * x + y * y);
    }

    /**
     * Plot the output data in the UI.
     */
    private void plotData() {
        dynamicPlot.setData(acceleration[0], plotAccelXAxisKey);
        dynamicPlot.setData(acceleration[1], plotAccelYAxisKey);
        dynamicPlot.setData(acceleration[2], plotAccelZAxisKey);

        dynamicPlot.setData(linearAcceleration[0], plotLinearAccelXAxisKey);
        dynamicPlot.setData(linearAcceleration[1], plotLinearAccelYAxisKey);
        dynamicPlot.setData(linearAcceleration[2], plotLinearAccelZAxisKey);

        dynamicPlot.draw();

        // Update the view with the new acceleration data
        xAxis.setText(df.format(acceleration[0]));
        yAxis.setText(df.format(acceleration[1]));
        zAxis.setText(df.format(acceleration[2]));

        if(acceleration[0]<-3){
            forward();
        }
        else if(acceleration[0]>3){
            backward();
        }
        else if(acceleration[1]<-3){
            left();
        }
        else if(acceleration[1]>3){
            right();
        }
        else{
            stop();
        }

        gaugeAccelerationTilt.updateRotation(acceleration);
        gaugeLinearAccelTilt.updateRotation(linearAcceleration);

        gaugeAcceleration.updatePoint(acceleration[0], acceleration[1],
                Color.parseColor("#33b5e5"));

        gaugeLinearAcceleration.updatePoint(linearAcceleration[0],
                linearAcceleration[1], Color.parseColor("#33b5e5"));
    }


    private void forward() {
        AgentSocket.emit("accelerometer","for");
    }

    private void backward() {
        AgentSocket.emit("accelerometer","back");
    }

    private void left() {
        AgentSocket.emit("accelerometer","left");
    }

    private void right() {
        AgentSocket.emit("accelerometer","right");
    }
    private void stop() {
        AgentSocket.emit("accelerometer","stop");
    }
}
