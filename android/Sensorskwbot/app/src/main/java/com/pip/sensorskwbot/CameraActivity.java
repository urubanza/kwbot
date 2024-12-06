package com.pip.sensorskwbot;


import android.annotation.SuppressLint;
import android.content.res.Configuration;
import android.graphics.Bitmap;
import android.os.Bundle;
import android.util.Size;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import com.google.common.util.concurrent.ListenableFuture;

import com.pip.sensorskwbot.env.ImageUtils;
import com.pip.sensorskwbot.env.SharedPreferencesManager;
import com.pip.sensorskwbot.utils.Constants;
import com.pip.sensorskwbot.utils.Enums;
import com.pip.sensorskwbot.utils.PermissionUtils;
import com.pip.sensorskwbot.utils.YuvToRgbConverter;
import com.pip.sensorskwbot.utils.pTimber;

import java.util.concurrent.ExecutionException;
import java.util.concurrent.ExecutorService;

import androidx.activity.result.ActivityResultLauncher;
import androidx.activity.result.contract.ActivityResultContracts;
import androidx.annotation.Nullable;
import androidx.camera.core.AspectRatio;
import androidx.camera.core.CameraSelector;
import androidx.camera.core.ImageAnalysis;
import androidx.camera.core.ImageProxy;
import androidx.camera.core.Preview;
import androidx.camera.lifecycle.ProcessCameraProvider;
import androidx.camera.view.PreviewView;
import androidx.core.content.ContextCompat;
import androidx.viewbinding.ViewBinding;
//import androidx.viewbinding.ViewBinding;
//import timber.log.Timber;

public abstract class CameraActivity extends ControlsActivity{
    private ExecutorService cameraExecutor;
    protected SharedPreferencesManager preferencesManager;
    private PreviewView previewView;
    private Preview preview;
    protected int lensFacing;
    private ProcessCameraProvider cameraProvider;
    private Size analyserResolution = Enums.Preview.HD.getValue();
    private YuvToRgbConverter converter;
    private Bitmap bitmapBuffer;
    private int rotationDegrees;

    @Override
    public void onCreate(@Nullable Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        preferencesManager = new SharedPreferencesManager(this);
    }

    protected View inflateFragment(View view){
        return addCamera(view);
    }

    protected View inflateFragment(int resId, ViewGroup container) {
        LayoutInflater inflater = getLayoutInflater();
        return addCamera(inflater.inflate(resId, container, false), container);
    }

    protected View addCamera(View view){
        View cameraView = view;
        previewView = cameraView.findViewById(R.id.viewFinder);
        lensFacing = CameraSelector.LENS_FACING_BACK;

        if (!PermissionUtils.hasCameraPermission(this)) {
            requestPermissionLauncherCamera.launch(Constants.PERMISSION_CAMERA);
        } else if (PermissionUtils.shouldShowRational(this, Constants.PERMISSION_CAMERA)) {
            PermissionUtils.showCameraPermissionsPreviewToast(this);
        } else {
            setupCamera();
        }

        return cameraView;
    }

    private View addCamera(View view, ViewGroup container) {
        // Inflate the fragment_camera layout directly in the Activity
        LayoutInflater inflater = getLayoutInflater();
        View cameraView = inflater.inflate(R.layout.fragment_camera, container, false);

        // Find the previewView in the inflated layout
        previewView = cameraView.findViewById(R.id.viewFinder);

        // Assuming lensFacing and preferencesManager are defined in the Activity class
        lensFacing = preferencesManager.getCameraSwitch()
                ? CameraSelector.LENS_FACING_FRONT
                : CameraSelector.LENS_FACING_BACK;

        // Add the passed view to the rootView of the inflated layout
        ViewGroup rootView = (ViewGroup) cameraView.getRootView();
        rootView.addView(view);

        // Check for camera permission and set up camera if permission is granted
        if (!PermissionUtils.hasCameraPermission(this)) {
            requestPermissionLauncherCamera.launch(Constants.PERMISSION_CAMERA);
        } else if (PermissionUtils.shouldShowRational(this, Constants.PERMISSION_CAMERA)) {
            PermissionUtils.showCameraPermissionsPreviewToast(this);
        } else {
            setupCamera();
        }

        return cameraView;
    }

    protected View inflateFragment(ViewBinding viewBinding, ViewGroup container) {
        LayoutInflater inflater = getLayoutInflater();
        return addCamera(viewBinding.getRoot(), container);
    }

