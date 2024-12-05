package com.example.kwrobot;

import androidx.appcompat.app.AppCompatActivity;
import kwbotControler.CustomView.lineDraw;
import android.os.Bundle;

public class lineDrawer extends AppCompatActivity {
    private lineDraw dl;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_line_drawer);
        dl = findViewById(R.id.dl);
    }
}
