<?php
   $con=mysqli_connect("localhost:3306","root","","aplicacion_clientes");

   if (mysqli_connect_errno($con)) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }

   $username = $_GET['username'];
   $password = $_GET['password'];
   $result = mysqli_query($con,"SELECT * FROM user where email='$username' 
      and password='$password'");
	  $nr=mysqli_num_rows($result);
	  $response = array();
	  if($nr=='1'){
		  $response["success"] = 1;
		  echo json_encode($response);
	  }else{
		  $response["success"] = 0;
		  echo json_encode($response);
	  }
	  
	  
   
	
   mysqli_close($con);
?>