package com.example.kwrobot;

public class Config {
    // connectivity settings of the app on the main server
    public final String SERVER_PROTOCAL = "http://";
    public final String PIP_SERVER_PROTOCOLS = "ws://";
    public final String SERVER_NAME = "192.168.43.40";
    public final String DEVICE_LOG_SOCKET = "8080";
    public final String PIP_SERVER_SOCKET = "8050";
    public final String MAIN_WEB_SOCKET_SERVER = this.SERVER_PROTOCAL+this.SERVER_NAME+":"+this.DEVICE_LOG_SOCKET;
    public final String PIPserverSockets = this.PIP_SERVER_PROTOCOLS+this.SERVER_NAME+":"+this.PIP_SERVER_SOCKET;
}
