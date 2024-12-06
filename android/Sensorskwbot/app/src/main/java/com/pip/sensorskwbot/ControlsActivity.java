package com.pip.sensorskwbot;

import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Spinner;
import android.widget.Toast;

import com.pip.sensorskwbot.env.ControllerToBotEventBus;
import com.pip.sensorskwbot.utils.FileUtils;
import com.pip.sensorskwbot.utils.PermissionUtils;

import java.io.File;
import java.util.List;
import java.util.Map;
import java.util.function.Function;
import java.util.function.Predicate;
import java.util.stream.Collectors;

import androidx.activity.result.ActivityResultCallback;
import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.annotation.Nullable;
import androidx.appcompat.app.AppCompatActivity;
import com.pip.sensorskwbot.env.PhoneController;
import com.pip.sensorskwbot.utils.pTimber;

import org.jetbrains.annotations.NotNull;

public abstract class ControlsActivity extends AppCompatActivity implements ServerListener {
    protected List<Model> masterList;
    private ArrayAdapter<String> modelAdapter;

    protected PhoneController phoneController;

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        masterList = FileUtils.loadConfigJSONFromAsset(this);

        phoneController = PhoneController.getInstance(this);
    }

    private boolean allGranted = true;

    protected final ActivityResultLauncher<String[]> requestPermissionLauncher =
            registerForActivityResult(new ActivityResultContracts.RequestMultiplePermissions(),
                    new ActivityResultCallback<Map<String, Boolean>>() {
                        @Override
                        public void onActivityResult(Map<String, Boolean> o) {
                            o.forEach((permission, granted) -> allGranted = allGranted && granted);
                            if (allGranted) {
                                // Permissions granted, perform the action
                                phoneController.connect(ControlsActivity.this);
                            } else {
                                // Permissions not granted, show a toast or handle accordingly
                                PermissionUtils.showControllerPermissionsToast(ControlsActivity.this);
                            }
                        }
                    });

    @NotNull
    protected List<String> getModelNames(java.util.function.Predicate<Model> filter){
        if(masterList!=null)
        return masterList.stream()
                .filter(filter)
                .map(f -> FileUtils.nameWithoutExtension(f.name))
                .collect(Collectors.toList());
        return null;
    }

    @Override
    public void onResume() {
        //serverCommunication.start();
        super.onResume();
    }
    @Override
    public void onDestroy() {
        pTimber.d("onDestroy");
        ControllerToBotEventBus.unsubscribe(this.getClass().getSimpleName());
        //if(vehicle!=null)  vehicle.setControl(0, 0);
        super.onDestroy();
    }



    @Override
    public synchronized void onPause() {
        pTimber.d("onPause");
        //serverCommunication.stop();
        //if(vehicle!=null) vehicle.setControl(0, 0);
        super.onPause();
    }

    @Override
    public void onStop() {
        pTimber.d("onStop");
        super.onStop();
    }

    @Override
    public void onAddModel(String model) {
        Model item =
                new Model(
                        masterList.size() + 1,
                        Model.CLASS.AUTOPILOT,
                        Model.TYPE.CMDNAV,
                        model,
                        Model.PATH_TYPE.FILE,
                        this.getFilesDir() + File.separator + model,
                        "256x96");

        if (modelAdapter != null && modelAdapter.getPosition(model) == -1) {
            modelAdapter.add(model);
            masterList.add(item);
            FileUtils.updateModelConfig(this, masterList);
        } else {
            setModel(item);
        }
        Toast.makeText(
                        this.getApplicationContext(),
                        "AutopilotModel added: " + model,
                        Toast.LENGTH_SHORT)
                .show();
    }

    protected void initFirstModel() {

        List<String> models = getModelNames(new Predicate<Model>() {
            @Override
            public boolean test(Model f) {
                return f.type.equals(Model.TYPE.DETECTOR) && f.pathType != Model.PATH_TYPE.URL;
            }
        });

        if(models==null){
            Log.d("INITIKKKK","No List found here!!");
            return;
        }

        if(models.size()>0){
            masterList.stream().filter(new Predicate<Model>() {
                @Override
                public boolean test(Model model) {

                    return model.name.contains(models.get(0));
                }
            }).findFirst().filter(new Predicate<Model>() {
                @Override
                public boolean test(Model modelx) {
                    setModel(modelx);
                    return true;
                }
            });
        }
        else Log.d("INITIKKKK","No List found on your phone!!");
    }



    protected void setModel(Model model) {}

    protected abstract void processControllerKeyData(String command);

    protected abstract void processUSBData(String data);


}
