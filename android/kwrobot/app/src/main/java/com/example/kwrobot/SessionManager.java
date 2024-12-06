package com.example.kwrobot;

import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Handler;
import android.util.Log;

import com.example.kwrobot.ui.OuthActivity;

import java.io.OutputStream;
import java.net.HttpURLConnection;
import java.net.URL;

public class SessionManager {
    private static final String PREF_NAME = "UserSession";
    private static final String KEY_SESSION_TOKEN = "session_token";
    private static final String KEY_USER_ID = "user_id";
    private static final String LOGOUT_ENDPOINT = "http://192.168.1.65:8080/logout";

    private static SessionManager instance;
    private final SharedPreferences preferences;
    private final Handler networkHandler = new Handler();

    private SessionManager(Context context) {
        preferences = context.getSharedPreferences(PREF_NAME, Context.MODE_PRIVATE);
    }

    public static synchronized SessionManager getInstance() {
        if (instance == null) {
            instance = new SessionManager(MainApplication.getAppContext());
        }
        return instance;
    }

    public void saveSessionToken(String token, String userId) {
        preferences.edit()
                .putString(KEY_SESSION_TOKEN, token)
                .putString(KEY_USER_ID, userId)
                .apply();
    }

    public String getSessionToken() {
        return preferences.getString(KEY_SESSION_TOKEN, null);
    }

    public String getUserId() {
        return preferences.getString(KEY_USER_ID, null);
    }

    public boolean isLoggedIn() {
        return getSessionToken() != null;
    }

    public void logoutUser() {
        String userId = getUserId();

        // Notify server about logout
        if (userId != null) {
            networkHandler.post(() -> notifyServerLogout(userId));
        }

        // Clear session locally
        preferences.edit().clear().apply();

        // Redirect to login screen
        Context context = MainApplication.getAppContext();
        Intent intent = new Intent(context, OuthActivity.class);
        intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_CLEAR_TASK);
        context.startActivity(intent);
    }

    private void notifyServerLogout(String userId) {
        try {
            URL url = new URL(LOGOUT_ENDPOINT);
            HttpURLConnection connection = (HttpURLConnection) url.openConnection();
            connection.setRequestMethod("POST");
            connection.setRequestProperty("Content-Type", "application/json; utf-8");
            connection.setRequestProperty("Accept", "application/json");
            connection.setDoOutput(true);

            // JSON body to send
            String requestBody = "{\"user_id\":\"" + userId + "\",\"status\":\"offline\"}";

            try (OutputStream os = connection.getOutputStream()) {
                byte[] input = requestBody.getBytes("utf-8");
                os.write(input, 0, input.length);
            }

            int responseCode = connection.getResponseCode();
            Log.d("SessionManager", "Logout request response: " + responseCode);
        } catch (Exception e) {
            Log.e("SessionManager", "Failed to notify server about logout: " + e.getMessage(), e);
        }
    }
}
