package com.example.kwrobot;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.view.animation.Animation;
import android.view.animation.AnimationUtils;
import android.widget.ImageView;

public class SpashActivity extends AppCompatActivity {
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_spash);
        ImageView main_log = findViewById(R.id.splash_log);
        Animation zoomIn = AnimationUtils.loadAnimation(this,R.anim.zoom_in_faster);
        main_log.setAnimation(zoomIn);
        Thread splash = new Thread(){
            public void run(){
                try {
                    sleep(3000);
                    GoHome();
                } catch (Exception e){

                }
            }
        };
        splash.start();
    }

    public void GoHome(){
        Intent intent = new Intent(this, MainActivity.class);
        startActivity(intent);
        overridePendingTransition(R.anim.slide_in_right, R.anim.slide_out_left);
        finish();
    }

    @Override
    public void finish(){
        super.finish();
    }
}
