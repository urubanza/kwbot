package com.pip.sensorskwbot;

import android.os.Bundle;
import android.util.Log;
import android.view.KeyEvent;
import android.view.MotionEvent;
import android.view.View;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.Spinner;
import android.widget.Toast;
import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.annotation.NonNull;
import androidx.annotation.Nullable;
import androidx.fragment.app.Fragment;
import java.io.File;
import java.util.List;
import java.util.Set;
import java.util.function.Predicate;
import java.util.stream.Collectors;
import org.jetbrains.annotations.NotNull;
import org.json.JSONObject;

import com.pip.sensorskwbot.env.BotToControllerEventBus;
import com.pip.sensorskwbot.env.ControllerToBotEventBus;
import com.pip.sensorskwbot.env.PhoneController;
import com.pip.sensorskwbot.env.SharedPreferencesManager;
import com.pip.sensorskwbot.utils.ConnectionUtils;
import com.pip.sensorskwbot.utils.Constants;
import com.pip.sensorskwbot.utils.Enums;
import com.pip.sensorskwbot.utils.FileUtils;
import com.pip.sensorskwbot.utils.PermissionUtils;
import com.pip.sensorskwbot.utils.pTimber;


public abstract class ControlsFragment extends Fragment{
  private static final String NO_SERVER = "No server";

  protected Animation startAnimation;
  protected SharedPreferencesManager preferencesManager;
  protected PhoneController phoneController;
  protected Enums.DriveMode currentDriveMode = Enums.DriveMode.GAME;

  protected final String voice = "matthew";
  protected List<Model> masterList;

  private ArrayAdapter<String> modelAdapter;
  private ArrayAdapter<String> serverAdapter;
  private Spinner modelSpinner;
  private Spinner serverSpinner;

  @Override
  public void onCreate(@Nullable Bundle savedInstanceState) {
    super.onCreate(savedInstanceState);
    // create before inflateFragment() to prevent npe when calling addCamera()
    preferencesManager = new SharedPreferencesManager(requireContext());
  }

  @Override
  public void onViewCreated(@NonNull View view, @Nullable Bundle savedInstanceState) {
    super.onViewCreated(view, savedInstanceState);
    requireActivity()
        .getWindow()
        .addFlags(android.view.WindowManager.LayoutParams.FLAG_KEEP_SCREEN_ON);

    phoneController = PhoneController.getInstance(requireContext());

    masterList = FileUtils.loadConfigJSONFromAsset(requireActivity());




    requireActivity()
        .getSupportFragmentManager()
        .setFragmentResultListener(
            Constants.GENERIC_MOTION_EVENT,
            this,
            (requestKey, result) -> {
              MotionEvent motionEvent = result.getParcelable(Constants.DATA);
              processControllerKeyData(Constants.CMD_DRIVE);
            });
    requireActivity()
        .getSupportFragmentManager()
        .setFragmentResultListener(
            Constants.KEY_EVENT,
            this,
            (requestKey, result) -> {
              KeyEvent event = result.getParcelable(Constants.DATA);
              if (KeyEvent.ACTION_UP == event.getAction()) {
                processKeyEvent(result.getParcelable(Constants.DATA));
              }
            });

    startAnimation = AnimationUtils.loadAnimation(requireContext(), R.anim.blink);
    handlePhoneControllerEvents();
  }

