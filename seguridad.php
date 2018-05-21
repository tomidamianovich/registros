<?php

header('Content-Type: text/html; charset=utf-8');

@session_start();
//Validamos si existe realmente una sesión activa o no 
if(!isset($_SESSION["autentica"])){
    
    echo '<script language="javascript">alert("Debe logearse en el Sistema.");</script>';      
    echo '<script language="javascript">window.location="./index.html"</script>';
    exit(); 
    
}
?>