    private final ActivityResultLauncher<String> requestPermissionLauncherCamera =
            registerForActivityResult(
                    new ActivityResultContracts.RequestPermission(),
                    isGranted -> {
                        if (isGranted) {
                            setupCamera();
                        } else if (PermissionUtils.shouldShowRational(
                                CameraActivity.this, Constants.PERMISSION_CAMERA)) {
                            PermissionUtils.showCameraPermissionsPreviewToast(CameraActivity.this);
                        } else {

                        }
                    });


    @Override
    public void onDestroy() {
        super.onDestroy();
        cameraExecutor.shutdown();
    }
    @SuppressLint("RestrictedApi")
    public Size getPreviewSize() {
        return preview.getAttachedSurfaceResolution();
    }
    public Size getMaxAnalyseImageSize() {
        return new Size(bitmapBuffer.getWidth(), bitmapBuffer.getHeight());
    }
    public void toggleCamera() {
        lensFacing =
                CameraSelector.LENS_FACING_FRONT == lensFacing
                        ? CameraSelector.LENS_FACING_BACK
                        : CameraSelector.LENS_FACING_FRONT;
        preferencesManager.setCameraSwitch(!preferencesManager.getCameraSwitch());
        bindCameraUseCases();
    }

    public void setAnalyserResolution(Size resolutionSize) {
        if (resolutionSize == null) analyserResolution = null;
        else {
            if (getResources().getConfiguration().orientation == Configuration.ORIENTATION_LANDSCAPE)
                this.analyserResolution = new Size(resolutionSize.getHeight(), resolutionSize.getWidth());
            else this.analyserResolution = resolutionSize;
        }
        bindCameraUseCases();
    }
    @SuppressLint("RestrictedApi")
    private void setupCamera() {
        ListenableFuture<ProcessCameraProvider> cameraProviderFuture =
                ProcessCameraProvider.getInstance(getApplicationContext());

        cameraProviderFuture.addListener(new Runnable() {
            @Override
            public void run() {
                try {
                    cameraProvider = cameraProviderFuture.get();
                    bindCameraUseCases();
                } catch (ExecutionException | InterruptedException e) {
                    pTimber.e("Camera setup failed: %s", e.toString());
                    throw new RuntimeException(e);
                }
            }
        },ContextCompat.getMainExecutor(getApplicationContext()));

        cameraProviderFuture.addListener(
                () -> {
                    try {
                        cameraProvider = cameraProviderFuture.get();
                        bindCameraUseCases();
                    } catch (ExecutionException | InterruptedException e) {
                        pTimber.e("Camera setup failed: %s", e.toString());
                    }
                },
                ContextCompat.getMainExecutor(getApplicationContext()));
    }

    @SuppressLint({"UnsafeExperimentalUsageError", "UnsafeOptInUsageError"})
    private void bindCameraUseCases() {
        converter = new YuvToRgbConverter(getApplicationContext());
        bitmapBuffer = null;
        preview = new Preview.Builder().setTargetAspectRatio(AspectRatio.RATIO_16_9).build();
        final boolean rotated = ImageUtils.getScreenOrientation(CameraActivity.this) % 180 == 90;
        final PreviewView.ScaleType scaleType =
                rotated ? PreviewView.ScaleType.FIT_CENTER : PreviewView.ScaleType.FIT_START;
        previewView.setScaleType(scaleType);
        preview.setSurfaceProvider(previewView.getSurfaceProvider());
        CameraSelector cameraSelector =
                new CameraSelector.Builder().requireLensFacing(lensFacing).build();
        ImageAnalysis imageAnalysis;

        if (analyserResolution == null)
            imageAnalysis =
                    new ImageAnalysis.Builder().setTargetAspectRatio(AspectRatio.RATIO_16_9).build();
        else
            imageAnalysis = new ImageAnalysis.Builder().setTargetResolution(analyserResolution).build();
        // insert your code here.
        imageAnalysis.setAnalyzer(
                cameraExecutor,
                image -> {
                    if (bitmapBuffer == null)
                        bitmapBuffer =
                                Bitmap.createBitmap(image.getWidth(), image.getHeight(), Bitmap.Config.ARGB_8888);

                    rotationDegrees = image.getImageInfo().getRotationDegrees();
                    converter.yuvToRgb(image.getImage(), bitmapBuffer);
                    image.close();

                    processFrame(bitmapBuffer, image);
                });
        try {
            if (cameraProvider != null) {
                cameraProvider.unbindAll();
                cameraProvider.bindToLifecycle(this, cameraSelector, preview, imageAnalysis);
            }
        } catch (Exception e) {
            pTimber.e("Use case binding failed: %s", e.toString());
        }
    }
    public int getRotationDegrees() {
        return rotationDegrees;
    }

    protected abstract void processFrame(Bitmap image, ImageProxy imageProxy);

}
