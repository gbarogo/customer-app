<?php
   $con=mysqli_connect("localhost:3306","root","","aplicacion_clientes");

   if (mysqli_connect_errno($con)) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }

   $nombre = $_GET['nombre'];
   $apellido1 = $_GET['apellido1'];
   $apellido2 = $_GET['apellido2'];
   $dni = $_GET['dni'];
   $telefono = $_GET['telefono'];
   $email = $_GET['email'];
   $pass = $_GET['pass'];
   
   
   $response = array();
   $result = mysqli_query($con,"SELECT * FROM user WHERE email='$email'");
   $nr=mysqli_num_rows($result);
	  if($nr=='1'){
		  $response["success"] = 1;
	  echo json_encode($response);}
	  else{
		  $response["success"] = 0;
		  mysqli_query($con,"INSERT INTO customer (dni, phone_number, name, surname, second_surname) VALUES ('$dni',$telefono,'$nombre',
		  '$apellido1','$apellido2')");
		  mysqli_query($con,"INSERT INTO user (email,password,dni_user) VALUES ('$email','$pass','$dni')");
		  
		  echo json_encode($response);
	  }
	
   mysqli_close($con);
?>