package com.example.kwrobot.ui;

import android.annotation.SuppressLint;
import android.app.ProgressDialog;
import android.content.Intent;
import android.os.Bundle;
import android.util.Log;
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
    private ProgressDialog progressDialog;

    @SuppressLint("MissingInflatedId")
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_forgot_password);

        // Initialize the views
        businessIdInput = findViewById(R.id.business_id_input);
        emailInput = findViewById(R.id.email_input);
        submitButton = findViewById(R.id.submit_button);

        // Initialize the ProgressDialog
        progressDialog = new ProgressDialog(this);
        progressDialog.setMessage("Please wait...");
        progressDialog.setCancelable(false);

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
        String businessId = businessIdInput.getText().toString().trim();
        String email = emailInput.getText().toString().trim();

        // Check if inputs are valid
        if (businessId.isEmpty() || email.isEmpty()) {
            showToast("Please fill in all fields");
            return;
        }

        // Show ProgressDialog
        progressDialog.show();

        // Start background thread for sending OTP
        new Thread(() -> {
            HttpURLConnection conn = null;
            try {
                // Set up the URL for sending the OTP request
                URL url = new URL("http://192.168.1.65:8080/ukwaandabot_v1.1/CpyForgotPassword");
                conn = (HttpURLConnection) url.openConnection();
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

                // Get the response code
                int responseCode = conn.getResponseCode();

                runOnUiThread(() -> {
                    progressDialog.dismiss(); // Hide ProgressDialog
                    if (responseCode == 200) {
                        // OTP sent successfully
                        showToast("OTP sent to your email.");
                        Intent intent = new Intent(ForgotPasswordActivity.this, ValidateOtpActivity.class);
                        intent.putExtra("business_id", businessId);
                        intent.putExtra("email", email);
                        startActivity(intent);
                        finish();
                    } else {
                        // Invalid response
                        showToast("Invalid Business ID or Email. Please try again.");
                    }
                });
            } catch (Exception e) {
                Log.e("ForgotPasswordError", "Error occurred: " + e.getMessage(), e);
                runOnUiThread(() -> {
                    progressDialog.dismiss(); // Hide ProgressDialog
                    showToast("An error occurred. Please check your network and try again.");
                });
            } finally {
                if (conn != null) {
                    conn.disconnect();
                }
            }
        }).start();
    }

    private void showToast(String message) {
        Toast.makeText(ForgotPasswordActivity.this, message, Toast.LENGTH_SHORT).show();
    }
}
