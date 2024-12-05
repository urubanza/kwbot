package com.example.kwrobot;

import androidx.appcompat.app.AppCompatActivity;
import io.socket.client.IO;
import io.socket.client.Socket;

import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.hardware.Sensor;
import android.hardware.SensorEvent;
import android.hardware.SensorEventListener;
import android.hardware.SensorManager;
import android.os.Build;
import android.os.Bundle;
import android.view.View;
import android.widget.Button;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.ScrollView;
import android.widget.Toast;
import org.json.JSONException;
import org.json.JSONObject;
import java.net.URISyntaxException;
import java.util.ArrayList;

public class ControlPanel extends AppCompatActivity  implements SensorEventListener {

    private Socket AgentSocket;
    private SensorManager senSensorManager;
    private Sensor senAccelerometer;
    private Boolean currentVoid = true;
    private Button savePathButton;

    // the position according to the GPS values given

    public Config config = new Config();
    static ListView list;

    static controlList allControl;
    String[] name,message;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_control_panel);

        senSensorManager = (SensorManager) getSystemService(Context.SENSOR_SERVICE);
        senAccelerometer = senSensorManager.getDefaultSensor(Sensor.TYPE_ACCELEROMETER);
        senSensorManager.registerListener(this, senAccelerometer , SensorManager.SENSOR_DELAY_NORMAL);

        try {
            AgentSocket = IO.socket(config.MAIN_WEB_SOCKET_SERVER).connect();
        } catch (URISyntaxException e){
            Toast.makeText(this,"invalid url",Toast.LENGTH_LONG).show();
        }

        list = findViewById(R.id.mylist);

        String[] controlNames = {"JOY STICK",
                                 "ACCELEROMETER",
                                 "FOLLOWER",
                                 "DRAWER"};
        String[] Description = {
                "a Joy stick in phone screen where the robot will move according to the control you give",
                "using the accelerometer of the phone the robot will move according to the phone rotation",
                "Control the robot by tracking your phone linear acceleration and movement you made",
                "Draw the path in your phone screen to give the robot instruction to move"
        };
        int[] controlImages = {
                R.drawable.ic_videogame_asset_black_24dp,
                R.drawable.ic_screen_rotation_black_24dp,
                R.drawable.ic_gps_fixed_black_24dp,
                R.drawable.ic_gesture_black_24dp
        };

        ArrayList AllControlls = new ArrayList<Controllers>();
        for(int i = 0;i<4;i++){
            Controllers controllers = new Controllers();
            controllers.setImage(controlImages[i]).setName(controlNames[i]).setDescription(Description[i]);
            controllers.setPosition(i);
            AllControlls.add(controllers);
        }
        allControl = new controlList(this, AllControlls);
        ScrollView con = findViewById(R.id.SelectAll);
        LinearLayout joystick = findViewById(R.id.JoyStick);
        LinearLayout lineDrawer = findViewById(R.id.lineDrawer);
        LinearLayout GPS = findViewById(R.id.GPS);
        LinearLayout Accel = findViewById(R.id.Accelerometer);
        allControl.setContainer(con);
//        allControl.setControls(joystick,lineDrawer,GPS,Accel);
        list.setAdapter(allControl);
        list.setOnItemClickListener((adapterView, view, i, l) -> {
            control(i);
        });
    }
    @Override
    public void onSensorChanged(SensorEvent event) {
        if (event.sensor.getType() == Sensor.TYPE_ACCELEROMETER) {
            getAccelerometer(event);
        }
    }
    @Override
    public void onAccuracyChanged(Sensor sensor, int accuracy) {

    }
    private void getAccelerometer(SensorEvent event){
        // Movement
        float xVal = event.values[0];
        float yVal = event.values[1];
        float zVal = event.values[2];

        String turn = "void";

        if(xVal>2.1){
            turn = "bottom";
        } else if(xVal<-2.1){
            turn = "up";
        } else if(yVal>2.1){
            turn = "right";
        } else if(yVal<-2.1){
            turn = "left";
        }

        JSONObject Sends = new JSONObject();
        try {
            Sends.put("xval",xVal);
            Sends.put("yval",yVal);
            Sends.put("zval",zVal);
            Sends.put("direction",turn);
            if(currentVoid)
                AgentSocket.emit("accelomets",Sends);
            if(turn.equals("void")){
                currentVoid = false;
            } else {
                currentVoid = true;
            }
        } catch (JSONException e){
            Toast.makeText(this,"Failed To sends",Toast.LENGTH_LONG).show();
        }


    }
    protected void onPause() {
        super.onPause();
        senSensorManager.unregisterListener(this);
    }
    protected void onResume() {
        super.onResume();
        senSensorManager.registerListener(this, senAccelerometer, SensorManager.SENSOR_DELAY_NORMAL);
    }
    @Override
    public void onRequestPermissionsResult(int requestCode,String permissions[], int[] grantResults){
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);
        boolean allSuccess = true;
        for (int i = 0; i< permissions.length;i++) {
            if (grantResults[i] == PackageManager.PERMISSION_DENIED) {
                allSuccess = false;
                boolean requestAgain = Build.VERSION.SDK_INT >= Build.VERSION_CODES.M && shouldShowRequestPermissionRationale(permissions[i]);
                if (requestAgain) {
                    Toast.makeText(this, "Permission denied", Toast.LENGTH_SHORT).show();
                } else {
                    Toast.makeText(this, "Go to settings and enable the permission", Toast.LENGTH_SHORT).show();
                }
            }
        }
        if(allSuccess)
            Toast.makeText(this, "permission garanted", Toast.LENGTH_SHORT).show();
    }
    public void control(int position){
        if(position>4) this.control(4);
        switch (position){
            case 0:{
                Intent intent = new Intent(this, JoyStick.class);
                startActivity(intent);
                finish();
                break;
            }

            case 1:{
                Intent i = new Intent(this,newAccelerometer.class);
                startActivity(i);
                finish();
                break;
            }

            case 2:{
                Intent i = new Intent(this,gpsControl.class);
                startActivity(i);
                finish();
                break;
            }

            case 3:{
                Intent i = new Intent(this,lineDrawer.class);
                startActivity(i);
                finish();
                break;
            }
            default:{
                break;
            }
        }
    }
    @Override
    public void finish() {
        super.finish();
        overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right);
    }
}
