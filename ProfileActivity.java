package com.example.gbaro.pruebalogin;

import android.os.AsyncTask;
import android.os.Bundle;
import android.support.design.widget.FloatingActionButton;
import android.support.design.widget.Snackbar;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.widget.EditText;
import android.widget.TextView;

import org.json.JSONArray;
import org.json.JSONException;
import org.json.JSONObject;

import java.io.BufferedReader;
import java.io.IOException;
import java.io.InputStreamReader;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.ProtocolException;
import java.net.URL;
import java.util.concurrent.ExecutionException;

public class ProfileActivity extends AppCompatActivity {
    String[] dataCustomer = new String[5];
    String jDev;
    private TextView dniText, pointsText, nameText, surnameText, secondText;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_profile);
        Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        setSupportActionBar(toolbar);
        dniText = (TextView) findViewById(R.id.dniCustomer);
        pointsText = (TextView) findViewById(R.id.pointsCustomer);
        nameText = (TextView) findViewById(R.id.nameCustomer);
        surnameText = (TextView) findViewById(R.id.surname1Customer);
        secondText = (TextView) findViewById(R.id.surname2Customer);

        FloatingActionButton fab = (FloatingActionButton) findViewById(R.id.fab);
        fab.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Snackbar.make(view, "Replace with your own action", Snackbar.LENGTH_LONG)
                        .setAction("Action", null).show();
            }
        });
        String correo = ((MyApplication) this.getApplication()).getSomeVariable();
        String url = "http://ingenieriasoftwarecustomerapp.000webhostapp.com/consultaDatos.php?username="+correo;
        HtttpRequestGet getProfile = new HtttpRequestGet();
        try {
            jDev = getProfile.execute(url).get();
            JSONArray jsonArr = new JSONArray(jDev);
            JSONObject jsonObj = jsonArr.getJSONObject(0);
            //JSONArray customers = jsonObj.getJSONArray("data");
            //for (int i = 0; i < customers.length(); i++) {
            //  JSONObject c = customers.getJSONObject(i);
            dataCustomer[0] = jsonObj.getString("dni");
            dataCustomer[1] = jsonObj.getString("puntos");
            dataCustomer[2] = jsonObj.getString("name");
            dataCustomer[3] = jsonObj.getString("surname");
            dataCustomer[4] = jsonObj.getString("second_surname");
        } catch (InterruptedException e) {
            e.printStackTrace();
        } catch (ExecutionException e) {
            e.printStackTrace();
        } catch (JSONException e) {
            e.printStackTrace();
        }
        dniText.setText(dataCustomer[0]);
        pointsText.setText(dataCustomer[1]);
        nameText.setText(dataCustomer[2]);
        surnameText.setText(dataCustomer[3]);
        secondText.setText(dataCustomer[4]);
    }
}