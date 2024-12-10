package com.example.kwrobot.ui;

import android.annotation.SuppressLint;
import android.os.Bundle;
import android.widget.Button;
import android.widget.EditText;
import android.widget.Toast;
import androidx.appcompat.app.AppCompatActivity;
import com.example.kwrobot.R;
import org.json.JSONObject;
import java.io.OutputStreamWriter;
import java.net.HttpURLConnection;
import java.net.URL;

public class ValidateOtpActivity extends AppCompatActivity {

    private EditText otpInput;
    private Button validateButton;
    private String businessId, email;

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

    private void attemptValidateOtp() {
        String otp = otpInput.getText().toString();

        if (otp.isEmpty()) {
            showToast("Please enter the OTP.");
            return;
        }

        new Thread(() -> {
            try {
                URL url = new URL("http://192.168.1.65:8080/ukwaandabot_v1.1/CpyValidateOtp");
                HttpURLConnection conn = (HttpURLConnection) url.openConnection();
                conn.setRequestMethod("POST");
                conn.setRequestProperty("Content-Type", "application/x-www-form-urlencoded; utf-8");
                conn.setRequestProperty("Accept", "application/json");
                conn.setDoOutput(true);

                String urlEncodedData = "business_id=" + businessId + "&email=" + email + "&otp=" + otp;

                OutputStreamWriter writer = new OutputStreamWriter(conn.getOutputStream());
                writer.write(urlEncodedData);
                writer.flush();
                writer.close();

                int responseCode = conn.getResponseCode();

                if (responseCode == 200) {
                    // OTP validated successfully
                    runOnUiThread(() -> {
                        showToast("OTP validated successfully. Please reset your password.");
                        // Proceed to reset password activity (or directly call the UpdateKeyServlet)
                        // Intent intent = new Intent(ValidateOtpActivity.this, ResetPasswordActivity.class);
                        // startActivity(intent);
                    });
                } else {
                    runOnUiThread(() -> showToast("Invalid OTP. Please try again."));
                }
            } catch (Exception e) {
                e.printStackTrace();
                runOnUiThread(() -> showToast("An error occurred while validating OTP."));
            }
        }).start();
    }

    private void showToast(String message) {
        Toast.makeText(ValidateOtpActivity.this, message, Toast.LENGTH_SHORT).show();
    }
}