  protected void processKeyEvent(KeyEvent keyCode) {
    if (Enums.ControlMode.getByID(preferencesManager.getControlMode())
        == Enums.ControlMode.GAMEPAD) {
      switch (keyCode.getKeyCode()) {
        case KeyEvent.KEYCODE_BUTTON_X: // square
          toggleIndicatorEvent(Enums.VehicleIndicator.LEFT.getValue());
          processControllerKeyData(Constants.CMD_INDICATOR_LEFT);
          break;
        case KeyEvent.KEYCODE_BUTTON_Y: // triangle
          toggleIndicatorEvent(Enums.VehicleIndicator.STOP.getValue());
          processControllerKeyData(Constants.CMD_INDICATOR_STOP);
          break;
        case KeyEvent.KEYCODE_BUTTON_B: // circle
          toggleIndicatorEvent(Enums.VehicleIndicator.RIGHT.getValue());
          processControllerKeyData(Constants.CMD_INDICATOR_RIGHT);
          break;
        case KeyEvent.KEYCODE_BUTTON_A: // x
          processControllerKeyData(Constants.CMD_LOGS);
          break;
        case KeyEvent.KEYCODE_BUTTON_START: // options
          toggleNoise();
          processControllerKeyData(Constants.CMD_NOISE);
          break;
        case KeyEvent.KEYCODE_BUTTON_L1:
          processControllerKeyData(Constants.CMD_DRIVE_MODE);
          break;
        case KeyEvent.KEYCODE_BUTTON_R1:
          processControllerKeyData(Constants.CMD_NETWORK);
          break;
        case KeyEvent.KEYCODE_BUTTON_THUMBL:
          processControllerKeyData(Constants.CMD_SPEED_DOWN);
          break;
        case KeyEvent.KEYCODE_BUTTON_THUMBR:
          processControllerKeyData(Constants.CMD_SPEED_UP);
          break;

        default:
          break;
      }
    }
  }

  private void handlePhoneControllerEvents() {
    ControllerToBotEventBus.subscribe(
        this.getClass().getSimpleName(),
        event -> {
          String commandType = "";
          if (event.has("command")) {
            commandType = event.getString("command");
          } else if (event.has("driveCmd")) {
            commandType = Constants.CMD_DRIVE;
          }

          switch (commandType) {
            case Constants.CMD_DRIVE:
              JSONObject driveValue = event.getJSONObject("driveCmd");
              break;

            case Constants.CMD_INDICATOR_LEFT:
              toggleIndicatorEvent(Enums.VehicleIndicator.LEFT.getValue());
              break;

            case Constants.CMD_INDICATOR_RIGHT:
              toggleIndicatorEvent(Enums.VehicleIndicator.RIGHT.getValue());
              break;

            case Constants.CMD_INDICATOR_STOP:
              toggleIndicatorEvent(Enums.VehicleIndicator.STOP.getValue());
              break;

              // We re connected to the controller, send back status info
            case Constants.CMD_CONNECTED:
              // PhoneController class will receive this event and resent it to the
              // controller.
              // Other controllers can subscribe to this event as well.
              // That is why we are not calling phoneController.send() here directly.
              BotToControllerEventBus.emitEvent(
                  ConnectionUtils.getStatus(
                      false, false, false, currentDriveMode.toString(),0 /*vehicle.getIndicator()*/));
              break;

            case Constants.CMD_DISCONNECTED:
              break;
          }

          processControllerKeyData(commandType);
        },
        error -> {
          Log.d(null, "Error occurred in ControllerToBotEventBus: " + error);
        },
        event -> event.has("command") || event.has("driveCmd") // filter out everything else
        );
  }

  protected void toggleNoise() {

  }

  private void toggleIndicatorEvent(int value) {
    BotToControllerEventBus.emitEvent(ConnectionUtils.createStatus("INDICATOR_LEFT", value == -1));
    BotToControllerEventBus.emitEvent(ConnectionUtils.createStatus("INDICATOR_RIGHT", value == 1));
    BotToControllerEventBus.emitEvent(ConnectionUtils.createStatus("INDICATOR_STOP", value == 0));
  }

  private boolean allGranted = true;
  protected final ActivityResultLauncher<String[]> requestPermissionLauncher =
      registerForActivityResult(
          new ActivityResultContracts.RequestMultiplePermissions(),
          result -> {
            result.forEach((permission, granted) -> allGranted = allGranted && granted);

            if (allGranted) phoneController.connect(requireContext());
            else {
              PermissionUtils.showControllerPermissionsToast(requireActivity());
            }
          });

  @NotNull
  protected List<String> getModelNames(Predicate<Model> filter) {
    return masterList.stream()
        .filter(filter)
        .map(f -> FileUtils.nameWithoutExtension(f.name))
        .collect(Collectors.toList());
  }

  @Override
  public void onResume() {
    super.onResume();
  }

