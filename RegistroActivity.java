package com.example.gbaro.pruebalogin;

import android.os.Bundle;
import android.support.design.widget.FloatingActionButton;
import android.support.design.widget.Snackbar;
import android.support.v7.app.AppCompatActivity;
import android.support.v7.widget.Toolbar;
import android.view.View;
import android.widget.EditText;

import org.json.JSONException;
import org.json.JSONObject;

import java.util.concurrent.ExecutionException;

public class RegistroActivity extends AppCompatActivity {
    private EditText nameText, surnameText,secondSurnameText,phoneText,emailText,passwordText,dniText;
    private String name,surname,secondSurname,phone,email,password,dni;
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_registro);
        Toolbar toolbar = (Toolbar) findViewById(R.id.toolbar);
        nameText=(EditText)findViewById(R.id.newName);
        surnameText=(EditText)findViewById(R.id.newSurname);
        secondSurnameText=(EditText)findViewById(R.id.newSecondSurname);
        phoneText=(EditText)findViewById(R.id.newPhone);
        emailText=(EditText)findViewById(R.id.newEmail);
        passwordText=(EditText)findViewById(R.id.newPassword);
        dniText=(EditText)findViewById(R.id.newDni);
        setSupportActionBar(toolbar);

        FloatingActionButton fab = (FloatingActionButton) findViewById(R.id.fab);
        fab.setOnClickListener(new View.OnClickListener() {
            @Override
            public void onClick(View view) {
                Snackbar.make(view, "Replace with your own action", Snackbar.LENGTH_LONG)
                        .setAction("Action", null).show();
            }
        });
        getSupportActionBar().setDisplayHomeAsUpEnabled(true);
    }

    public void continuarRegistro(View view){
        name=nameText.getText().toString();
        surname=surnameText.getText().toString();
        secondSurname=secondSurnameText.getText().toString();
        phone=phoneText.getText().toString();
        email=emailText.getText().toString();
        password=passwordText.getText().toString();
        dni=dniText.getText().toString();
        String url ="http://ingenieriasoftwarecustomerapp.000webhostapp.com/registro.php?nombre="+name+"&apellido1="+surname+"&apellido2="+secondSurname+"&dni="+dni+"&telefono="+phone+"&email="+email+"&pass="+password;
        HtttpRequestGet request = new HtttpRequestGet();
        try {
            String registroRequest= request.execute(url).get();
            JSONObject jObj = new JSONObject(registroRequest);
            int registroExito = jObj.getInt("success");
            if(registroExito==1){
                ((MyApplication) this.getApplication()).setSomeVariable(email);
                finish();
            }
            else{
                nameText.setText("No se ha podido registrar");
            }
        } catch (InterruptedException e) {
            e.printStackTrace();
        } catch (ExecutionException e) {
            e.printStackTrace();
        } catch (JSONException e) {
            e.printStackTrace();
        }
    }

}
