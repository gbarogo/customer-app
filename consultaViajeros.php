<?php
   $con=mysqli_connect("localhost:3306","root","","aplicacion_clientes");
   if (mysqli_connect_errno($con)) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }
   $viaje = $_GET['id_viaje'];
   $result=mysqli_query($con,"SELECT email_viaje FROM cliente_viajes WHERE id_viaje='$viaje'");
   while ($row = mysqli_fetch_assoc($result)) {
		
		$array[] = $row;
		
	}
	header('Content-Type:Application/json');
	echo json_encode($array);
   
   mysqli_free_result($result);
 
    mysqli_close($con);
   
   
   
   
   
   
   
   ?>
