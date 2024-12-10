package com.example.kwrobot.ui;

import android.annotation.SuppressLint;
import android.app.AlertDialog;
import android.content.Intent;
import android.os.Build;
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

import androidx.annotation.RequiresApi;
import androidx.appcompat.app.AppCompatActivity;

import com.example.kwrobot.R;

import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.InputStreamReader;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;
import java.util.stream.Collectors;

public class ValidateOtpActivity extends AppCompatActivity {

    private EditText otpInput;
    private Button validateButton;
    private String businessId, email;

    @RequiresApi(api = Build.VERSION_CODES.N)
    @SuppressLint("MissingInflatedId")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_validate_otp);

        otpInput = findViewById(R.id.otp_input);
        validateButton = findViewById(R.id.validate_button);

        // Get businessId and email passed from ForgotPasswordActivity
        businessId = getIntent().getStringExtra("business_id");
        email = getIntent().getStringExtra("email");

        validateButton.setOnClickListener(v -> attemptValidateOtp());
    }

    @RequiresApi(api = Build.VERSION_CODES.N)
    private void attemptValidateOtp() {
        String otp = otpInput.getText().toString().trim();

        if (otp.isEmpty()) {
            showCustomToast("Please enter the OTP.", false);
            return;
        }

        new Thread(() -> {
            HttpURLConnection conn = null;
            try {
                URL url = new URL("http://192.168.1.65:8080/ukwaandabot_v1.1/CpyValidateOtp");
                conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setRequestProperty("Content-Type", "application/x-www-form-urlencoded; utf-8");
                conn.setRequestProperty("Accept", "application/json");
                conn.setDoOutput(true);

                // Form the URL-encoded data
                String urlEncodedData = "business_id=" + businessId + "&email=" + email + "&otp=" + otp;

                // Write data to the output stream
                try (OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream())) {
                    writer.write(urlEncodedData);
                    writer.flush();
                }

                // Read the server response
                int responseCode = conn.getResponseCode();
                if (responseCode == 200) {
                    String responseMessage = new BufferedReader(new InputStreamReader(conn.getInputStream()))
                            .lines().collect(Collectors.joining("\n"));
                    JSONObject jsonResponse = new JSONObject(responseMessage);

                    String status = jsonResponse.optString("status", "unknown");
                    String message = jsonResponse.optString("message", "No additional details provided.");

                    if ("success".equalsIgnoreCase(status)) {
                        runOnUiThread(() -> {
                            showCustomToast("OTP validated successfully!", true);
                            hitUpdateKeyServlet(); // Trigger the passkey update
                        });
                    } else {
                        runOnUiThread(() -> showCustomToast("Invalid OTP. " + message, false));
                    }
                } else {
                    String errorMessage = conn.getErrorStream() != null
                            ? new BufferedReader(new InputStreamReader(conn.getErrorStream()))
                            .lines().collect(Collectors.joining("\n"))
                            : "Server error occurred.";
                    runOnUiThread(() -> showCustomToast("Invalid OTP. " + errorMessage, false));
                }
            } catch (Exception e) {
                runOnUiThread(() -> showCustomToast("An error occurred while validating OTP.", false));
            } finally {
                if (conn != null) conn.disconnect();
            }
        }).start();
    }

    @RequiresApi(api = Build.VERSION_CODES.N)
    private void hitUpdateKeyServlet() {
        new Thread(() -> {
            HttpURLConnection conn = null;
            try {
                URL url = new URL("http://192.168.1.65:8080/ukwaandabot_v1.1/updateKey");
                conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setRequestProperty("Content-Type", "application/x-www-form-urlencoded; utf-8");
                conn.setRequestProperty("Accept", "application/json");
                conn.setDoOutput(true);

                // Include 'id' in the POST data
                String urlEncodedData = "id=" + businessId;

                try (OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream())) {
                    writer.write(urlEncodedData);
                    writer.flush();
                }

                int responseCode = conn.getResponseCode();
                if (responseCode == 200) {
                    String responseMessage = new BufferedReader(new InputStreamReader(conn.getInputStream()))
                            .lines().collect(Collectors.joining("\n"));
                    JSONObject jsonResponse = new JSONObject(responseMessage);

                    String status = jsonResponse.optString("status", "unknown");
                    String message = jsonResponse.optString("message", "No additional details provided.");

                    if ("success".equalsIgnoreCase(status)) {
                        runOnUiThread(() -> showModalDialog("Success", "Passkey has been updated. Email has been sent.", true));
                    } else {
                        runOnUiThread(() -> showModalDialog("Error", "Failed to update passkey: " + message, false));
                    }
                } else {
                    String errorMessage = conn.getErrorStream() != null
                            ? new BufferedReader(new InputStreamReader(conn.getErrorStream()))
                            .lines().collect(Collectors.joining("\n"))
                            : "Unknown error occurred.";
                    runOnUiThread(() -> showModalDialog("Error", "Error during updateKey: " + errorMessage, false));
                }
            } catch (Exception e) {
                runOnUiThread(() -> showModalDialog("Error", "An error occurred while updating the passkey.", false));
            } finally {
                if (conn != null) conn.disconnect();
            }
        }).start();
    }

    private void showCustomToast(String message, boolean isSuccess) {
        LayoutInflater inflater = getLayoutInflater();
        View layout = inflater.inflate(R.layout.custom_toast, null);

        ImageView icon = layout.findViewById(R.id.toast_icon);
        TextView text = layout.findViewById(R.id.toast_message);

        text.setText(message);
        icon.setImageResource(isSuccess ? R.drawable.baseline_check_circle_24 : R.drawable.ic_error);

        Toast toast = new Toast(getApplicationContext());
        toast.setGravity(Gravity.TOP | Gravity.END, 50, 100);
        toast.setDuration(Toast.LENGTH_LONG);
        toast.setView(layout);
        toast.show();
    }

    private void showModalDialog(String title, String message, boolean shouldRedirect) {
        AlertDialog.Builder builder = new AlertDialog.Builder(this);
        builder.setTitle(title)
                .setMessage(message)
                .setPositiveButton("OK", (dialog, which) -> {
                    dialog.dismiss();
                    if (shouldRedirect) {
                        Intent intent = new Intent(ValidateOtpActivity.this, OuthActivity.class);
                        startActivity(intent);
                        finish();
                    }
                })
                .setCancelable(false)
                .show();
    }
}
