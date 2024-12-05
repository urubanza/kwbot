package com.example.kwrobot.ui.home;

import android.annotation.SuppressLint;
import android.content.Context;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.Toast;

import androidx.annotation.NonNull;
import androidx.fragment.app.Fragment;
import com.example.kwrobot.R;
import com.daimajia.slider.library.Animations.DescriptionAnimation;
import com.daimajia.slider.library.SliderTypes.BaseSliderView;
import com.daimajia.slider.library.SliderTypes.TextSliderView;
import com.daimajia.slider.library.SliderLayout;
import com.daimajia.slider.library.Tricks.ViewPagerEx;
import com.github.clans.fab.FloatingActionButton;
import com.github.clans.fab.FloatingActionMenu;
import java.util.HashMap;

public class HomeFragment extends Fragment implements BaseSliderView.OnSliderClickListener,ViewPagerEx.OnPageChangeListener {
    Context context;
    SliderLayout sliderLayout;
    HashMap<String,String> hash_file_maps;
    FloatingActionMenu theMenu;
    FloatingActionButton button1,button2,button3;



    public View onCreateView(@NonNull LayoutInflater inflater,
                             ViewGroup container, Bundle savedInstanceState) {
        View root = inflater.inflate(R.layout.fragment_home, container, false);
        theMenu = root.findViewById(R.id.theBottomMenu);
        button1 = root.findViewById(R.id.menu1);
        button2 = root.findViewById(R.id.menu2);
        button3 = root.findViewById(R.id.menu3);

        hash_file_maps = new HashMap<String, String>();
        sliderLayout = root.findViewById(R.id.sliders);
        hash_file_maps.put("Slider1","https://media.npr.org/assets/img/2020/07/13/gettyimages-1214305129_wide-607d1eae25e2a06b09975d923a906106da084f6b.jpg?s=1400");
        hash_file_maps.put("Slider2","https://www.afro.who.int/sites/default/files/2020-07/Robot%20Urumuri%20to%20support%20screening%20of%20temperature%20at%20Airport.jpg");
        hash_file_maps.put("Sliders3","https://www.afro.who.int/sites/default/files/2020-07/Robot%20at%20Kigali%20International%20Airport.jpg");
        hash_file_maps.put("Sliders4","https://i.guim.co.uk/img/media/1439a8439a4cbf544ed9d5c2d6d4afaea828c604/0_0_3152_2035/master/3152.jpg?width=445&quality=45&auto=format&fit=max&dpr=2&s=acc63db9fbac3c4640c3609d7c3fe78e");

        for(String name : hash_file_maps.keySet()){
            TextSliderView textSliderView = new TextSliderView(this.context);
            textSliderView
                    .description(name)
                    .image(hash_file_maps.get(name))
                    .setScaleType(BaseSliderView.ScaleType.Fit)
                    .setOnSliderClickListener(this);
            textSliderView.bundle(new Bundle());
            textSliderView.getBundle().putString("extra",name);
            sliderLayout.addSlider(textSliderView);
        }
        sliderLayout.setPresetTransformer(SliderLayout.Transformer.Accordion);
        sliderLayout.setPresetIndicator(SliderLayout.PresetIndicators.Center_Bottom);
        sliderLayout.setCustomAnimation(new DescriptionAnimation());
        sliderLayout.setDuration(3000);
        sliderLayout.addOnPageChangeListener(this);

        button1.setOnClickListener(view -> {
            Toast.makeText(context,"Menu 1 clicked",Toast.LENGTH_LONG).show();
        });
        button2.setOnClickListener(view -> {
            Toast.makeText(context,"Menu 2 clicked",Toast.LENGTH_LONG).show();
        });

        button3.setOnClickListener(view -> {
            Toast.makeText(context,"Menu 3 clicked",Toast.LENGTH_LONG).show();
        });

        return root;
    }

    @SuppressLint("ValidFragment")
    public HomeFragment(){

    }

    @Override
    public void onAttach(Context context){
        super.onAttach(context);
        this.context = context;
    }

    @Override
    public void onStop(){
        sliderLayout.stopAutoCycle();
        super.onStop();
    }

    @Override
    public void onSliderClick(BaseSliderView slider) {
        Toast.makeText(this.context,slider.getBundle().get("extra")+"",Toast.LENGTH_LONG).show();
    }

    @Override
    public void onPageScrolled(int position, float positionOffset, int positionOffsetPixels) {

    }

    @Override
    public void onPageSelected(int position) {

    }

    @Override
    public void onPageScrollStateChanged(int state) {

    }
}
