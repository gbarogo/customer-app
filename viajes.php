<?php
   $con=mysqli_connect("localhost:3306","root","","aplicacion_clientes");
   if (mysqli_connect_errno($con)) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }
   
   $email_viaje = $_GET['email_viaje'];
   $id_viaje = $_GET['id_viaje']
   
   $response = array();
   $result = mysqli_query($con,"INSERT INTO cliente_viajes (email_viaje, id_viaje) VALUES ('$email_viaje', '$id_viaje');
	 $response["success"] = 1
	  }
   
   
