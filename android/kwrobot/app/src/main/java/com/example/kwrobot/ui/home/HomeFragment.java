package com.example.kwrobot.ui.home;

import android.annotation.SuppressLint;
import android.content.Context;
import android.content.Intent;
import android.content.SharedPreferences;
import android.os.Bundle;
import android.util.Log;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.PopupMenu;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;

import com.android.volley.Request;
import com.android.volley.RequestQueue;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.StringRequest;
import com.android.volley.toolbox.Volley;
import com.daimajia.slider.library.Animations.DescriptionAnimation;
import com.daimajia.slider.library.SliderLayout;
import com.daimajia.slider.library.SliderTypes.BaseSliderView;
import com.daimajia.slider.library.SliderTypes.TextSliderView;
import com.daimajia.slider.library.Tricks.ViewPagerEx;
import com.example.kwrobot.AcountActivity;
import com.example.kwrobot.R;
import com.example.kwrobot.ui.OuthActivity;
import com.github.clans.fab.FloatingActionButton;
import com.github.clans.fab.FloatingActionMenu;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.HashMap;
import java.util.Map;

public class HomeFragment extends Fragment implements BaseSliderView.OnSliderClickListener, ViewPagerEx.OnPageChangeListener {

    private Context context;
    private SliderLayout sliderLayout;
    private HashMap<String, String> hashFileMaps;
    private FloatingActionMenu theMenu;
    private FloatingActionButton button1, button2, button3;

    @Override
    public View onCreateView(@NonNull LayoutInflater inflater,
                             ViewGroup container, Bundle savedInstanceState) {
        View root = inflater.inflate(R.layout.fragment_home, container, false);

        // Initialize Floating Action Menu and Buttons
        theMenu = root.findViewById(R.id.theBottomMenu);
        button1 = root.findViewById(R.id.menu1);
        button2 = root.findViewById(R.id.menu2);
        button3 = root.findViewById(R.id.menu3);

        // Initialize Slider Layout
        hashFileMaps = new HashMap<>();
        sliderLayout = root.findViewById(R.id.sliders);

        // Add Images to the Slider
        hashFileMaps.put("Slider 1", "https://media.npr.org/assets/img/2020/07/13/gettyimages-1214305129_wide-607d1eae25e2a06b09975d923a906106da084f6b.jpg?s=1400");
        hashFileMaps.put("Slider 2", "https://www.afro.who.int/sites/default/files/2020-07/Robot%20Urumuri%20to%20support%20screening%20of%20temperature%20at%20Airport.jpg");
        hashFileMaps.put("Slider 3", "https://www.afro.who.int/sites/default/files/2020-07/Robot%20at%20Kigali%20International%20Airport.jpg");
        hashFileMaps.put("Slider 4", "https://i.guim.co.uk/img/media/1439a8439a4cbf544ed9d5c2d6d4afaea828c604/0_0_3152_2035/master/3152.jpg?width=445&quality=45&auto=format&fit=max&dpr=2&s=acc63db9fbac3c4640c3609d7c3fe78e");

        // Configure Slider Layout
        for (String name : hashFileMaps.keySet()) {
            TextSliderView textSliderView = new TextSliderView(context);
            textSliderView
                    .description(name)
                    .image(hashFileMaps.get(name))
                    .setScaleType(BaseSliderView.ScaleType.Fit)
                    .setOnSliderClickListener(this);

            textSliderView.bundle(new Bundle());
            textSliderView.getBundle().putString("extra", name);

            sliderLayout.addSlider(textSliderView);
        }

        sliderLayout.setPresetTransformer(SliderLayout.Transformer.Accordion);
        sliderLayout.setPresetIndicator(SliderLayout.PresetIndicators.Center_Bottom);
        sliderLayout.setCustomAnimation(new DescriptionAnimation());
        sliderLayout.setDuration(3000);
        sliderLayout.addOnPageChangeListener(this);

        // Set Floating Action Menu Item Listeners
        button1.setOnClickListener(view -> Toast.makeText(context, "Menu 1 clicked", Toast.LENGTH_LONG).show());
        button2.setOnClickListener(view -> Toast.makeText(context, "Menu 2 clicked", Toast.LENGTH_LONG).show());
        button3.setOnClickListener(view -> Toast.makeText(context, "Menu 3 clicked", Toast.LENGTH_LONG).show());

        // Set Up Profile Icon Dropdown Menu
        root.findViewById(R.id.imgSearch).setOnClickListener(this::showProfileMenu);

        return root;
    }

    // Display a Dropdown Menu for the Profile Icon
    private void showProfileMenu(View view) {
        PopupMenu popupMenu = new PopupMenu(context, view);
        popupMenu.getMenuInflater().inflate(R.menu.profile_menu, popupMenu.getMenu());

        popupMenu.setOnMenuItemClickListener(item -> {
            switch (item.getItemId()) {
                case R.id.menuProfile:
                    Intent intent = new Intent(context, AcountActivity.class);
                    startActivity(intent);
                    return true;
                case R.id.menuLogout:
                    logout();
                    return true;
                default:
                    return false;
            }

        });

        popupMenu.show();
    }

    // Logout Method to Destroy the Session
    private void logout() {
        SharedPreferences preferences = context.getSharedPreferences("user_prefs", Context.MODE_PRIVATE);
        String sessionToken = preferences.getString("session_token", null);

        Log.d("SessionToken", "Session token retrieved: " + sessionToken);

        if (sessionToken != null) {
            String logoutUrl = "http://192.168.1.65:8080/ukwaandabot_v1.1/logout?session_token=" + sessionToken;

            Log.d("LogoutURL", "Logout URL: " + logoutUrl);

            RequestQueue queue = Volley.newRequestQueue(context);
            StringRequest stringRequest = new StringRequest(Request.Method.GET, logoutUrl,
                    response -> {
                        try {
                            JSONObject jsonResponse = new JSONObject(response);
                            String status = jsonResponse.getString("status");

                            if ("success".equals(status)) {
                                Toast.makeText(context, "Logged out successfully", Toast.LENGTH_SHORT).show();
                                Intent intent = new Intent(context, OuthActivity.class);
                                startActivity(intent);

                                SharedPreferences.Editor editor = preferences.edit();
                                editor.remove("session_token");
                                editor.apply();
                            } else {
                                Toast.makeText(context, "Logout failed", Toast.LENGTH_SHORT).show();
                            }
                        } catch (JSONException e) {
                            e.printStackTrace();
                            Toast.makeText(context, "Error parsing response", Toast.LENGTH_SHORT).show();
                        }
                    },
                    error -> Toast.makeText(context, "Error logging out", Toast.LENGTH_SHORT).show());

            queue.add(stringRequest);
        } else {
            Toast.makeText(context, "Session token is missing", Toast.LENGTH_SHORT).show();
        }
    }

    @Override
    public void onAttach(@NonNull Context context) {
        super.onAttach(context);
        this.context = context;
    }

    @Override
    public void onStop() {
        sliderLayout.stopAutoCycle();
        super.onStop();
    }

    @Override
    public void onSliderClick(BaseSliderView slider) {
        Toast.makeText(context, slider.getBundle().get("extra") + "", Toast.LENGTH_LONG).show();
    }

    @Override
    public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {}

    @Override
    public void onPageSelected(int position) {}

    @Override
    public void onPageScrollStateChanged(int state) {}
}
