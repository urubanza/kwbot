package com.pip.sensorskwbot.modes;
import android.graphics.Bitmap;
import android.util.Log;
import com.pip.sensorskwbot.Detector;
import com.pip.sensorskwbot.comminication.usb.SerialTimers;
import com.pip.sensorskwbot.comminication.usb.USB;
import com.pip.sensorskwbot.filters.INSInterval;
import org.java_websocket.client.WebSocketClient;
import org.json.JSONException;
import org.json.JSONObject;
import java.util.ArrayList;
import java.util.List;
public class oBjectFollower {
    private List<Detector.Recognition> Objects;
    private static float MINIMUM_CONFODENCE = 0.6F;
    private String classType = "person";
    private INSInterval magnetoInterval;
    private SerialTimers localSerialTimer;
    private USB LowerLevelMotors;
    private CommandGenerator commandGenerator;
    public oBjectFollower(){
        magnetoInterval = new INSInterval(0.9);
        localSerialTimer = new SerialTimers();
        Objects = new ArrayList<>();
        commandGenerator = new CommandGenerator();
    }
    public boolean read(Detector detector, Bitmap bitmap){
        Objects = detector.recognizeImage(bitmap,classType);
        return found();
    }
    public boolean found(){
        return !Objects.isEmpty();
    }
    public int total(){
        return Objects.size();
    }
    public oBjectFollower Motor(USB LowLev){
        LowerLevelMotors = LowLev;
        return this;
    }
    public oBjectFollower run(WebSocketClient socket){
        if(LowerLevelMotors==null && socket == null){
            Log.d("STATIC_OPTIONS_FOLLOWER", "Both motor and web Socket are null");
            return this;
        } else if(LowerLevelMotors != null){
            localSerialTimer.message(commandGenerator.forward(0.9f));
            if(localSerialTimer.vlid()){
                LowerLevelMotors.send(localSerialTimer.message());
            }
            return this;
        }
        else if(socket.isOpen()){
            SendUsbFailedServer(socket,"No USB Found, No Motors To Run", "run");
        }
        else Log.d("STATIC_OPTIONS_FOLLOWER","WebSocket Server not Found!");
        return this;
    }
    private static void SendUsbFailedServer(WebSocketClient socketClient, String message, String type){
        JSONObject toSends = new JSONObject();
        try {
            toSends.put("stop",type);
            toSends.put("message",message);
            socketClient.send(toSends.toString());
        } catch (JSONException e) {
            throw new RuntimeException(e);
        }
    }
    public oBjectFollower stop(WebSocketClient socket){
        if(LowerLevelMotors==null && socket == null){
            Log.d("STATIC_OPTIONS_FOLLOWER", "Both motor and web Socket are null");
        } else if(LowerLevelMotors != null){
            localSerialTimer.message(commandGenerator.stop());
            if(localSerialTimer.vlid()) LowerLevelMotors.send(localSerialTimer.message());
        }
        else if(socket.isOpen()) SendUsbFailedServer(socket,"No USB Found, No Motors To Stop","stop");
        else Log.d("STATIC_OPTIONS_FOLLOWER","WebSocket Server not Found!");
        return this;
    }
    public oBjectFollower filter(){
        List<Detector.Recognition> newList = new ArrayList<>();
        for(int i = 0; i < Objects.size(); i++){
            if(Objects.get(i).getConfidence()>=MINIMUM_CONFODENCE) newList.add(Objects.get(i));
        }
        Objects = newList;
        return this;
    }
    oBjectFollower Class(String newClass){
        classType = newClass;
        return this;
    }

    public List<Detector.Recognition> results(){
        return Objects;
    }


}
