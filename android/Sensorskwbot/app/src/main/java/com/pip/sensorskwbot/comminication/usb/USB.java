package com.pip.sensorskwbot.comminication.usb;

import android.app.Activity;
import android.app.PendingIntent;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.hardware.usb.UsbDevice;
import android.hardware.usb.UsbDeviceConnection;
import android.hardware.usb.UsbManager;
import android.os.Build;
import android.os.Handler;
import android.os.Looper;
import android.text.SpannableStringBuilder;
import android.util.Log;
import android.widget.Toast;

import com.koushikdutta.ion.BuildConfig;
import com.pip.sensorskwbot.felhr.driver.UsbSerialDriver;
import com.pip.sensorskwbot.felhr.driver.UsbSerialPort;
import com.pip.sensorskwbot.felhr.driver.UsbSerialProber;
import com.pip.sensorskwbot.felhr.util.HexDump;
import com.pip.sensorskwbot.felhr.util.SerialInputOutputManager;
import java.io.IOException;
import java.util.Arrays;

public class USB implements SerialInputOutputManager.Listener{
    private enum UsbPermission { Unknown, Requested, Granted, Denied }
    private static final String INTENT_ACTION_GRANT_USB = BuildConfig.APPLICATION_ID + ".GRANT_USB";
    private static final int WRITE_WAIT_MILLIS = 2000;
    private static final int READ_WAIT_MILLIS = 2000;
    private UsbDeviceIdentities usbDeviceIdentities;
    private boolean withIoManager;
    private final BroadcastReceiver broadcastReceiver;
    private final Handler mainLooper;
    private ControlLines controlLines;
    private SerialInputOutputManager usbIoManager;
    private UsbSerialPort usbSerialPort;

    private UsbPermission usbPermission = UsbPermission.Unknown;

    private UsbMessages TheusbMessages;
    public USB(Activity activity, UsbDeviceIdentities usbDev, UsbMessages usbMessages){
        usbDeviceIdentities = usbDev;
        TheusbMessages = usbMessages;
        broadcastReceiver = new BroadcastReceiver() {
            @Override
            public void onReceive(Context context, Intent intent) {
                if(INTENT_ACTION_GRANT_USB.equals(intent.getAction())) {
                    usbPermission = intent.getBooleanExtra(UsbManager.EXTRA_PERMISSION_GRANTED, false)
                            ? UsbPermission.Granted : UsbPermission.Denied;
                    connect(activity);
                }
            }
        };
        mainLooper = new Handler(Looper.getMainLooper());
        controlLines = new ControlLines(mainLooper,usbMessages);

        usbMessages.onStatus("Starting USB Low level communication");
    }
    public void connect(Activity activity) {
        UsbDevice device = null;
        UsbManager usbManager = (UsbManager) activity.getSystemService(Context.USB_SERVICE);
        for(UsbDevice v : usbManager.getDeviceList().values())
            if(v.getDeviceId() == usbDeviceIdentities.deviceId)
                device = v;
        if(device == null) {
            TheusbMessages.onError("connection failed to the id "+usbDeviceIdentities.deviceId+" : device not found");
            return;
        }
        UsbSerialDriver driver = UsbSerialProber.getDefaultProber().probeDevice(device);
        if(driver == null) {
            driver = CustomProber.getCustomProber().probeDevice(device);
        }
        if(driver == null) {
            TheusbMessages.onError("connection failed: no driver for device");
            return;
        }
        if(driver.getPorts().size() < usbDeviceIdentities.portNum) {
            TheusbMessages.onStatus("connection failed: not enough ports at device");
            return;
        }
        try{
            usbSerialPort = driver.getPorts().get(usbDeviceIdentities.portNum);
            UsbDeviceConnection usbConnection = usbManager.openDevice(driver.getDevice());

            if(usbConnection == null && usbPermission == UsbPermission.Unknown && !usbManager.hasPermission(driver.getDevice())) {
                usbPermission = UsbPermission.Requested;
                int flags = Build.VERSION.SDK_INT >= Build.VERSION_CODES.M ? PendingIntent.FLAG_MUTABLE : 0;
                PendingIntent usbPermissionIntent = PendingIntent.getBroadcast(activity, 0, new Intent(INTENT_ACTION_GRANT_USB), flags);
                usbManager.requestPermission(driver.getDevice(), usbPermissionIntent);
                return;
            }
            if(usbConnection == null) {
                if (!usbManager.hasPermission(driver.getDevice()))
                    TheusbMessages.onStatus("connection failed: permission denied");
                else
                    TheusbMessages.onStatus("connection failed: open failed");
                return;
            }
            try {
                usbSerialPort.open(usbConnection);
                try{
                    usbSerialPort.setParameters(usbDeviceIdentities.baudRate, 8, 1, UsbSerialPort.PARITY_NONE);
                }catch (UnsupportedOperationException e){
                    TheusbMessages.onStatus("unsupported set parameters");
                }
                if(withIoManager) {
                    usbIoManager = new SerialInputOutputManager(usbSerialPort, this);
                    usbIoManager.start();
                }
                TheusbMessages.onStatus("connected");
                controlLines.connect().start();
            }
            catch (Exception e) {
                TheusbMessages.onStatus("connection failed: " + e.getMessage());
                disconnect();
            }
        }
        catch (Exception e){
            String theMessage = "Failed "+ e.getMessage() + " Ports : "+usbDeviceIdentities.portNum;
            TheusbMessages.onError(theMessage);
        }

        //


    }




