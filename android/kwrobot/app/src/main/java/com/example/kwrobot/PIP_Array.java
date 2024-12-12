package com.example.kwrobot;

import android.database.Cursor;
import android.util.Log;

import org.json.JSONArray;
import org.json.JSONException;

public class PIP_Array {
    public String AllValues[][] = {{"none"}};
    public int Dimension[] = {0,0};
    public String Dimension_array;
    public JSONArray jsons;
    public String reversed[][] = {{"none"}};
    public int size;
    public String keys[] = {"none"};
    public String AV[][] = {{"none"}};
    public String RV[][] = {{"none"}};
    public JSONArray JS;
    public String message = "No thing wrong";

    PIP_Array(){
        return;
    }

    PIP_Array(String[][] AV){
        this.AllValues = this.setAllValues(AV);
        this.AV = this.setAllValues(AV);
        this.RV = this.rever();
        this.size = AV.length;
        if(this.size==1){
            if(this.AV[0].length==1){
                if(this.AV[0][0].equals("none")){
                    this.size = 0;
                    this.Dimension[0] = 0;
                    this.Dimension[1] = this.size;
                    this.Dimension_array = "["+this.size+"]"+"["+Dimension[0]+"]";
                } else {
                    this.Dimension[0] = AV[0].length;
                    this.Dimension[1] = this.size;
                    this.Dimension_array = "["+this.size+"]"+"["+Dimension[0]+"]";
                }
            } else {
                this.Dimension[0] = AV[0].length;
                this.Dimension[1] = this.size;
                this.Dimension_array = "["+this.size+"]"+"["+Dimension[0]+"]";
            }
        } else{
            this.Dimension[0] = AV[0].length;
            this.Dimension[1] = this.size;
            this.Dimension_array = "["+this.size+"]"+"["+Dimension[0]+"]";
        }

    }

    PIP_Array(Cursor AV){
        this.size = AV.getCount();
        this.Dimension[0] = AV.getColumnCount();
        this.Dimension[1] = this.size;
        this.Dimension_array = "["+this.size+"]"+"["+this.Dimension[0]+"]";
        if(this.size>0){
            this.AllValues[0][0] = AV.getString(0);
            for(int i = 1; i<this.Dimension[0];i++){
                this.AllValues[0] = this.add(i,this.AllValues[0],AV.getString(i));
            }
            int j = 1;
            while(AV.moveToNext()){
                for(int i = 0; i< this.Dimension[0];i++){
                    this.AllValues = this.add(j,this.AllValues,this.add(i,this.AllValues[j-1],AV.getString(i)));
                }
                j++;
            }
        }
        this.AV = this.setAllValues(this.AllValues);
        this.RV = this.rever();
    }

    // to call this function it is a must to call the setKeys function before otherwise it will return no thing
    PIP_Array JSON(JSONArray AV){
        this.jsons = AV;
        this.JS = AV;
        String Arr[][] = {{"none"}};
        if(AV.length()>0){
            try{
                Arr[0][0] = AV.getJSONObject(0).getString(this.keys[0]);
            } catch (JSONException err){
                Log.d("BD_JSON:", err.getMessage()+this.keys.length);
            }
            for(int j=1; j<this.keys.length;j++){
                try{
                    Arr[0] = this.add(j,Arr[0],AV.getJSONObject(0).getString(this.keys[j]));
                } catch (JSONException err){
                    Log.d("BD_JSON:",err.getMessage());
                }
            }
        }
        for(int i=1; i<AV.length();i++) {
            String arr[] = new String[0];
            for(int j=0;j<this.keys.length;j++){
                try{
                    arr = this.add(Arr[i-1],AV.getJSONObject(i).getString(this.keys[j]));
                } catch (JSONException e){
                    Log.d("BD_JSON", e.getMessage());
                }
            }
            Arr = this.add(i,Arr,arr);
        }

        return new PIP_Array(Arr);
    }

