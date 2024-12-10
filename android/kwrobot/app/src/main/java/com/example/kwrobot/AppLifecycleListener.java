package com.example.kwrobot;

import android.app.Activity;
import android.app.Application;
import android.os.Bundle;

public class AppLifecycleListener implements Application.ActivityLifecycleCallbacks {
    private int activityReferences = 0;
    private boolean isActivityChangingConfigurations = false;
    private final Runnable onBackgrounded;
    private final Runnable onForegrounded;

    public AppLifecycleListener(Runnable onBackgrounded, Runnable onForegrounded) {
        this.onBackgrounded = onBackgrounded;
        this.onForegrounded = onForegrounded;
    }

    @Override
    public void onActivityCreated(Activity activity, Bundle savedInstanceState) {}

    @Override
    public void onActivityStarted(Activity activity) {
        if (++activityReferences == 1 && !isActivityChangingConfigurations) {
            onForegrounded.run();
        }
    }

    @Override
    public void onActivityStopped(Activity activity) {
        isActivityChangingConfigurations = activity.isChangingConfigurations();
        if (--activityReferences == 0 && !isActivityChangingConfigurations) {
            onBackgrounded.run();
        }
    }

    @Override
    public void onActivityResumed(Activity activity) {}

    @Override
    public void onActivityPaused(Activity activity) {}

    @Override
    public void onActivitySaveInstanceState(Activity activity, Bundle outState) {}

    @Override
    public void onActivityDestroyed(Activity activity) {}
}
