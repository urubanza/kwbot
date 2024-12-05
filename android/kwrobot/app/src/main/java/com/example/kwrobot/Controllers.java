package com.example.kwrobot;

public class Controllers {
    private String name;
    private int Image;
    private int position;
    private String Description;

    public int getImage(){
        return Image;
    }
    public String getName(){
        return name;
    }

    public Controllers setName(String name) {
        this.name = name;
        return this;
    }
    public Controllers setImage(int Image){
        this.Image = Image;
        return this;
    }
    public int getPosition(){
        return this.position;
    }
    public Controllers setPosition(int p){
        this.position = p;
        return this;
    }

    public Controllers setDescription(String ds){
        this.Description = ds;
        return this;
    }
    public String getDescription(){
        return this.Description;
    }
}
