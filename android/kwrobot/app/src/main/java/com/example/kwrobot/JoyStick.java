package com.example.kwrobot;

import android.annotation.SuppressLint;

import androidx.appcompat.app.ActionBar;
import androidx.appcompat.app.AppCompatActivity;
import io.socket.client.IO;

import android.os.Bundle;
import android.os.Handler;
import android.util.Log;
import android.view.MotionEvent;
import android.view.View;
import android.widget.Toast;

import org.java_websocket.client.WebSocketClient;
import org.json.JSONException;
import org.json.JSONObject;

import io.socket.client.IO;
import io.socket.client.Socket;
import kwbotControler.DrawLineCanvas;

import java.net.URI;
import java.net.URISyntaxException;
import java.util.Timer;
import java.util.TimerTask;

import org.java_websocket.client.WebSocketClient;
import org.java_websocket.handshake.ServerHandshake;

/**
 * An example full-screen activity that shows and hides the system UI (i.e.
 * status bar and navigation/system bar) with user interaction.
 */

public class JoyStick extends AppCompatActivity {
    private Socket AgentSocket;
    public Config config = new Config();
    public DrawLineCanvas joyStick;
    private boolean currentStoped = true;
    /**
     * Whether or not the system UI should be auto-hidden after
     * {@link #AUTO_HIDE_DELAY_MILLIS} milliseconds.
     */
    private static final boolean AUTO_HIDE = true;

    /**
     * If {@link #AUTO_HIDE} is set, the number of milliseconds to wait after
     * user interaction before hiding the system UI.
     */
    private static final int AUTO_HIDE_DELAY_MILLIS = 3000;

    /**
     * Some older devices needs a small delay between UI widget updates
     * and a change of the status and navigation bar.
     */
    private static final int UI_ANIMATION_DELAY = 300;
    private final Handler mHideHandler = new Handler();
    private View mContentView;
    private final Runnable mHidePart2Runnable = new Runnable() {
        @SuppressLint("InlinedApi")
        @Override
        public void run() {
            // Delayed removal of status and navigation bar

            // Note that some of these constants are new as of API 16 (Jelly Bean)
            // and API 19 (KitKat). It is safe to use them, as they are inlined
            // at compile-time and do nothing on earlier devices.
            mContentView.setSystemUiVisibility(View.SYSTEM_UI_FLAG_LOW_PROFILE
                    | View.SYSTEM_UI_FLAG_FULLSCREEN
                    | View.SYSTEM_UI_FLAG_LAYOUT_STABLE
                    | View.SYSTEM_UI_FLAG_IMMERSIVE_STICKY
                    | View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION
                    | View.SYSTEM_UI_FLAG_HIDE_NAVIGATION);
        }
    };
    private View mControlsView;
    private final Runnable mShowPart2Runnable = new Runnable() {
        @Override
        public void run() {
            // Delayed display of UI elements
            ActionBar actionBar = getSupportActionBar();
            if (actionBar != null) {
                actionBar.show();
            }
            mControlsView.setVisibility(View.VISIBLE);
        }
    };
    private boolean mVisible;
    private final Runnable mHideRunnable = new Runnable() {
        @Override
        public void run() {
            hide();
        }
    };
    /**
     * Touch listener to use for in-layout UI controls to delay hiding the
     * system UI. This is to prevent the jarring behavior of controls going away
     * while interacting with activity UI.
     */
    private final View.OnTouchListener mDelayHideTouchListener = new View.OnTouchListener() {
        @Override
        public boolean onTouch(View view, MotionEvent motionEvent) {
            if (AUTO_HIDE) {
                delayedHide(AUTO_HIDE_DELAY_MILLIS);
            }
            return false;
        }
    };

    private WebSocketClient webSocketClient;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        setContentView(R.layout.activity_joy_stick);

        mVisible = true;
        mControlsView = findViewById(R.id.fullscreen_content_controls);
        mContentView = findViewById(R.id.fullscreen_content);
        joyStick = findViewById(R.id.joyStick);


        // Set up the user interaction to manually show or hide the system UI.
        mContentView.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                toggle();
            }
        });

        // Upon interacting with UI controls, delay any scheduled hide()
        // operations to prevent the jarring behavior of controls going away
        // while interacting with the UI.
        findViewById(R.id.dummy_button).setOnTouchListener(mDelayHideTouchListener);

        try {
            AgentSocket = IO.socket(config.MAIN_WEB_SOCKET_SERVER).connect();
        } catch (URISyntaxException e){
            Toast.makeText(this,"invalid url",Toast.LENGTH_LONG).show();
        }

        timer t = new timer();
        Timer T = new Timer(true);
        T.scheduleAtFixedRate(t,0,100);

        try{
            Thread.sleep(100);
        }
        catch (InterruptedException e){
            e.printStackTrace();
        }
        connectWebSocket();
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
        return "ws://192.168.1.77:8000";
    }

    private void findJoyStick(){
        float dir = (float) joyStick.getAngle();
        float speed = (float) joyStick.getSpeed();
        if(dir==-1000){
            try{
                JSONObject j = new JSONObject();
                j.put("stops","0");
                if(currentStoped) {
                    AgentSocket.emit("joyStickStop",j);
                    currentStoped = false;
                }
            } catch (JSONException e){
                Log.d("Error",e.toString());
            }
        } else {
            try{
                JSONObject j = new JSONObject();
                j.put("speed",speed);
                j.put("dir",dir);
                AgentSocket.emit("joyStick",j);
                currentStoped = true;
            } catch (JSONException e) {
                Log.d("Data failed",e.toString());
            }
        }
        //findJoyStick();
    }
    @Override
    protected void onPostCreate(Bundle savedInstanceState) {
        super.onPostCreate(savedInstanceState);
        // Trigger the initial hide() shortly after the activity has been
        // created, to briefly hint to the user that UI controls
        // are available.
        delayedHide(100);
    }
    private void toggle() {
        if (mVisible) {
            hide();
        } else {
            show();
        }
    }
    private void hide() {
        // Hide UI first
        ActionBar actionBar = getSupportActionBar();
        if (actionBar != null) {
            actionBar.hide();
        }
        mControlsView.setVisibility(View.GONE);
        mVisible = false;

        // Schedule a runnable to remove the status and navigation bar after a delay
        mHideHandler.removeCallbacks(mShowPart2Runnable);
        mHideHandler.postDelayed(mHidePart2Runnable, UI_ANIMATION_DELAY);
    }
    @SuppressLint("InlinedApi")
    private void show() {
        // Show the system bar
        mContentView.setSystemUiVisibility(View.SYSTEM_UI_FLAG_LAYOUT_FULLSCREEN
                | View.SYSTEM_UI_FLAG_LAYOUT_HIDE_NAVIGATION);
        mVisible = true;

        // Schedule a runnable to display UI elements after a delay
        mHideHandler.removeCallbacks(mHidePart2Runnable);
        mHideHandler.postDelayed(mShowPart2Runnable, UI_ANIMATION_DELAY);
    }
    /**
     * Schedules a call to hide() in delay milliseconds, canceling any
     * previously scheduled calls.
     */
    private void delayedHide(int delayMillis) {
        mHideHandler.removeCallbacks(mHideRunnable);
        mHideHandler.postDelayed(mHideRunnable, delayMillis);
    }
    private class timer extends TimerTask{

        @Override
        public void run() {
            findJoyStick();
        }

        private void complete(){
            try{
                Thread.sleep(3000);
            } catch (InterruptedException e){
                e.printStackTrace();
            }
        }
    }
}
