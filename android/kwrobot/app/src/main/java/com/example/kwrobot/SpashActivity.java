package com.example.kwrobot;

import androidx.appcompat.app.AppCompatActivity;
import android.content.Intent;
import android.os.Bundle;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;

import com.example.kwrobot.ui.OuthActivity;

public class SpashActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_spash);

        ImageView mainLog = findViewById(R.id.splash_log);
        Animation zoomIn = AnimationUtils.loadAnimation(this, R.anim.zoom_in_faster);
        mainLog.setAnimation(zoomIn);

        Thread splash = new Thread() {
            public void run() {
                try {
                    sleep(3000); // Show splash for 3 seconds
                    goToLogin();
                } catch (Exception e) {
                    e.printStackTrace();
                }
            }
        };
        splash.start();
    }

    public void goToLogin() {
        Intent intent = new Intent(this, OuthActivity.class);
        startActivity(intent);
        overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left);
        finish();
    }

    @Override
    public void finish() {
        super.finish();
    }
}
