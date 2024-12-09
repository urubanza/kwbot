package com.pip.sensorskwbot.comminication.usb;

import android.os.Handler;
import com.pip.sensorskwbot.felhr.driver.UsbSerialPort;
import java.io.IOException;
import java.util.EnumSet;

public class ControlLines {
    private static final int refreshInterval = 200; // msec

    private Handler mainLooper;
    private UsbMessages statusMessage;

    private final Runnable runnable;
    private Boolean connected = false;

    ControlLines(Handler handler,UsbMessages usbMessages) {
        runnable = this::run;
        mainLooper = handler;
        statusMessage = usbMessages;
    }

    public ControlLines connect(){
        connected = true;
        return this;
    }

    public ControlLines disconnect(){
        connected = false;
        return this;
    }

    public Boolean Connected(){
        return connected;
    }


    private void toggle(UsbSerialPort usbSerialPort,boolean RTS_DTR, boolean Setted) {
        if (!this.Connected()) {
            statusMessage.onWarning("Low Level USB not Connected");
            return;
        }
        String ctrl = "";
        try {
            if(RTS_DTR) usbSerialPort.setRTS(Setted);
            else usbSerialPort.setDTR(Setted);
        } catch (IOException e) {
            statusMessage.onError("set" + ctrl + "() failed: " + e.getMessage());
        }
    }

    private void run() {
        if(!Connected()) {
            statusMessage.onError("Low level Device is not connected");
            return;
        }
        try {
//            EnumSet<UsbSerialPort.ControlLine> controlLines = usbSerialPort.getControlLines();
//            rtsBtn.setChecked(controlLines.contains(UsbSerialPort.ControlLine.RTS));
//            ctsBtn.setChecked(controlLines.contains(UsbSerialPort.ControlLine.CTS));
//            dtrBtn.setChecked(controlLines.contains(UsbSerialPort.ControlLine.DTR));
//            dsrBtn.setChecked(controlLines.contains(UsbSerialPort.ControlLine.DSR));
//            cdBtn.setChecked(controlLines.contains(UsbSerialPort.ControlLine.CD));
//            riBtn.setChecked(controlLines.contains(UsbSerialPort.ControlLine.RI));
            mainLooper.postDelayed(runnable, refreshInterval);
        } catch (Exception e) {
            String errorMessage = "getControlLines() failed: " + e.getMessage() + " -> stopped control line refresh";
            statusMessage.onError(errorMessage);
        }
    }
    void start() {
        if (!connected)
            return;
        try {
//            EnumSet<UsbSerialPort.ControlLine> controlLines = usbSerialPort.getSupportedControlLines();
//            if (!controlLines.contains(UsbSerialPort.ControlLine.RTS)) rtsBtn.setVisibility(View.INVISIBLE);
//            if (!controlLines.contains(UsbSerialPort.ControlLine.CTS)) ctsBtn.setVisibility(View.INVISIBLE);
//            if (!controlLines.contains(UsbSerialPort.ControlLine.DTR)) dtrBtn.setVisibility(View.INVISIBLE);
//            if (!controlLines.contains(UsbSerialPort.ControlLine.DSR)) dsrBtn.setVisibility(View.INVISIBLE);
//            if (!controlLines.contains(UsbSerialPort.ControlLine.CD))   cdBtn.setVisibility(View.INVISIBLE);
//            if (!controlLines.contains(UsbSerialPort.ControlLine.RI))   riBtn.setVisibility(View.INVISIBLE);
            run();
        } catch (Exception e) {
            statusMessage.onError("Getting Supported Control Lines Failed :" + e.getMessage());
//            rtsBtn.setVisibility(View.INVISIBLE);
//            ctsBtn.setVisibility(View.INVISIBLE);
//            dtrBtn.setVisibility(View.INVISIBLE);
//            dsrBtn.setVisibility(View.INVISIBLE);
//            cdBtn.setVisibility(View.INVISIBLE);
//            cdBtn.setVisibility(View.INVISIBLE);
//            riBtn.setVisibility(View.INVISIBLE);
        }
    }

    void stop() {
        mainLooper.removeCallbacks(runnable);
    }
}