    public static UsbDeviceIdentities scanDevice(Activity activity){
        UsbManager usbManager = (UsbManager) activity.getSystemService(Context.USB_SERVICE);
        UsbDevice device = null;
        Toast.makeText(activity.getApplicationContext(),"Number of found devices : "+usbManager.getDeviceList().size(),Toast.LENGTH_LONG).show();
        for(UsbDevice v : usbManager.getDeviceList().values()){
            device = v;
        }
        if(device==null){
            return null;
        }
        UsbSerialDriver driver = UsbSerialProber.getDefaultProber().probeDevice(device);

        if(driver == null) {
            driver = CustomProber.getCustomProber().probeDevice(device);
        }
        if(driver == null) {
            Toast.makeText(activity.getApplicationContext(),"connection failed: no driver for device",Toast.LENGTH_LONG).show();
            return null;
        }

        UsbDeviceIdentities ids =  new UsbDeviceIdentities();
        ids.deviceId = device.getDeviceId();
        ids.portNum = driver.getPorts().size() - 1;
        return ids;
    }

    public static int scanDevicePort(Activity activity){
        UsbManager usbManager = (UsbManager) activity.getSystemService(Context.USB_SERVICE);
        Toast.makeText(activity.getApplicationContext(),"Number of found devices : "+usbManager.getDeviceList().size(),Toast.LENGTH_LONG).show();
        for(UsbDevice v : usbManager.getDeviceList().values()){
            return v.getDeviceId();
        }
        return 0;
    }
    private void disconnect() {
        controlLines.disconnect().stop();
        if(usbIoManager != null) {
            usbIoManager.setListener(null);
            usbIoManager.stop();
        }
        usbIoManager = null;
        try {
            usbSerialPort.close();
        } catch (IOException ignored) {
            TheusbMessages.onStatus(" Closing Usb Port Error : " + ignored.getMessage());
        }
        usbSerialPort = null;
    }

    public void send(String str) {
        if(!controlLines.Connected()){
            TheusbMessages.onStatus("Low level Usb Device not connected");
            return;
        }
        try {
            byte[] data = str.getBytes();
            SpannableStringBuilder spn = new SpannableStringBuilder();
            spn.append("send " + data.length + " bytes\n");
            spn.append(HexDump.dumpHexString(data)).append("\n");
            TheusbMessages.onStatus(spn.toString());
            usbSerialPort.write(data, WRITE_WAIT_MILLIS);
        } catch (Exception e) {
            TheusbMessages.onError("Failed to write on USB : " + e.getMessage());
            onRunError(e);
        }
    }

    private void read() {
        if(!controlLines.Connected()){
            TheusbMessages.onStatus("Low level Usb Device not connected");
            return;
        }
        try {
            byte[] buffer = new byte[8192];
            int len = usbSerialPort.read(buffer, READ_WAIT_MILLIS);
            receive(Arrays.copyOf(buffer, len));
        }
        catch (IOException e) {
            TheusbMessages.onError("connection lost: " + e.getMessage());
            disconnect();
        }
    }

    private void receive(byte[] data) {
        SpannableStringBuilder spn = new SpannableStringBuilder();
        spn.append("receive " + data.length + " bytes\n");
        if(data.length > 0) spn.append(HexDump.dumpHexString(data)).append("\n");
        TheusbMessages.onStatus("Received : " + spn);
        //receiveText.append(spn);
    }
    @Override
    public void onNewData(byte[] data) {
        mainLooper.post(new Runnable() {
            @Override
            public void run() {
                receive(data);
            }
        });
    }
    @Override
    public void onRunError(Exception e) {
        mainLooper.post(() -> {
            TheusbMessages.onError("connection lost: " + e.getMessage());
            disconnect();
        });
    }
}
