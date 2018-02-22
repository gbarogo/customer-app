<?php
   $con=mysqli_connect("localhost:3306","root","","aplicacion_clientes");

   if (mysqli_connect_errno($con)) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }

   $nombre = $_POST['nombre'];
   $apellido1 = $_POST['apellido1'];
   $apellido2 = $_POST['apellido2'];
   $dni = $_POST['dni'];
   $telefono = $_POST['telefono'];
   $email = $_POST['email'];
   $pass = $_POST['pass'];
   
   
   
   $result = mysqli_query($con,"SELECT * FROM user where email='$email'");
	  if($result){
	  echo "Usuario ya registrado ";}
	  else{
		  mysqli_query($con,"INSERT INTO customer (dni, phone_number, name, surname, second_surname) values ('$dni',$telefono,'$nombre',
		  '$apellido1','$apellido2')");
		  mysqli_query($con,"INSERT INTO user (email,password,dni_user) values ('$email','$password','$dni')");
		  
		  echo "usuario registrado con exito";
	  }
	
   mysqli_close($con);
?>