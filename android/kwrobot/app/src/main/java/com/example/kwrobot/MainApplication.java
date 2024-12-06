package com.example.kwrobot;

import android.app.Application;
import android.content.Context;

public class MainApplication extends Application {
    private static Context appContext;

    @Override
    public void onCreate() {
        super.onCreate();
        appContext = this;

        // Initialize SessionHandler
        SessionHandler.initialize(this);
    }

    public static Context getAppContext() {
        return appContext;
    }
}
