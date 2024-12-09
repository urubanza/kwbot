package com.example.kwrobot.ui;

import android.content.Intent;
import android.content.SharedPreferences;
import android.graphics.Color;
import android.graphics.drawable.Drawable;
import android.os.Bundle;
import android.util.Log;
import android.view.Gravity;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.kwrobot.MainActivity;
import com.example.kwrobot.R;

import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStream;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.stream.Collectors;

public class OuthActivity extends AppCompatActivity {

    private EditText businessIdInput, passwordInput;
    private Button loginButton;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_outh);

        businessIdInput = findViewById(R.id.business_id_input);
        passwordInput = findViewById(R.id.password_input);
        loginButton = findViewById(R.id.login_button);

        loginButton.setOnClickListener(v -> attemptLogin());
    }

    private void attemptLogin() {
        String businessId = businessIdInput.getText().toString();
        String password = passwordInput.getText().toString();

        if (businessId.isEmpty() || password.isEmpty()) {
            showCustomToast("Please fill in all fields", R.drawable.ic_error, Color.RED);
            return;
        }

        new Thread(() -> {
            try {
                URL url = new URL("http://192.168.1.65:8080/ukwaandabot_v1.1/login");
                HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setRequestProperty("Content-Type", "application/x-www-form-urlencoded; utf-8");
                conn.setRequestProperty("Accept", "application/json");
                conn.setDoOutput(true);

                String urlEncodedData = "business_id=" + businessId + "&pass_key=" + password;

                Log.d("PAYLOAD", "Payload: " + urlEncodedData);

                OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream());
                writer.write(urlEncodedData);
                writer.flush();
                writer.close();

                int responseCode = conn.getResponseCode();
                Log.d("HTTP_RESPONSE", "Response Code: " + responseCode);

                InputStream inputStream = conn.getErrorStream() != null ? conn.getErrorStream() : conn.getInputStream();
                String response;
                if (android.os.Build.VERSION.SDK_INT >= android.os.Build.VERSION_CODES.N) {
                    response = new BufferedReader(new InputStreamReader(inputStream))
                            .lines().collect(Collectors.joining("\n"));
                } else {
                    response = null;
                }

                Log.d("HTTP_RESPONSE_BODY", "Response Body: " + response);

                if (responseCode == 200) {
                    runOnUiThread(() -> {
                        try {
                            // Parse the session token from the response
                            JSONObject jsonResponse = new JSONObject(response);
                            String sessionToken = jsonResponse.getString("session_token");

                            // Save the session token in SharedPreferences
                            saveSessionToken(sessionToken);

                            showCustomToast("Login Success", R.drawable.baseline_check_circle_24, Color.GREEN);
                            navigateToMainActivity();
                        } catch (Exception e) {
                            Log.e("PARSE_ERROR", "Error parsing session token: " + e.getMessage());
                            showCustomToast("Error parsing server response", R.drawable.ic_error, Color.RED);
                        }
                    });
                } else {
                    runOnUiThread(() -> {
                        String errorMessage = parseErrorMessage(response);
                        showCustomToast(errorMessage, R.drawable.ic_error, Color.RED);
                    });
                }
            } catch (Exception e) {
                Log.e("HTTP_ERROR", "Error: " + e.getMessage(), e);
                runOnUiThread(() -> showCustomToast("An error occurred", R.drawable.ic_error, Color.RED));
            }
        }).start();
    }

    // Save session token to SharedPreferences
    private void saveSessionToken(String sessionToken) {
        SharedPreferences preferences = getSharedPreferences("user_prefs", MODE_PRIVATE);
        SharedPreferences.Editor editor = preferences.edit();
        editor.putString("session_token", sessionToken);
        editor.apply();

        Log.d("SESSION_TOKEN", "Session token saved: " + sessionToken);
    }


    private String parseErrorMessage(String response) {
        if (response == null) return "An unknown error occurred";
        if (response.contains("Invalid business ID or passkey"))
            return "Invalid ID or Password combination. Try again.";
        if (response.contains("Maximum number of users reached"))
            return "Membership limit reached. Please wait or upgrade.";
        if (response.contains("Account is inactive"))
            return "Account is inactive. Access denied.";
        return "Login failed. Please try again.";
    }

    private void showCustomToast(String message, int iconResId, int backgroundColor) {
        LayoutInflater inflater = getLayoutInflater();
        View layout = inflater.inflate(R.layout.custom_toast, findViewById(R.id.custom_toast_container));

        ImageView toastIcon = layout.findViewById(R.id.toast_icon);
        TextView toastText = layout.findViewById(R.id.toast_text);

        toastIcon.setImageResource(iconResId);
        toastText.setText(message);
        layout.setBackgroundColor(backgroundColor);

        Toast toast = new Toast(getApplicationContext());
        toast.setGravity(Gravity.TOP | Gravity.RIGHT, 50, 100);
        toast.setDuration(Toast.LENGTH_LONG);
        toast.setView(layout);
        toast.show();
    }

    // Method to navigate to MainActivity
    private void navigateToMainActivity() {
        Intent intent = new Intent(OuthActivity.this, MainActivity.class);
        startActivity(intent);
        overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left);
        finish();
    }
}
