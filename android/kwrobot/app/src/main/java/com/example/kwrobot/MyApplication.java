package com.example.kwrobot;

import android.app.Activity;
import android.app.Application;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;

import androidx.annotation.NonNull;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.example.kwrobot.ui.OuthActivity;

import org.json.JSONException;
import org.json.JSONObject;

public class MyApplication extends Application {

    private int activityReferences = 0;
    private boolean isActivityChangingConfigurations = false;

    @Override
    public void onCreate() {
        super.onCreate();

        registerActivityLifecycleCallbacks(new ActivityLifecycleCallbacks() {
            @Override
            public void onActivityCreated(@NonNull Activity activity, Bundle savedInstanceState) {}

            @Override
            public void onActivityStarted(@NonNull Activity activity) {
                if (++activityReferences == 1 && !isActivityChangingConfigurations) {
                    Log.d("AppLifecycle", "App moved to foreground.");
                }
            }

            @Override
            public void onActivityResumed(@NonNull Activity activity) {}

            @Override
            public void onActivityPaused(@NonNull Activity activity) {}

            @Override
            public void onActivityStopped(@NonNull Activity activity) {
                isActivityChangingConfigurations = activity.isChangingConfigurations();
                if (--activityReferences == 0 && !isActivityChangingConfigurations) {
                    Log.d("AppLifecycle", "App moved to background.");
                    triggerLogout(activity.getApplicationContext());
                }
            }

            @Override
            public void onActivitySaveInstanceState(@NonNull Activity activity, @NonNull Bundle outState) {}

            @Override
            public void onActivityDestroyed(@NonNull Activity activity) {}
        });
    }

    private void triggerLogout(Context context) {
        SharedPreferences preferences = context.getSharedPreferences("user_prefs", Context.MODE_PRIVATE);
        String sessionToken = preferences.getString("session_token", null);

        if (sessionToken == null) {
            Log.d("Logout", "Session token is missing. No logout required.");
            return;
        }

        String logoutUrl = "http://192.168.1.65:8080/ukwaandabot_v1.1/logout?session_token=" + sessionToken;
        Log.d("LogoutURL", "Logout URL: " + logoutUrl);

        RequestQueue queue = Volley.newRequestQueue(context);
        StringRequest stringRequest = new StringRequest(Request.Method.GET, logoutUrl,
                response -> {
                    try {
                        JSONObject jsonResponse = new JSONObject(response);
                        String status = jsonResponse.getString("status");

                        if ("success".equals(status)) {
                            Log.d("Logout", "User successfully logged out.");
                            Intent intent = new Intent(context, OuthActivity.class);
                            intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
                            context.startActivity(intent);

                            SharedPreferences.Editor editor = preferences.edit();
                            editor.remove("session_token");
                            editor.apply();
                        } else {
                            Log.e("Logout", "Logout failed.");
                        }
                    } catch (JSONException e) {
                        Log.e("Logout", "Error parsing response: " + e.getMessage());
                    }
                },
                error -> Log.e("Logout", "Error logging out: " + error.getMessage()));

        queue.add(stringRequest);
    }
}
