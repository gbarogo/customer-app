<?php
   $con=mysqli_connect("localhost:3306","root","","aplicacion_clientes");
   if (mysqli_connect_errno($con)) {
      echo "Failed to connect to MySQL: " . mysqli_connect_error();
   }
   $email = $_GET['email_viaje']
   $id_viaje = $_GET['id_viaje']
   $result = mysqli_query($con, "INSERT INTO 'cliente_viajeS' (email_viaje,id_viaje,asiento) VALUES ('$email','$id_viaje','0')");
   ?>
 
