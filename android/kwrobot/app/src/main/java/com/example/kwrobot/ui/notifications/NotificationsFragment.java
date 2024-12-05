package com.example.kwrobot.ui.notifications;

import android.content.Context;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ListView;
import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import androidx.lifecycle.ViewModelProviders;

import com.example.kwrobot.R;

public class NotificationsFragment extends Fragment {
    private Context cc;
    int types[] = {0,1,2};
    String titles[] = {"Warning","Robot Connected","Path saved"};
    String text[] = {"the Unnamed Robot Battery is low try to charge it to prevent some failure",
                     "The Unnamed Robot is connected successfully",
                     "The path named kichen to bar has been saved successfully"
    };
    private NotificationsViewModel notificationsViewModel;
    @Override
    public void onAttach(Context context){
        super.onAttach(context);
        cc = context;
    }
    public View onCreateView(@NonNull LayoutInflater inflater,
                             ViewGroup container, Bundle savedInstanceState) {
        notificationsViewModel =
                ViewModelProviders.of(this).get(NotificationsViewModel.class);
        View root = inflater.inflate(R.layout.fragment_notifications, container, false);
        ListView Notification_container = root.findViewById(R.id.notification_list);
        notificationAdapter customA = new notificationAdapter(inflater.getContext(),types,titles,text);
        Notification_container.setAdapter(customA);
        return root;
    }
}
