package com.example.kwrobot;

import android.app.Activity;
import android.app.Application;
import android.os.Handler;
import android.util.Log;

public class SessionHandler implements Application.ActivityLifecycleCallbacks {
    private static final long SESSION_TIMEOUT = 5 * 60 * 1000; // 5 minutes
    private final Handler timeoutHandler = new Handler();
    private final Runnable logoutRunnable = this::logoutUser;

    private boolean isAppInForeground = false;

    private static SessionHandler instance;

    public static void initialize(Application application) {
        if (instance == null) {
            instance = new SessionHandler();
            application.registerActivityLifecycleCallbacks(instance);
        }
    }

    private void logoutUser() {
        Log.d("SessionHandler", "Session expired. Logging out user.");
        // Call your logout logic here
        SessionManager sessionManager = SessionManager.getInstance();
        sessionManager.logoutUser();
    }

    private void resetTimeout() {
        timeoutHandler.removeCallbacks(logoutRunnable);
        timeoutHandler.postDelayed(logoutRunnable, SESSION_TIMEOUT);
    }

    private void stopTimeout() {
        timeoutHandler.removeCallbacks(logoutRunnable);
    }

    @Override
    public void onActivityCreated(Activity activity, android.os.Bundle savedInstanceState) {
        // No action needed
    }

    @Override
    public void onActivityStarted(Activity activity) {
        if (!isAppInForeground) {
            isAppInForeground = true;
            Log.d("SessionHandler", "App entered foreground");
            resetTimeout();
        }
    }

    @Override
    public void onActivityResumed(Activity activity) {
        resetTimeout();
    }

    @Override
    public void onActivityPaused(Activity activity) {
        stopTimeout();
    }

    @Override
    public void onActivityStopped(Activity activity) {
        // Check if the app is still in the foreground
        isAppInForeground = false;
        stopTimeout();
        Log.d("SessionHandler", "App moved to background");
    }

    @Override
    public void onActivitySaveInstanceState(Activity activity, android.os.Bundle outState) {
        // No action needed
    }

    @Override
    public void onActivityDestroyed(Activity activity) {
        // No action needed
    }
}