    public PIP_Array reverse(){
        return new PIP_Array(this.rever());
    }
    public PIP_Array setKeys(String[] keys) {
        this.keys[0] = keys[0];
        for(int i=1;i<keys.length;i++){
            this.keys = this.add(i,keys,keys[i]);
        }
        return this;
    }
    private String[][] rever(){
        String rets [][] = this.AV;
        if(this.size>0){
            int j = 0;
            for(int i = (this.AV.length-1); i>= 0; i--){
                rets[j] = this.AV[i];
                j++;
            }
        }
        return rets;
    }
    // Function to add x in arr
    public static int[] add(int n, int arr[], int x){
        int i;
        // create a new array of size n+1
        int newarr[] = new int[n + 1];
        // insert the elements from
        // the old array into the new array
        // insert all elements till n
        // then insert x at n+1
        for (i = 0; i < n; i++)
            newarr[i] = arr[i];
        newarr[n] = x;
        return newarr;
    }
    public static String[] add(int n, String arr[], String x){
        int i;
        String newarr[] = new String[n + 1];
        for (i = 0; i < n; i++)
            newarr[i] = arr[i];
        newarr[n] = x;
        return newarr;
    }
    public static String[] add(String arr[], String x){
        int i, n = arr.length;
        String newarr[] = new String[n + 1];
        for (i = 0; i < n; i++)
            newarr[i] = arr[i];
        newarr[n] = x;
        return newarr;
    }
    public static String[][] add(int n, String arr[][], String x[]){
        String[][] new_arr = new String[n+1][x.length];
        for(int i = 0; i<n; i++){
            new_arr[i] = arr[i];
        }
        new_arr[n] = x;
        return new_arr;
    }
    public  String[][] setAllValues(String[][] allValues) {
        if(allValues.length>0){
            this.AllValues[0][0] = allValues[0][0];
        }
        for(int i =1; i< allValues.length;i++){
            this.AllValues = this.add(i,this.AllValues,allValues[i]);
            for(int j = 1; j < allValues[0].length; j++){
                this.AllValues[i] = this.add(j,this.AllValues[i],allValues[i][j]);
            }
        }
        return this.AllValues;
    }
    public String JS(int index1 , int index2){
        if(this.size<index1){
            this.message = "Out of Bound";
            return this.message;
        } else {
            this.message = "the index was found success";
            return this.AV[index1][index2];
        }
    }


    // to call this function it is a must to call the setKeys function before otherwise it will return no thing
    public String JS(int index, String name){
        int position = 0;
        this.message = "the index ("+name+") was not found";
        for(int i = 0; i<this.keys.length; i++){
            if(this.keys[i].equals(name)){
                position = i;
                this.message = "the index was found success";
                break;
            }
        }
        if(this.size<index){
            this.message = "Out of Bound";
            return this.message;
        } else {
            return this.AV[index][position];
        }
    }

    // to call this function it is a must to call the setKeys function before otherwise it will return no thing
    public PIP_Array gets(String index, String value, String Type){
        String rets[][] = {{"none"}};
        if(this.size>0){
            if(this.JS(0,0).equals("-1")){
                return this;
            } else {
                int jj = 0;
                for(int i = 0; i<this.size; i++){
                    if(!Type.equals("REMOVE")) {
                        if (this.JS(i, index).equals(value)) {
                            String[] singleA = {};
                            for (int j = 0; j < this.keys.length; j++) {
                                singleA = this.add(j, singleA, this.JS(i, this.keys[j]));
                            }
                            rets = this.add(jj, rets, singleA);
                            jj++;
                        }
                    } else {
                        if (!this.JS(i, index).equals(value)) {
                            String[] singleA = {};
                            for (int j = 0; j < this.keys.length; j++) {
                                singleA = this.add(j, singleA, this.JS(i, this.keys[j]));
                            }
                            rets = this.add(jj, rets, singleA);
                            jj++;
                        }
                    }
                }
                return new PIP_Array(rets);
            }
        } else {
            return this;
        }
    }
    // to call this function it is a must to call the setKeys function before otherwise it will return no thing
    public PIP_Array gets(String index, String value){
        return this.gets(index,value,"REMOVE");
    }
    // to call this function it is a must to call the setKeys function before otherwise it will return no thing
    public PIP_Array getsD(String index){
        String[][] rets = {{"none"}};
        for(int i = 0; i< this.size; i++){
            Boolean exist = false;
            for(int ii=0; ii<i; ii++){
                if(this.JS(i,index).equals(this.JS(ii,index))){
                    exist = true;
                    break;
                }
            }
            if(!exist){
                rets = this.add(i,rets,this.AV[i]);
            }
        }
        return new PIP_Array(rets);
    }
    // to call this function it is a must to call the setKeys function before otherwise it will return no thing
    public PIP_Array getsM(PIP_Array PIP_ARRAY2, String INDEX1, String INDEX2, String Type){
        PIP_Array ARR = this.getsD(INDEX2);
        PIP_Array all = PIP_ARRAY2.getsD(INDEX1);
        if(all.size>0){
            for(int ii = 0; ii<all.size;ii++){
                ARR = ARR.gets(INDEX2,all.JS(ii,INDEX1),Type);
            }
            return new PIP_Array(ARR.AV);
        } return new PIP_Array(this.empty().AV);
    }
    // to call this function it is a must to call the setKeys function before otherwise it will return no thing
    public PIP_Array getsM(PIP_Array PIP_ARRAY2, String INDEX1, String INDEX2){
        PIP_Array ARR = this.getsD(INDEX2);
        PIP_Array all = PIP_ARRAY2.getsD(INDEX1);
        if(all.size>0){
            for(int ii = 0; ii<all.size;ii++){
                ARR = ARR.gets(INDEX2,all.JS(ii,INDEX1));
            }
            return new PIP_Array(ARR.AV);
        } return new PIP_Array(this.empty().AV);
    }

    public PIP_Array empty(){
        String rets[][] = {{"none"}};
        return new PIP_Array(rets);
    }



}
