package com.example.gbaro.pruebalogin;

import android.app.Application;

/**
 * Created by gbaro on 08/03/2018.
 */

public class MyApplication extends Application {

    private String someVariable;

    public String getSomeVariable() {
        return someVariable;
    }

    public void setSomeVariable(String someVariable) {
        this.someVariable = someVariable;
    }
}