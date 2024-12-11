package com.example.kwrobot.ui;

import android.annotation.SuppressLint;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;
import android.widget.TextView;
import android.widget.Toast;

import androidx.appcompat.app.AppCompatActivity;

import com.example.kwrobot.R;

import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;

public class ForgotPasswordActivity extends AppCompatActivity {

    private EditText businessIdInput, emailInput;
    private Button submitButton;

    @SuppressLint("MissingInflatedId")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_forgot_password);

        // Initialize the views
        businessIdInput = findViewById(R.id.business_id_input);
        emailInput = findViewById(R.id.email_input);
        submitButton = findViewById(R.id.submit_button);

        // Set click listener for the submit button
        submitButton.setOnClickListener(v -> attemptToSendOtp());

        // Go back to the OuthActivity when the "Go back to login" text is clicked
        TextView goBackToLogin = findViewById(R.id.go_back_outh);
        goBackToLogin.setOnClickListener(v -> {
            Intent intent = new Intent(ForgotPasswordActivity.this, OuthActivity.class);
            startActivity(intent);
            finish();
        });
    }

    private void attemptToSendOtp() {
        // Get input values
        String businessId = businessIdInput.getText().toString();
        String email = emailInput.getText().toString();

        // Check if inputs are valid
        if (businessId.isEmpty() || email.isEmpty()) {
            showToast("Please fill in all fields");
            return;
        }

        // Start background thread for sending OTP
        new Thread(() -> {
            try {
                // Set up the URL for sending the OTP request
                URL url = new URL("http://192.168.1.65:8080/ukwaandabot_v1.1/CpyForgotPassword");
                HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setRequestProperty("Content-Type", "application/x-www-form-urlencoded; utf-8");
                conn.setRequestProperty("Accept", "application/json");
                conn.setDoOutput(true);

                // Prepare data to be sent
                String urlEncodedData = "business_id=" + businessId + "&email=" + email;

                // Send the request
                OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream());
                writer.write(urlEncodedData);
                writer.flush();
                writer.close();

                // Get the response code from the server
                int responseCode = conn.getResponseCode();

                // Handle server response
                if (responseCode == 200) {
                    // OTP sent successfully
                    runOnUiThread(() -> {
                        showToast("OTP sent to your email.");
                        // Proceed to OTP validation activity
                        Intent intent = new Intent(ForgotPasswordActivity.this, ValidateOtpActivity.class);
                        intent.putExtra("business_id", businessId);
                        intent.putExtra("email", email);
                        startActivity(intent);
                    });
                } else {
                    // Invalid response or error
                    runOnUiThread(() -> showToast("Invalid Business ID or Email"));
                }
            } catch (Exception e) {
                // Handle any exceptions during the network operation
                Log.e("HTTP_ERROR", "Error: " + e.getMessage(), e);
                runOnUiThread(() -> showToast("An error occurred while sending OTP."));
            }
        }).start();
    }

    private void showToast(String message) {
        // Show a toast message
        Toast.makeText(ForgotPasswordActivity.this, message, Toast.LENGTH_SHORT).show();
    }
}
