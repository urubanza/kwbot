package com.example.kwrobot.ui.dashboard;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.GridView;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.ViewModelProviders;


import com.example.kwrobot.AcountActivity;
import com.example.kwrobot.AnalyticsActivity;
import com.example.kwrobot.ControlPanel;
import com.example.kwrobot.DevicesActivity;
import com.example.kwrobot.MainActivity;
import com.example.kwrobot.PathsActivity;
import com.example.kwrobot.R;
import com.example.kwrobot.Robots;
import com.example.kwrobot.SettingsActivity;

public class DashboardFragment extends Fragment {

    private DashboardViewModel dashboardViewModel;
    private Context cc;
    GridView containerNewGrids;
    int logos[] = {
            R.drawable.mobile,
            R.drawable.route,
            R.drawable.devices,
            R.drawable.settings,
            R.drawable.account,
            R.drawable.equalizer,
            R.drawable.dashboard,
            R.drawable.notification
    };
    String[] items = {
            "Robots",
            "Paths",
            "Devices",
            "settings",
            "Accounts",
            "Controls",
            "Analytics",
            "Notifications"
    };
    public DashboardFragment(){

    }

    @Override
    public void onAttach(Context context){
        super.onAttach(context);
        cc = context;
    }

    public View onCreateView(@NonNull LayoutInflater inflater,
                             ViewGroup container, Bundle savedInstanceState) {
        dashboardViewModel =
                ViewModelProviders.of(this).get(DashboardViewModel.class);
        View root = inflater.inflate(R.layout.new_home, container, false);
        containerNewGrids = root.findViewById(R.id.containerNewGrids);
        DashbordAdapter customA = new DashbordAdapter(inflater.getContext(),logos,items);
        containerNewGrids.setAdapter(customA);

        containerNewGrids.setOnItemClickListener(new AdapterView.OnItemClickListener() {
            @Override
            public void onItemClick(AdapterView<?> adapterView, View view, int i, long l) {
                GoTo(i);
            }
        });
        return root;
    }

    public void GoTo(int i){
        switch (i){
            case 0:{
                start(Robots.class);
                //close();
                break;
            }
            case 1:{
                start(PathsActivity.class);
                //close();
                break;
            }
            case 2:{
                start(DevicesActivity.class);
                //close();
                break;
            }
            case 3:{
                start(SettingsActivity.class);
                break;
            }
            case 4:{
                start(AcountActivity.class);
                close();
                break;
            }
            case 5:{
                start(ControlPanel.class);
                //close();
                break;
            }
            case 6:{
                start(AnalyticsActivity.class);
                break;
            }
            default:{
                break;
            }
        }
    }

    public void close(){
        Activity act = (MainActivity)getActivity();
        act.finish();
    }

    public void start(Class<?> c){
        startActivity(new Intent(this.cc,c));
    }

}
