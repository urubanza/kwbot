package com.example.kwrobot.ui.notifications;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.example.kwrobot.R;


public class notificationAdapter extends BaseAdapter {
    private int icons[] = {
            R.drawable.ic_warning_black_24dp,
            R.drawable.route,
            R.drawable.mobile
    };
    private int types[];
    private String titles[];
    private String Contents[];
    private Context c;


    public notificationAdapter(Context c, int type[], String titles[], String Contents[]){
        this.c = c;
        this.types = type;
        this.titles = titles;
        this.Contents = Contents;
    }
    @Override
    public int getCount() {
        return this.types.length;
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
        view = (LayoutInflater.from(this.c)).inflate(R.layout.simple_notification,null);
        ImageView notification_image =  view.findViewById(R.id.notification_image);
        TextView notification_text = view.findViewById(R.id.notification_text);
        TextView notification_title = view.findViewById(R.id.notification_title);
        notification_image.setImageResource(this.icons[this.types[i]]);
        notification_text.setText(this.Contents[i]);
        notification_title.setText(this.titles[i]);
        return view;
    }
}
