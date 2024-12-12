package com.pip.sensorskwbot;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.Canvas;
import android.graphics.Color;
import android.graphics.Matrix;
import android.graphics.Paint;
import android.graphics.RectF;
import android.graphics.Typeface;
import android.hardware.Sensor;
import android.hardware.SensorEvent;
import android.hardware.SensorEventListener;
import android.hardware.SensorManager;
import android.os.Bundle;
import android.os.Handler;
import android.os.HandlerThread;
import android.os.PersistableBundle;
import android.os.SystemClock;
import android.util.Log;
import android.util.TypedValue;
import android.view.View;
import android.widget.ArrayAdapter;
import android.widget.TextView;
import android.widget.Toast;

import com.pip.sensorskwbot.comminication.usb.ControlLines;
import com.pip.sensorskwbot.comminication.usb.SerialTimers;
import com.pip.sensorskwbot.comminication.usb.USB;
import com.pip.sensorskwbot.comminication.usb.UsbDeviceIdentities;
import com.pip.sensorskwbot.comminication.usb.UsbMessages;
import com.pip.sensorskwbot.customview.OverlayView;
import com.pip.sensorskwbot.env.BorderedText;
import com.pip.sensorskwbot.env.ImageUtils;
import com.pip.sensorskwbot.filters.INSInterval;
import com.pip.sensorskwbot.tracking.MultiBoxTracker;
import com.pip.sensorskwbot.utils.CameraUtils;
import com.pip.sensorskwbot.utils.Enums;
import com.pip.sensorskwbot.utils.MovingAverage;


import androidx.camera.core.CameraSelector;
import androidx.camera.core.ImageProxy;
import com.pip.sensorskwbot.databinding.ActivityMainBinding;
import com.pip.sensorskwbot.utils.pTimber;

import org.java_websocket.client.WebSocketClient;
import org.java_websocket.handshake.ServerHandshake;

import java.io.File;
import java.io.FilenameFilter;
import java.io.IOException;
import java.net.URI;
import java.util.LinkedList;
import java.util.List;
import java.util.Set;
import java.util.Timer;
import java.util.TimerTask;

import static com.pip.sensorskwbot.filters.INSInterval.MAGNETO_MAGNETIC_REFS_MAX;
import static com.pip.sensorskwbot.filters.INSInterval.MAGNETO_MAGNETIC_REFS_MIN;

public class MainActivity extends CameraActivity {

    ActivityMainBinding binding;
    //Inertia navigation Sensors

    private SensorManager sensorManager;
    private Sensor Accelerometer;
    private Sensor Magnetometer;
    private Sensor Gyroscope;
    private SensorEventListener AccelerometerListener;
    private SensorEventListener GyroscopeListener;
    private SensorEventListener MagnetometerListener;
    private TextView messageView;

    static Context context;

    private Handler handler;

    private HandlerThread handlerThread;

    private INSInterval Fmagneto;

    private SerialTimers MagnetoTimer;


    public static float MINIMUM_CONFIDENCE_TF_OD_API = 0.5f;
    private String classType = "person";
    private long lastProcessingTimeMs = -1;
    private long frameNum = 0;

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
    private Bitmap cropCopyBitmap;

    private static final float TEXT_SIZE_DIP = 10;

    private USB lowLevelCom;

    private static UsbDeviceIdentities ControllersId;

    private UsbMessages usbMessages = new UsbMessages() {
        @Override
        public void onStatus(String message) {
            try{
                //webSocketClient.send(message);
            }
            catch (Exception e){
               //Toast.makeText(MainActivity.this,e.getMessage(),Toast.LENGTH_LONG).show();
            }
        }

        @Override
        public void onError(String message) {
            try{
                //webSocketClient.send(message);
            }
            catch (Exception e){
                //Toast.makeText(MainActivity.this,e.getMessage(),Toast.LENGTH_LONG).show();
            }
            //Toast.makeText(MainActivity.this,"Error : "+message,Toast.LENGTH_LONG).show();
        }

        @Override
        public void onWarning(String message) {
            try{
                //webSocketClient.send(message);
            }
            catch (Exception e){
                //Toast.makeText(MainActivity.this,e.getMessage(),Toast.LENGTH_LONG).show();
            }
            //Toast.makeText(MainActivity.this,"Warning : "+message,Toast.LENGTH_LONG).show();
        }
    };

