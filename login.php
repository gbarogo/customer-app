<?php
   $con=mysqli_connect("localhost:3306","root","","aplicacion_clientes");

   if (mysqli_connect_errno($con)) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }

   $username = $_GET['username'];
   $password = $_GET['password'];
   $result = mysqli_query($con,"SELECT * FROM user where email='$username' 
      and password='$password'");
	  if($result) echo "logueado correctamete"
	  else echo "va a ser que no"
	  
	  
   
	
   mysqli_close($con);
?>