package com.example.kwrobot;

import android.Manifest;
import android.annotation.SuppressLint;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.location.Location;
import android.location.LocationListener;
import android.location.LocationManager;
import android.os.Build;
import android.os.Bundle;
import android.provider.Settings;
import android.util.Log;
import android.widget.TextView;
import android.widget.Toast;

import com.example.kwrobot.R;

import org.json.JSONException;
import org.json.JSONObject;

import androidx.appcompat.app.AppCompatActivity;

public class gpsControl_old extends AppCompatActivity {

    LocationManager locationManager;
    private Boolean hasGps = false;
    private Boolean hasNetwork = false;
    private Location locationGps = null;
    private Location locationNetwork = null;
    private final int PERMISSION_REQUEST = 10;

    private TextView showValues;

    // the position according to the GPS values given
    private int xPos = 0;
    private int yPos = 0;
    private double prevLon = 0;
    private double prevlat = 0;
    private final double meterCoeef = 111000;

    private String[] permissions = {
            Manifest.permission.ACCESS_FINE_LOCATION,
            Manifest.permission.ACCESS_COARSE_LOCATION
    };
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_gps_control);

        showValues = findViewById(R.id.showValues);

        if (Build.VERSION.SDK_INT >= Build.VERSION_CODES.M) {
            if (checkPermission(permissions)) {
                getLocation();
            } else {
                requestPermissions(permissions, PERMISSION_REQUEST);
            }
        } else {
            getLocation();
        }
    }
    @SuppressLint("MissingPermission")
    private void getLocation(){
        locationManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);
        hasGps = locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER);
        hasNetwork = locationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER);
        if(hasGps || hasNetwork){
            if(hasNetwork){
                locationManager.requestLocationUpdates(LocationManager.NETWORK_PROVIDER, 1000, 0F, new LocationListener() {
                    @Override
                    public void onLocationChanged(Location location) {
                        calculateDistance(location);
                    }

                    @Override
                    public void onStatusChanged(String s, int i, Bundle bundle) {

                    }

                    @Override
                    public void onProviderEnabled(String s) {

                    }

                    @Override
                    public void onProviderDisabled(String s) {

                    }
                });
            }
            else if(hasGps){
                Toast.makeText(this,"no network found using GPS",Toast.LENGTH_LONG).show();
                locationManager.requestLocationUpdates(LocationManager.GPS_PROVIDER, 1000, 0F, new LocationListener() {
                    @Override
                    public void onLocationChanged(Location location) {
                        calculateDistance(location);
                    }

                    @Override
                    public void onStatusChanged(String s, int i, Bundle bundle) {

                    }

                    @Override
                    public void onProviderEnabled(String s) {

                    }

                    @Override
                    public void onProviderDisabled(String s) {

                    }
                });
            }
        }
        else {
            startActivity(new Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS));
        }
    }
    private Boolean checkPermission(String[] permissions){
        Boolean AllSuccess = true;
        for(int i = 0; i< permissions.length; i++){
            if(checkCallingOrSelfPermission(permissions[i])== PackageManager.PERMISSION_DENIED){
                AllSuccess = false;
            }
        }
        return AllSuccess;
    }
    private void calculateDistance(Location location){
        if (location != null) {
            locationGps = location;
            if(prevLon==0)
                prevLon = location.getLongitude();
            if(prevlat==0)
                prevlat = location.getLatitude();



            double xchange = (prevLon - location.getLongitude())*meterCoeef;
            double ychange = (prevlat - location.getLatitude())*meterCoeef;

            showValues.setText("xChange:"+xchange+" yChange:"+ychange);

            prevLon = location.getLongitude();
            prevlat = location.getLatitude();

            JSONObject sends = new JSONObject();
            try{
                sends.put("x",xchange);
                sends.put("y",ychange);
                sends.put("alt",String.valueOf(locationGps.getAltitude()));
                //AgentSocket.emit("GPSVS",sends);
            } catch (JSONException e){
                Log.d("er",e.toString());
            }
        }
        else {
            showValues.setText("location is null");
        }
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
            Toast.makeText(this, "permission granted", Toast.LENGTH_SHORT).show();
    }
}