    private WebSocketClient webSocketClient;

    public static Context getContext() {
        return context;
    }

    private boolean SocketConnected = false;
    @Override
    public void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        binding = ActivityMainBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());
        messageView = findViewById(R.id.display_data);
        View xc = getLayoutInflater().inflate(R.layout.fragment_camera, binding.getRoot());
        context = getApplicationContext();
        connectWebSocket();

        if(initSensors()){
            initListeners();
            AssignSensors();
            showMessage("Innertia sensors are ready to run");
        }
        else {
            showMessage("Sensors not working!");
        }

        addCamera(xc);
        setAnalyserResolution(Enums.Preview.HD.getValue());
        connectRobot();



    }
    private void connectRobot(){


        ControllersId = USB.scanDevice(this);
        if(ControllersId!=null && webSocketClient.isOpen()){
            ControllersId.baudRate = 115200;
            lowLevelCom = new USB(this,ControllersId,usbMessages);
            usbMessages.onStatus("Connected to the Low Level Robot success!!");
            lowLevelCom.connect(this);
            Timer theTimer = new Timer();

            TimerTask task = new TimerTask() {
                @Override
                public void run() {
                    runOnUiThread(new Runnable() {
                        @Override
                        public void run() {
                            lowLevelCom.read();
                        }
                    });
                }
            };
            //theTimer.scheduleAtFixedRate(task,500,200);
        }
        else {
            usbMessages.onError("No Robot Found");
        }
    }
    private void updateCropImageInfo() {
        //    Timber.i("%s x %s",getPreviewSize().getWidth(), getPreviewSize().getHeight());
        //    Timber.i("%s x %s",getMaxAnalyseImageSize().getWidth(),
        //     getMaxAnalyseImageSize().getHeight());
        frameToCropTransform = null;

        sensorOrientation = 90 - ImageUtils.getScreenOrientation(this);

        final float textSizePx =
                TypedValue.applyDimension(
                        TypedValue.COMPLEX_UNIT_DIP, TEXT_SIZE_DIP, getResources().getDisplayMetrics());
        BorderedText borderedText = new BorderedText(textSizePx);
        borderedText.setTypeface(Typeface.MONOSPACE);

        tracker = new MultiBoxTracker(this);
        tracker.setDynamicSpeed(preferencesManager.getDynamicSpeed());

        pTimber.i("Camera orientation relative to screen canvas: "+sensorOrientation);

        recreateNetwork(getModel(), getDevice(), getNumThreads());
        if (detector == null) {
            pTimber.e("No network on preview!");
            return;
        }

        binding.trackingOverlay.addCallback(new OverlayView.DrawCallback() {
            @Override
            public void drawCallback(Canvas canvas) {
                tracker.draw(canvas);
                //tracker.drawDebug(canvas);
            }
        });
        tracker.setFrameConfiguration(
                getMaxAnalyseImageSize().getWidth(),
                getMaxAnalyseImageSize().getHeight(),
                sensorOrientation);
    }
    private void recreateNetwork(Model model, Network.Device device, int numThreads) {
        resetFpsUi();
        if (model == null) return;
        tracker.clearTrackedObjects();
        if (detector != null) {
            pTimber.d("Closing detector.");
            detector.close();
            detector = null;
        }
        try {
            pTimber.d("Creating detector (model=%s, device=%s, numThreads=%d)");//, model, device, numThreads);
            detector = Detector.create(MainActivity.this, model, device, numThreads);
            if(detector == null){
                Log.d("&&&&","We are here the dectort is null");
            }
            assert detector != null;
            croppedBitmap =
                    Bitmap.createBitmap(
                            detector.getImageSizeX(), detector.getImageSizeY(), Bitmap.Config.ARGB_8888);

            if(croppedBitmap!=null){
                Log.d("&&&&","We are here the dectort is not null");
            }

            frameToCropTransform =
                    ImageUtils.getTransformationMatrix(
                            getMaxAnalyseImageSize().getWidth(),
                            getMaxAnalyseImageSize().getHeight(),
                            croppedBitmap.getWidth(),
                            croppedBitmap.getHeight(),
                            sensorOrientation,
                            detector.getCropRect(),
                            detector.getMaintainAspect());

            cropToFrameTransform = new Matrix();
            frameToCropTransform.invert(cropToFrameTransform);

            MainActivity.this
                    .runOnUiThread(
                            () -> {
                                ArrayAdapter<String> adapter =
                                        new ArrayAdapter<>(
                                                getContext(),
                                                android.R.layout.simple_dropdown_item_1line,
                                                detector.getLabels());
                            });

        } catch (IllegalArgumentException | IOException e) {
            String msg = "Failed to create network.";
            pTimber.e(e, msg);
            MainActivity.this
                    .runOnUiThread(
                            () ->
                                    Toast.makeText(
                                                    MainActivity.this,
                                                    e.getMessage(),
                                                    Toast.LENGTH_LONG)
                                            .show());
        }
    }
    @Override
    protected void processControllerKeyData(String command) {

    }
    @Override
    protected void processUSBData(String data) {

    }
    @Override
    protected void processFrame(Bitmap bitmap, ImageProxy imageProxy) {
        if (tracker == null) updateCropImageInfo();

        ++frameNum;

        if (binding != null ) {

            // If network is busy, return.
            if (computingNetwork) {
                return;
            }

            computingNetwork = true;
            pTimber.i("Putting image " + frameNum + " for detection in bg thread.");

            runInBackground(
                    () -> {
                        if(croppedBitmap==null) return;
                        final Canvas canvas = new Canvas(croppedBitmap);
                        if (lensFacing == CameraSelector.LENS_FACING_FRONT) {
                            canvas.drawBitmap(
                                    CameraUtils.flipBitmapHorizontal(bitmap), frameToCropTransform, null);
                        } else {
                            canvas.drawBitmap(bitmap, frameToCropTransform, null);
                        }

                        if (detector != null) {

                            pTimber.d("Running detection on image %s", String.valueOf(frameNum));
                            final long startTime = SystemClock.elapsedRealtime();
                            final List<Detector.Recognition> results =
                                    detector.recognizeImage(croppedBitmap, classType);
                            lastProcessingTimeMs = SystemClock.elapsedRealtime() - startTime;



                            if (!results.isEmpty()) {
                                String theString = " Person : " + results.size()

                                        + " Confidendence : " + results.get(0).getConfidence();




                                Log.d("&&&&",theString);
                                if(results.get(0).getConfidence()>0.6){
                                    if(Magnetometer!=null){
                                        MagnetoTimer.message("forward<0.9>%");
                                        if(MagnetoTimer.vlid()){
                                            Log.d("$%$%$%","HERE!!");
                                            if(lowLevelCom!=null) lowLevelCom.send(MagnetoTimer.message());
                                            else webSocketClient.send("No Usb Found!");
                                            Log.d("$$%%$$%%",MagnetoTimer.message());
                                        }
                                    }
                                }
                                else {
                                    if(Magnetometer!=null) {
                                        MagnetoTimer.message("stop%");
                                        if (MagnetoTimer.vlid()) {
                                            Log.d("$%$%$%", "HERE!!");
                                            if (lowLevelCom != null)
                                                lowLevelCom.send(MagnetoTimer.message());
                                            else webSocketClient.send("No Usb Found!");
                                            Log.d("$$%%$$%%", MagnetoTimer.message());
                                        }
                                    }
                                }
                                pTimber.i(
                                        "Object: "
                                                + results.get(0).getLocation().centerX()
                                                + ", "
                                                + results.get(0).getLocation().centerY()
                                                + ", "
                                                + results.get(0).getLocation().height()
                                                + ", "
                                                + results.get(0).getLocation().width());
                            }

                            cropCopyBitmap = Bitmap.createBitmap(croppedBitmap);
                            final Canvas canvas1 = new Canvas(cropCopyBitmap);
                            final Paint paint = new Paint();
                            paint.setColor(Color.RED);
                            paint.setStyle(Paint.Style.STROKE);
                            paint.setStrokeWidth(2.0f);

                            final List<Detector.Recognition> mappedRecognitions = new LinkedList<>();

                            for (final Detector.Recognition result : results) {
                                final RectF location = result.getLocation();
                                if (location != null && result.getConfidence() >= MINIMUM_CONFIDENCE_TF_OD_API) {
                                    canvas1.drawRect(location, paint);
                                    cropToFrameTransform.mapRect(location);
                                    result.setLocation(location);
                                    mappedRecognitions.add(result);
                                }
                            }

                            tracker.trackResults(mappedRecognitions, frameNum);
                            binding.trackingOverlay.postInvalidate();
                        }

                        computingNetwork = false;
                    });
            if (lastProcessingTimeMs > 0) {

            }
        }
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

        Fmagneto = new INSInterval(0.8);

        MagnetoTimer = new SerialTimers();

        MagnetoTimer.update();

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
                if(webSocketClient.isOpen()){
                    if(Fmagneto.Sensed(event.values[0],event.values[1],event.values[2])){
                        //webSocketClient.send(disply);
                        Log.d("$$$$$",disply);
                    }
                    if((event.values[2]< MAGNETO_MAGNETIC_REFS_MIN)){
                        MagnetoTimer.message("back<0.9>%");
                        if(MagnetoTimer.vlid()){
                            Log.d("$%$%$%","HERE!!");
                            if(lowLevelCom!=null) lowLevelCom.send(MagnetoTimer.message());
                            else webSocketClient.send("No Usb Found!");
                            Log.d("$$%%$$%%",MagnetoTimer.message());
                        }

                    }
                    else {
                        MagnetoTimer.message("stop%");
                        if(MagnetoTimer.vlid()){
                            if(lowLevelCom!=null) lowLevelCom.send(MagnetoTimer.message());
                            else webSocketClient.send("No Usb Found!");
                            Log.d("$$%%$$%%",MagnetoTimer.message());
                        }

                    }
                }
            }

            @Override
            public void onAccuracyChanged(Sensor sensor, int accuracy) {

            }
        };
    }
    private void showMessage(String mess){
        Toast.makeText(this,mess, Toast.LENGTH_LONG).show();
    }

    private void resetFpsUi() {
        processedFrames = 0;
        movingAvgProcessingTimeMs = new MovingAverage(movingAvgSize);
        MainActivity.this.runOnUiThread(new Runnable() {
            @Override
            public void run() {

            }
        });
    }

    protected Model getModel() {
        return model;
    }

    @Override
    protected void setModel(Model model) {
        if (this.model != model) {
            pTimber.d("Updating  model: %s", model.name);
            this.model = model;
            preferencesManager.setObjectNavModel(model.name);
            onInferenceConfigurationChanged();
        }
    }
    protected Network.Device getDevice() {
        return device;
    }
    private void setDevice(Network.Device device) {
        if (this.device != device) {
            pTimber.d("Updating  device: %s", device.name());
            this.device = device;
            final boolean threadsEnabled = device == Network.Device.CPU;
            preferencesManager.setDevice(device.ordinal());
            onInferenceConfigurationChanged();
        }
    }
    protected int getNumThreads() {
        return numThreads;
    }
    private void setNumThreads(int numThreads) {
        if (this.numThreads != numThreads) {
            pTimber.d("Updating  numThreads: %s", String.valueOf(numThreads));
            this.numThreads = numThreads;
            preferencesManager.setNumThreads(numThreads);
            onInferenceConfigurationChanged();
        }
    }
    protected void onInferenceConfigurationChanged() {
        computingNetwork = false;
        if (croppedBitmap == null) {
            // Defer creation until we're getting camera frames.
            return;
        }
        final Network.Device device = getDevice();
        final Model model = getModel();
        final int numThreads = getNumThreads();
        runInBackground(new Runnable() {
            @Override
            public void run() {
                recreateNetwork(model, device, numThreads);
            }
        });
    }
    protected synchronized void runInBackground(final Runnable r) {
        if (handler != null) {
            handler.post(r);
        }
    }
    @Override
    public synchronized void onResume() {
        croppedBitmap = null;
        tracker = null;
        handlerThread = new HandlerThread("inference");
        handlerThread.start();
        handler = new Handler(handlerThread.getLooper());
        super.onResume();
    }
    @Override
    public synchronized void onPause() {
        handlerThread.quitSafely();
        try {
            handlerThread.join();
            handlerThread = null;
            handler = null;
        } catch (final InterruptedException e) {
            e.printStackTrace();
        }
        super.onPause();
    }
    private void setNetworkEnabled(boolean b) {
        resetFpsUi();
        if(!b){
            handler.postDelayed(new Runnable() {
                @Override
                public void run() {
                    //vehicle.setControl(0, 0);
                }
            },Math.max(lastProcessingTimeMs, 50));
        }
    }
    @Override
    public void onRemoveModel(String model) {
    }
    @Override
    public void onConnectionEstablished(String ipAddress) {
    }
    @Override
    public void onServerListChange(Set<String> servers) {
    }
    private void connectWebSocket() {
        URI uri;
        Toast.makeText(this," Connecting to : " + ServerUrl() ,Toast.LENGTH_LONG).show();
        try {
            uri = new URI(ServerUrl() ); // Replace <PC_IP> with the PC's IP address
            //Toast.makeText(this,"Conneting to : "+ uri.toASCIIString(),Toast.LENGTH_LONG).show();
        } catch (Exception e) {
            Toast.makeText(this,"Failed : "+ e.getMessage(),Toast.LENGTH_LONG).show();
            e.printStackTrace();
            Log.d("SXSXSSSS",e.getMessage());
            return;
        }

        webSocketClient = new WebSocketClient(uri) {
            @Override
            public void onOpen(ServerHandshake handshakedata) {
                runOnUiThread(() -> {
                    SocketConnected = true;
                    Log.d("SXSXSSSS","Connected to server");
                });
            }

            @Override
            public void onMessage(String message) {
                runOnUiThread(() -> {
                    System.out.println("Server says: " + message);
                    Log.d("SXSXSSSS","Server says: " + message);
                });
            }

            @Override
            public void onClose(int code, String reason, boolean remote) {
                runOnUiThread(() -> {
                    Log.d("SXSXSSSS","Connection closed");
                    SocketConnected = false;
                    System.out.println("Connection closed");
                });
            }

            @Override
            public void onError(Exception ex) {
                runOnUiThread(() -> {
                    System.out.println("Error: " + ex.getMessage());
                    Log.d("SXSXSSSS",ex.getMessage());
                });
            }
        };

        webSocketClient.connect();
    }
    private String ServerUrl(){
        return "ws://"+this.getString(R.string.websocket_server) + ":" + this.getString(R.string.websocket_port);
    }
    private String ServerUrlH(){
        return "http://"+ getString(R.string.websocket_server) + ":" +  getString(R.string.websocket_port);
    }
}
