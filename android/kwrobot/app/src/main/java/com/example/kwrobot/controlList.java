package com.example.kwrobot;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.ScrollView;
import android.widget.TextView;
import android.widget.Toast;

import java.util.ArrayList;

public class controlList extends BaseAdapter {

    // Declare Variables
    Context context;
    String[] name;
    int[] image;
    int[] position;
    String[] desc;
    LayoutInflater inflater;

//    private LinearLayout JoyStick,lineDrawer,GPS,Accelerometer;
    private ScrollView SelectAll;

//    public void setControls(LinearLayout joyStick, LinearLayout lineDrawer, LinearLayout gps, LinearLayout accelerometer){
//        this.JoyStick = joyStick;
//        this.lineDrawer = lineDrawer;
//        this.GPS = gps;
//        this.Accelerometer = accelerometer;
//    }

    public void setContainer(ScrollView cont){
        this.SelectAll = cont;
    }

    public controlList(Context context, String[] name, int[] image, int[] position, String[] Desc) {
        this.context = context;
        this.name = name;
        this.image = image;
        this.position = position;
        this.desc = Desc;
    }

    public controlList(Context context, ArrayList<Controllers> controllers){
        this.context = context;
        PIP_Array pip_array = new PIP_Array();

        for(int i = 0; i < controllers.size(); i++){
            this.name = pip_array.add(i,this.name,controllers.get(i).getName());
            this.image = pip_array.add(i,this.image,controllers.get(i).getImage());
            this.position = pip_array.add(i,this.position,controllers.get(i).getPosition());
            this.desc = pip_array.add(i,this.desc,controllers.get(i).getDescription());
        }
    }

    @Override
    public int getCount() {
        return this.name.length;
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
        TextView name, desc;
        ImageView image;
        inflater = (LayoutInflater) context
                .getSystemService(Context.LAYOUT_INFLATER_SERVICE);
        View itemView = inflater.inflate(R.layout.controlls, viewGroup, false);

        name = itemView.findViewById(R.id.name_controls);
        image = itemView.findViewById(R.id.theImage);
        desc = itemView.findViewById(R.id.description);
        name.setText(this.name[i]);
        desc.setText(this.desc[i]);
        image.setImageResource(this.image[i]);
        final int index = i;
        return itemView;
    }
//    public void setClick(int position){
//        if(position>4) this.setClick(4);
//        switch (position){
//            case 0:{
//                this.JoyStick.setVisibility(View.VISIBLE);
//                this.lineDrawer.setVisibility(View.GONE);
//                this.GPS.setVisibility(View.GONE);
//                this.Accelerometer.setVisibility(View.GONE);
//                this.SelectAll.setVisibility(View.GONE);
//                break;
//            }
//
//            case 1:{
//                this.JoyStick.setVisibility(View.GONE);
//                this.lineDrawer.setVisibility(View.GONE);
//                this.GPS.setVisibility(View.GONE);
//                this.Accelerometer.setVisibility(View.VISIBLE);
//                this.SelectAll.setVisibility(View.GONE);
//                break;
//            }
//
//            case 2:{
//                this.JoyStick.setVisibility(View.GONE);
//                this.lineDrawer.setVisibility(View.GONE);
//                this.GPS.setVisibility(View.VISIBLE);
//                this.Accelerometer.setVisibility(View.GONE);
//                this.SelectAll.setVisibility(View.GONE);
//                break;
//            }
//
//            case 3:{
//                this.JoyStick.setVisibility(View.GONE);
//                this.lineDrawer.setVisibility(View.VISIBLE);
//                this.GPS.setVisibility(View.GONE);
//                this.Accelerometer.setVisibility(View.GONE);
//                this.SelectAll.setVisibility(View.GONE);
//                break;
//            }
//            case 4:{
//                this.JoyStick.setVisibility(View.GONE);
//                this.lineDrawer.setVisibility(View.GONE);
//                this.GPS.setVisibility(View.GONE);
//                this.Accelerometer.setVisibility(View.GONE);
//                this.SelectAll.setVisibility(View.VISIBLE);
//            }
//            default:{
//                break;
//            }
//        }
//    }
}
