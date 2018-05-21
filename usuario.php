<?php

   $user = $_POST['user'];
   $password = $_POST['password'];   
   $_SESSION["autentica"] = "false";

   if (($user == "dppsv") AND ($password == "123cambiar")) {

      session_start(); 
      $_SESSION["autentica"] = "ok";
      $_SESSION["usuario"] = $user;
      //echo '<script language="javascript">alert("Bienvenido '.$_SESSION["usuario"].'.");</script>';      
      echo '<script language="javascript">window.location="./cargar.php"</script>';
      
    } else {

      $_SESSION["autentica"] = "false";      
      echo '<script language="javascript">alert("¡Usuario y/o contraseña incorrectos!");</script>';     
      echo '<script language="javascript">window.location="./index.html"</script>';        

    }
?>

