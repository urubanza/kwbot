package com.example.kwrobot.ui.dashboard;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.example.kwrobot.R;

public class DashbordAdapter extends BaseAdapter {
    Context c;
    int Logos[];
    LayoutInflater inflater;
    String names[];
    public DashbordAdapter(Context cc, int logs[],String nams[]){
        this.c = cc;
        this.Logos = logs;
        this.names = nams;
        inflater = (LayoutInflater.from(cc));
    }

    @Override
    public int getCount() {

        return this.Logos.length;
    }

    @Override
    public Object getItem(int i) {

        return null;
    }

    @Override
    public long getItemId(int i) {

        return 0;
    }

    @Override
    public View getView(int i, View view, ViewGroup viewGroup) {
        view = inflater.inflate(R.layout.simple_settings,null);
        ImageView icon =  view.findViewById(R.id.icon);
        TextView name = view.findViewById(R.id.names);
        icon.setImageResource(Logos[i]);
        name.setText(names[i]);
        return view;
    }
}