  @Override
  public void onDestroy() {
    pTimber.d("onDestroy");
    ControllerToBotEventBus.unsubscribe(this.getClass().getSimpleName());
    super.onDestroy();
  }

  @Override
  public synchronized void onPause() {
    pTimber.d("onPause");
    super.onPause();
  }

  @Override
  public void onStop() {
    pTimber.d("onStop");
    super.onStop();
  }

  protected void initModelSpinner(Spinner spinner, List<String> models, String selected) {
    modelAdapter = new ArrayAdapter<>(requireContext(), R.layout.spinner_item, models);
    modelAdapter.setDropDownViewResource(android.R.layout.simple_dropdown_item_1line);
    modelSpinner = spinner;
    modelSpinner.setAdapter(modelAdapter);
    if (!selected.isEmpty())
      modelSpinner.setSelection(
          Math.max(0, modelAdapter.getPosition(FileUtils.nameWithoutExtension(selected))));
    modelSpinner.setOnItemSelectedListener(
        new AdapterView.OnItemSelectedListener() {
          @Override
          public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
            String selected = parent.getItemAtPosition(position).toString();
            try {
              masterList.stream()
                  .filter(f -> f.name.contains(selected))
                  .findFirst()
                  .ifPresent(value -> setModel(value));

            } catch (IllegalArgumentException e) {
              e.printStackTrace();
            }
          }

          @Override
          public void onNothingSelected(AdapterView<?> parent) {}
        });
  }

  protected void initServerSpinner(Spinner spinner) {
    serverAdapter = new ArrayAdapter<>(requireContext(), R.layout.spinner_item);
    serverAdapter.setDropDownViewResource(android.R.layout.simple_dropdown_item_1line);
    serverSpinner = spinner;
    serverSpinner.setAdapter(serverAdapter);
    serverSpinner.setOnItemSelectedListener(
        new AdapterView.OnItemSelectedListener() {
          @Override
          public void onItemSelected(AdapterView<?> parent, View view, int position, long id) {
            String selected = parent.getItemAtPosition(position).toString();
            if (selected.equals(NO_SERVER)) {
              if (serverAdapter.getPosition(preferencesManager.getServer()) > -1) {
                preferencesManager.setServer(selected);
              }
            } else {

              preferencesManager.setServer(selected);
            }
          }

          @Override
          public void onNothingSelected(AdapterView<?> parent) {


          }
        });
  }

  public void onServerListChange(Set<String> servers) {
    if (serverAdapter == null) {
      return;
    }
    requireActivity()
        .runOnUiThread(
            () -> {
              serverAdapter.clear();
              serverAdapter.add(NO_SERVER);
              serverAdapter.addAll(servers);
              if (!preferencesManager.getServer().isEmpty()) {
                serverSpinner.setSelection(
                    Math.max(0, serverAdapter.getPosition(preferencesManager.getServer())));
              }
            });
  }


  public void onAddModel(String model) {
    Model item =
        new Model(
            masterList.size() + 1,
            Model.CLASS.AUTOPILOT,
            Model.TYPE.CMDNAV,
            model,
            Model.PATH_TYPE.FILE,
            requireActivity().getFilesDir() + File.separator + model,
            "256x96");

    if (modelAdapter != null && modelAdapter.getPosition(model) == -1) {
      modelAdapter.add(model);
      masterList.add(item);
      FileUtils.updateModelConfig(requireActivity(), masterList);
    } else {
      if (model.equals(modelSpinner.getSelectedItem())) {
        setModel(item);
      }
    }
    Toast.makeText(
            requireContext().getApplicationContext(),
            "AutopilotModel added: " + model,
            Toast.LENGTH_SHORT)
        .show();
  }


  public void onRemoveModel(String model) {
    if (modelAdapter != null && modelAdapter.getPosition(model) != -1) {
      modelAdapter.remove(model);
    }
    Toast.makeText(
            requireContext().getApplicationContext(),
            "AutopilotModel removed: " + model,
            Toast.LENGTH_SHORT)
        .show();
  }


  protected void setModel(Model model) {}

  protected abstract void processControllerKeyData(String command);

  protected abstract void processUSBData(String data);
}
