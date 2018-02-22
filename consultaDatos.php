<?php
   $con=mysqli_connect("localhost:3306","root","","aplicacion_clientes");

   if (mysqli_connect_errno($con)) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }
   $username = $_GET['username'];
   $result=mysqli_query($con,"SELECT dni_user FROM user WHERE email='$username'");
   $row = mysqli_fetch_array($result);
   $data = $row[0];
   $result=mysqli_query($con,"SELECT * FROM customer WHERE dni='$data'");
   while ($row = mysqli_fetch_assoc($result)) {
		
		$array[] = $row;
		
	}
	header('Content-Type:Application/json');
	
	echo json_encode($array);
   
   mysqli_free_result($result);
 
    mysqli_close($con);
   
   
   
   
   
   
   
   ?>