package com.example.kwrobot;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.os.Bundle;
import android.widget.Toast;

public class AcountActivity extends AppCompatActivity {

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_acount);
        findViewById(R.id.backButton).setOnClickListener(view -> {
            ShowMessage("Back home");
            GoHome();
        });
    }

    private void ShowMessage(String yes) {
        Toast.makeText(this,yes,Toast.LENGTH_LONG).show();
    }

    private void GoHome(){
        startActivity(new Intent(this,MainActivity.class));
        finish();
    }


    public static class ActivityOuth {
    }
}
