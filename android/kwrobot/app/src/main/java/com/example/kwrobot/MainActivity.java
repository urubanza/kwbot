package com.example.kwrobot;

import android.os.Bundle;


import com.example.kwrobot.ui.dashboard.DashboardFragment;
import com.example.kwrobot.ui.home.HomeFragment;
import com.example.kwrobot.ui.notifications.NotificationsFragment;
import com.google.android.material.bottomnavigation.BottomNavigationView;


import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;

import androidx.fragment.app.FragmentManager;
import androidx.fragment.app.FragmentTransaction;

import android.util.Log;
import android.view.MenuItem;
import android.widget.Toast;

import org.json.JSONException;
import org.json.JSONObject;

import java.net.URI;
import java.net.URISyntaxException;

import io.socket.client.IO;
import io.socket.client.Socket;

import tech.gusavila92.websocketclient.WebSocketClient;

public class MainActivity extends AppCompatActivity {
    public Config config = new Config();
    private Socket AgentSocket;
    private Boolean sentJust = false;
    private WebSocketClient wb;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_main);
        final BottomNavigationView navView = findViewById(R.id.nav_view);
        FragmentManager fragmentManager = getSupportFragmentManager();
        FragmentTransaction fragmentTransaction = fragmentManager.beginTransaction();

        HomeFragment homefragment = new HomeFragment();
        fragmentTransaction.add(R.id.fragment_container, homefragment);
        fragmentTransaction.commit();

        navView.setOnNavigationItemSelectedListener(new BottomNavigationView.OnNavigationItemSelectedListener() {
            @Override
            public boolean onNavigationItemSelected(@NonNull MenuItem menuItem) {
                switch (menuItem.getItemId()){
                    case R.id.navigation_home: {
                        FragmentManager fragmentManager = getSupportFragmentManager();
                        FragmentTransaction fragmentTransaction = fragmentManager.beginTransaction();
                        HomeFragment homefragment = new HomeFragment();
                        fragmentTransaction.replace(R.id.fragment_container, homefragment);
                        fragmentTransaction.addToBackStack(null);
                        fragmentTransaction.commit();
                        break;
                    }
                    case R.id.navigation_dashboard: {
                        FragmentManager fragmentManager = getSupportFragmentManager();
                        FragmentTransaction fragmentTransaction = fragmentManager.beginTransaction();
                        DashboardFragment Dashfragment = new DashboardFragment();
                        fragmentTransaction.replace(R.id.fragment_container, Dashfragment);
                        fragmentTransaction.addToBackStack(null);
                        fragmentTransaction.commit();
                        break;
                    }
                    case R.id.navigation_notifications: {
                        FragmentManager fragmentManager = getSupportFragmentManager();
                        FragmentTransaction fragmentTransaction = fragmentManager.beginTransaction();
                        NotificationsFragment Notfragment = new NotificationsFragment();
                        fragmentTransaction.replace(R.id.fragment_container, Notfragment);
                        fragmentTransaction.addToBackStack(null);
                        fragmentTransaction.commit();
                        break;
                    }
                }
                return true;
            }
        });
        realTime();
        ConnectTopipServer();
    }
    public void playDefault(){
        try{
            JSONObject sends = new JSONObject();
            sends.put("play","0");
            AgentSocket.emit("play_test",sends);
        }
        catch (JSONException bn){
            Log.d("Error Send JSON",bn.getMessage());
        }
    }
    public void realTime(){
        try {
            AgentSocket = IO.socket(config.MAIN_WEB_SOCKET_SERVER).connect();
            Toast.makeText(this,"connection to "+config.MAIN_WEB_SOCKET_SERVER, Toast.LENGTH_LONG).show();
            if(!sentJust){
                try{
                    JSONObject sends = new JSONObject();
                    sends.put("username","root");
                    sends.put("password","123");
                    AgentSocket.emit("Android_connect",sends);
                }
                catch (JSONException bn){
                    Log.d("Error Send JSON",bn.getMessage());
                }
                sentJust = true;
            }
        }
        catch (URISyntaxException e){
            Log.d("Bad Url",e.toString());
        }
    }
    @Override
    public void finish(){
        super.finish();
        overridePendingTransition(R.anim.slide_in_left, R.anim.slide_out_right);
    }

    private void ConnectTopipServer(){
        URI uri;
        try{
            uri = new URI(config.PIPserverSockets);
        } catch (URISyntaxException e){
            e.printStackTrace();
            return;
        }

        wb = new WebSocketClient(uri) {
            @Override
            public void onOpen() {
                Log.i("Websocket","Session is starting");
                wb.send("Hello World");
            }

            @Override
            public void onTextReceived(String message) {
                Log.i("WebSocket","Message received");
            }

            @Override
            public void onBinaryReceived(byte[] data) {

            }

            @Override
            public void onPingReceived(byte[] data) {

            }

            @Override
            public void onPongReceived(byte[] data) {

            }

            @Override
            public void onException(Exception e) {
                Log.i("WebSocket Exeption", e.getMessage());
            }

            @Override
            public void onCloseReceived() {
                Log.i("webSocket","Closed");
            }
        };

        wb.setConnectTimeout(10000);
        wb.setReadTimeout(6000);
        wb.enableAutomaticReconnection(5000);
        wb.connect();
    }
}
