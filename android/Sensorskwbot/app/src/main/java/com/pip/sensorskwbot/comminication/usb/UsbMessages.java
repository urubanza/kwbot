package com.pip.sensorskwbot.comminication.usb;

public interface UsbMessages {
    void onStatus(String message);
    void onError(String message);
    void onWarning(String message);
}
