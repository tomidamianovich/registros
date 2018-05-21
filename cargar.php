<?php    

    echo "<html>";
    echo "<body>";
    echo "<div class='loader'></div>";
    
    //el usuario debe estar logeado para acceder a cargar registros
    require("seguridad.php");

    echo"<script src='https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js'></script>";

    // dirección donde guarda los archivos csv
    $dir_subida = 'C:/xampp/htdocs/registros/files/';
    $flagarchivocomprimido="no";

    foreach(glob($dir_subida.'{*.zip,*.tar}',GLOB_BRACE) as $archivo){
        
        $flagarchivocomprimido="si";
        //Creamos un objeto de la clase ZipArchive()
        $enzipado = new ZipArchive();
    
        //Abrimos el archivo a descomprimir
        $enzipado->open($archivo);
        
        //Extraemos el contenido del archivo dentro de la carpeta especificada
        $extraido = $enzipado->extractTo("./files/");
        
        /* Si el archivo se extrajo correctamente listamos los nombres de los
        * archivos que contenia de lo contrario mostramos un mensaje de error
        */
        if($extraido == TRUE){
            for ($x = 0; $x < $enzipado->numFiles; $x++) {
                $archivo = $enzipado->statIndex($x);            
            }
        }
        else {
            echo "<script type=\"text/javascript\">alert(\"Ocurrió un error y el archivo no se pudó descomprimir.\");</script>";	    
        }
        $enzipado->close();
    

    

    //Se obtiene la cantidad de archivos a cargar
    $total_csv = count(glob($dir_subida.'/{*.txt,*.csv}',GLOB_BRACE));

    
    
    //Se verifica que existan archivos en la carpeta especificada para ser cargados
    if ($total_csv > 0) {

            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "desanuevo";
            
            $con = new mysqli($servername, $username, $password, $dbname);
        
            if ($con->connect_error) {
                die("Ha ocurrido un Error: " . $con->connect_error);
            }      

            $sql = mysqli_query($con,"SELECT MAX(idsubidas) AS idsubidas from `desanuevo`.`subidas`") or die('error');
        
            if($row = mysqli_fetch_array($sql)){       
                $id = $row['idsubidas']+1;
            }else{
                $id = null;
            }
            
            

            //Repite las siguientes funciones para todos los .csv y .txt contenidos en $dir_subida
            foreach(glob($dir_subida.'{*.txt,*.csv}',GLOB_BRACE) as $archivo){
                
                $pesoarchivo=filesize($archivo);
                $arraydirarchivo = explode("/",$archivo);
                //obtengo el final de la url que tiene informacion del archivo con el que trabajare        
                $lastdirarchivo= $arraydirarchivo[count($arraydirarchivo)-1];
                    
                $arraylastdirarchivo = explode(".",$lastdirarchivo);
                $rutaimagen = $arraylastdirarchivo[0];        
                    
                $arrayprotocolo= explode("-",$lastdirarchivo);$lastdirarchivo= $arraydirarchivo[count($arraydirarchivo)-1];
                    
                $arraylastdirarchivo = explode(".",$lastdirarchivo);
                $rutaimagen = $arraylastdirarchivo[0];        
                    
                $arrayprotocolo= explode("-",$lastdirarchivo);
                    
                $protocolo="".$arrayprotocolo[0]."-".$arrayprotocolo[1]."-".$arrayprotocolo[2];
                //obtengo la cantidad de imagenes contenida en la carpeta que contiene las imagenes(mismo nombre que el txt/csv)
                $cantimagenes = count(glob($dir_subida.$rutaimagen.'/{*.jpg,*.gif,*.png}',GLOB_BRACE));
                            
                $cantlineas=0;
                    
                if (fopen($archivo, "r")) {        
                    
                    $filetoopen = fopen($archivo, "r");       
                    
                    $cantlineas=count(file($archivo));               
                        
                    echo "<h1 style='border: 10px solid white; padding: 13px;margin-left: 9%; border-radius: 22px;font-family:  Roboto, Helvetica, Arial, sans-serif;
                    margin-right:  11%; color:  white; text-align:center;'>Registros de Infracciones (".$total_csv." Archivo/s)</h1>";               
                    
                    echo "<p id='registros' style='color: white;margin-bottom: -7px;padding-left: 12%;font-size: 1.8em;font-weight: 500;text-decoration: underline;font-family: Roboto, Helvetica, Arial, sans-serif;'></p>";
                    
                    echo "<h2 style='padding-left: 12%;font-weight: 100;font-family:  Roboto, Helvetica, Arial, sans-serif;color:  white;'>Datos de la Carga:</h2>";              
                    echo "<h2 style='padding-left: 12%;font-weight: 100;font-family:  Roboto, Helvetica, Arial, sans-serif;color:  white;'> - Dirección: ".$archivo."</h2>";              
                    echo "<h2 style='color: white;padding-left: 12%;font-weight: 100;font-family: Roboto, Helvetica, Arial, sans-serif;'> - Peso del Archivo: ".$pesoarchivo." bytes";"</h2>";  
                    echo "<h2 style='color: white;padding-left: 12%;font-weight: 100;font-family: Roboto, Helvetica, Arial, sans-serif;'> - Carpeta Imagenes: ".$rutaimagen." ";"</h2>";  
                    echo "<h2 style='color: white;padding-left: 12%;font-weight: 100;font-family: Roboto, Helvetica, Arial, sans-serif;'> - Cantidad Imagenes: ".$cantimagenes." ";"</h2>";
                    echo "<h2 style='color: white;padding-left: 12%;font-weight: 100;font-family: Roboto, Helvetica, Arial, sans-serif;'> - Cantidad de Archivos Descomprimidos: ".$enzipado->numFiles." ";"</h2>";
                    

                    echo "<style type='text/css'> ";
                    echo " html { ";
                    echo " background-color: #9aca3c } ";
                    echo ".loader {";
                    echo " position: fixed;";
                    echo " left: 0px;";
                    echo " top: 0px;";
                    echo " width: 100%;";
                    echo " height: 100%;";
                    echo " z-index: 9999;";
                    echo " background: url('images/loader.gif') 50% 50% no-repeat rgb(249,249,249);";
                    echo " opacity: .8;";
                    echo "}";
                    echo "   </style> ";
                                        
                    
                
                    while($cantlineas>0){	
                            
                        $cantlineas=$cantlineas-1;   
                        echo "<div id='row' style='font-size: 17px;font-family: &quot;Roboto&quot Helvetica, Arial, sans-serif;
                        border: 2px solid #8BC34A;border-radius:  28px;margin-left: -3.5%;width: 91%;text-align: left;
                        padding-left: 3.5%;line-height: 1.5;color:black;font-weight: 100;margin-top: 17px;
                        background-color:white;'>";

                        //tomo cada valor del csv con el explode y le asigno una variable a cada uno
                        $datos = explode(";",fgets($filetoopen));	
                        $tipoRegistro=trim($datos[0]);
                        $codigoProveedor=trim($datos[1]);
                        $numSerie=trim($datos[2]);
                        $numOperativo=trim($datos[3]);
                        $lugar=trim($datos[4]);
                        $fechaCaptura=trim($datos[5]);
                        $fechaCapturaarray = explode("/",$fechaCaptura);
                        $fechaCaptura="".trim($fechaCapturaarray[2])."-".trim($fechaCapturaarray[1])."-".trim($fechaCapturaarray[0]);
                        $horaCaptura=trim($datos[6]);
                        $horaCaptura = "".idate('H',trim($datos[6])).":".idate('i', trim($datos[6])).":".idate('s', trim($datos[6]));
                        $fechaCaptura = $fechaCaptura." ".$horaCaptura; 
                        $horaCaptura = "".idate('h',trim($datos[6])).":".idate('i', trim($datos[6])).":".idate('s', trim($datos[6]));
                        $velPermitida=trim($datos[7]);
                        $exceso=trim($datos[8])-trim($datos[7]);
                        $velRegistrada=trim($datos[8]);
                        $dominio=trim($datos[9]);
                        $tipoVehiculo=trim($datos[10]);
                        $rutaImagen=trim($datos[11]);
                        $nombreImagen =trim($datos[12]);
                        $fechaProceso=trim($datos[13]);		
                        $fechaProcesoarray = explode("/",$fechaProceso);
                        $fechaProceso="".trim($fechaProcesoarray[2])."-".trim($fechaProcesoarray[1])."-".trim($fechaProcesoarray[0]); 
                        $horaProceso=trim($datos[14]);
                        $horaProceso = "".idate('h',trim($datos[14])).":".idate('i', trim($datos[14])).":".idate('s', trim($datos[14]));
                        $fechaProceso = $fechaProceso." ".$horaProceso;
                        $horaCaptura = "".idate('h',trim($datos[14])).":".idate('i', trim($datos[14])).":".idate('s', trim($datos[14]));
                        $Matricula=trim($datos[15]);
                        $Apellido=trim($datos[16]);
                        $Jerarquia=trim($datos[17]);
                        $numeroProtocolo=trim($datos[18]);
                        $jurisdiccionContratacion=trim($datos[19]);
                        $jurisdiccionAplicacion=trim($datos[20]);
                        $Autoridad=trim($datos[21]);
                        $ejidoUrbano=trim($datos[22]);			
                        $numkm=trim($datos[23]);
                        $nro=trim($datos[24]);
                        $sentido=trim($datos[25]);
                        $mano=trim($datos[26]);
                        $codPostal=trim($datos[27]);
                        $localidad=trim($datos[28]);
                        $diffMinutos=trim($datos[29]);
                        $dominio=trim($datos[31]);
                        $fechaConf=trim($datos[32]);	
                        $fechaConfarray = explode("/",$fechaConf);	
                        $fechaConf="".trim($fechaConfarray[2])."-".trim($fechaConfarray[1])."-".trim($fechaConfarray[0]); echo "<br>";
                        $horaconf=trim($datos[33]);
                        $horaconf = "".idate('h',trim($datos[33])).":".idate('i', trim($datos[33])).":".idate('s', trim($datos[33]));
                        $fechaConf = $fechaConf." ".$horaconf;
                        $ley=trim($datos[34]);
                        $articulo=trim($datos[35]);
                        $inciso=trim($datos[36]);
                        $fechaBajada = $fechaConf." ".$horaconf;
                        $nombreImagen2=trim($datos[37]);
                        $nombreImagen3=trim($datos[38]);
                        $dominio=trim($datos[39]);
                        $archivodeVideo=trim($datos[40]);
                        $coordenadas=trim($datos[41]);
                        $Archivocoord=trim($datos[42]);
                        $carril=trim($datos[43]);
                        $tiempodesdeEncendido=trim($datos[44]);
                        $tiempoRojo=trim($datos[45]);
                        $tiempoAmarilla=trim($datos[46]);
                        $tiempoVerde=trim($datos[47]);
                        $clipVideo=trim($datos[48]);
                        $Notificada=trim($datos[49]);
                        $Observaciones=trim($datos[50]);
                        $idMedioCaptura=trim($datos[51]);

                        //muestro las variables de cada registro con su informacion asociada
                        echo "Tipo de Registro: ".$tipoRegistro; echo "<br>";
                        echo "Codigo Proveedor: ".$codigoProveedor; echo "<br>";
                        echo "Numero de Serie: ".$numSerie; echo "<br>";
                        echo "Numero Operativo: ".$numOperativo; echo "<br>";
                        echo "Lugar: ".$lugar; echo "<br>";
                        echo "Fecha de Captura: ".trim($fechaCapturaarray[2])."-".trim($fechaCapturaarray[1])."-".trim($fechaCapturaarray[0]); echo "<br>";
                        echo "Hora de Captura: ".$horaCaptura;echo "<br>";
                        echo "Vel. Permitida: ".trim($datos[7]); echo "<br>";
                        echo "Vel. Registrada: ".trim($datos[8])." (Exceso de: ".$exceso.")";  echo "<br>";
                        echo "Dominio: ".trim($datos[9]); echo "<br>";
                        echo "Tipo de Vehiculo: ".trim($datos[10]); echo "<br>";
                        echo "Ruta de Imagen: ".trim($datos[11]); echo "<br>";
                        echo "Imagen: ".trim($datos[12]); echo "<br>";
                        echo "Fecha de Proceso: ".trim($fechaProcesoarray[2])."-".trim($fechaProcesoarray[1])."-".trim($fechaProcesoarray[0]);
                        echo "<br>";
                        echo "Hora de Proceso: ".$horaCaptura;
                        echo "<br>";
                        echo "Matricua/Legajo: ".trim($datos[15]); echo "<br>";
                        echo "Apellido y Nombre: ".trim($datos[16]); echo "<br>";
                        echo "Jerarquia o Cargo: ".trim($datos[17]); echo "<br>";
                        echo "Numero Protocolo: ".trim($datos[18]); echo "<br>";
                        echo "Jurisdicción de Contratacion: ".trim($datos[19]); echo "<br>";
                        echo "Jurisdiccion de Aplicación: ".trim($datos[20]); echo "<br>";
                        echo "Autoridad Constatación: ".trim($datos[21]); echo "<br>";
                        echo "Ejido Urbano: ".trim($datos[22]); echo "<br>";
                        echo "Calle/Ruta: ".trim($datos[23]); echo "<br>";
                        echo "Nro/Km: ".trim($datos[24]); echo "<br>";
                        echo "Sentido: ".trim($datos[25]); echo "<br>";
                        echo "Mano: ".trim($datos[26]); echo "<br>";
                        echo "Codigo Postal: ".trim($datos[27]); echo "<br>";
                        echo "Localidad: ".trim($datos[28]); echo "<br>";
                        echo "Minutos Diferencia AP: ".trim($datos[29]); echo "<br>";
                        echo "Dominio: ".trim($datos[31]); echo "<br>";
                        echo "Fecha de Configuración: ".trim($fechaConfarray[2])."-".trim($fechaConfarray[1])."-".trim($fechaConfarray[0]); 
                        echo "Hora de Captura: ".$horaconf; echo "<br>";
                        echo "Imputación (Ley): ".trim($datos[34]); echo "<br>";
                        echo "Imputación (Articulo): ".trim($datos[35]); echo "<br>";
                        echo "Imputación (Inciso): ".trim($datos[36]); echo "<br>";
                        echo "Nombre Imagen 2: ".trim($datos[37]); echo "<br>";
                        echo "Nombre Imagen 3: ".trim($datos[38]); echo "<br>";	
                        echo "Dominio: ".trim($datos[39]); echo "<br>";
                        echo "Archivo de Video: ".trim($datos[40]); echo "<br>";
                        echo "Coordenadas: ".trim($datos[41]); echo "<br>";
                        echo "Archivo de Coordenadas: ".trim($datos[42]); echo "<br>";
                        echo "Carril: ".trim($datos[43]); echo "<br>";
                        echo "Tiempo-Desde-Que-Encendio: ".trim($datos[44]); echo "<br>";
                        echo "Tiempo-Luz-Roja: ".trim($datos[45]); echo "<br>";
                        echo "Tiempo-Luz-Amarilla: ".trim($datos[46]); echo "<br>";
                        echo "Tiempo-Luz-Verde: ".trim($datos[47]); echo "<br>";
                        echo "ClipVideo: ".trim($datos[48]); echo "<br>";
                        echo "Notificada: ".trim($datos[49]); echo "<br>";
                        echo "Observaciones: ".trim($datos[50]); echo "<br>";
                        echo "id Tipo de Medio de Captura: ".trim($datos[51]); echo "<br>";
                        echo "<br>";
                        echo "</div>";

                        //inserto los valores en la tabla de registros
                        $consulta = "INSERT INTO `desanuevo`.`registros`(
                        `tipoRegistro`,`codProveedor`,`nroSerie`,`nroOperativo`,`lugar`,`fechaFactura`,`velPermitida`,
                        `velRegistrada`,`dominio`,`tipoVehiculo`,`rutaImagen`,`nombreImagen`,`fechaProceso`,`Matricula`,`Apellido`,`Jerarquia`,
                        `numeroProtocolo`,`jurisdiccionContratacion`,`jurisdiccionAplicacion`,`jurisdiccionConstatacion`,`ejidoUrbano`,`calleruta`,
                        `numkm`,`sentido`,`mano`,`codPostal`,`localidad`,`diffMinutos`,`fechaConf`,`fechaBajada`,`ley`,`articulo`,    `inciso`,
                        `descInfraccion`,`nombreImagen2`,`nombreImagen3`,`nombreImagen4`,`archivodeVideo`,`coordenadas`,`carril`,`tiempodesdeEncendido`,
                        `tiempoRojo`,`tiempoAmarilla`,`tiempoVerde`,`clipVideo`,`Notificada`,`Observaciones`,`idMedioCaptura`,`idSubida`)
                        VALUES('".$tipoRegistro."','".$codigoProveedor."','".$numSerie."','".$numOperativo."','".$lugar."','".$fechaCaptura."',
                        '".$velPermitida."','".$velRegistrada."','".$dominio."','".$tipoVehiculo."','".$rutaImagen."','".$nombreImagen."',
                        '".$fechaProceso."','".$Matricula."','".$Apellido."','".$Jerarquia."',
                        '".$numeroProtocolo."','".$jurisdiccionContratacion."','".$jurisdiccionAplicacion."','".$Autoridad."',
                        '".$ejidoUrbano."','".$numkm."','".$numkm."','".$sentido."','".$mano."','".$codPostal."',
                        '".$localidad."', '".$diffMinutos."','".$fechaConf."','".$fechaBajada."','".$ley."','".$articulo."','".$inciso."',
                        '','".$nombreImagen2."','".$nombreImagen3."','".$nombreImagen2."','".$archivodeVideo."',
                        '".$coordenadas."','".$Archivocoord."','".$carril."','".$tiempodesdeEncendido."','".$tiempoRojo."',
                        '".$tiempoAmarilla."','".$tiempoVerde."','".$Notificada."','".$Observaciones."','".$idMedioCaptura."','".$id."')";	
                        
                        $flagregistros=null;
                        // si se hace el insert correctamente el flag queda con valor 'ok' sino con valor false
                        if ($con->query($consulta) == TRUE) { $flagregistros='ok'; } 
                    
                }            
                fclose($filetoopen);
                // si no puede leer el archivo da un alert y corta la ejecucion
                }else{
                    echo "<script type=\"text/javascript\">alert(\"Error al abrir el archivo.\");</script>";	    
                    header('Location: index.html');	 
                    exit();   
                }
        }   
   

    //---------------------------------------------------------------------------------------------------------------------
    //--------------------------------------------- Subidas realizadas ----------------------------------------------------
    //---------------------------------------------------------------------------------------------------------------------

    
    $consulta = "INSERT INTO `desanuevo`.`subidas`(`idsubidas`,`protocolo`,`rutaimagen`,`rutaimagenmin`,`tamimagen`, `fechasubida`) 
                 VALUES('".$id."','".$protocolo."','".$archivo."','".$rutaimagen."', '".$pesoarchivo."',CURDATE());";	
    
    $flagsubidas=null;	

    // si se hace el insert correctamente el flag queda con valor 'ok' sino con valor false
    if ($con->query($consulta) == TRUE) { $flagsubidas='ok'; } 

    if ($flagsubidas === "ok" && $flagregistros === "ok"){         
        echo "<script>$('#registros').append( 'Registros cargados Correctamente' );</script>";
        
        foreach(glob($dir_subida.'{*.zip,*.tar}',GLOB_BRACE) as $archivo){
            $arraydirarchivo = explode("/",$archivo);
            //obtengo el final de la url que tiene informacion del archivo con el que trabajare        
            $lastdirarchivo= $arraydirarchivo[count($arraydirarchivo)-1];
            rename($archivo, $dir_subida.'/subidas/'.$lastdirarchivo);  
        }
        
        foreach(glob($dir_subida.'{*.txt,*.csv}',GLOB_BRACE) as $archivo){
            unlink($archivo);
            /*$arraydirarchivo = explode("/",$archivo);
            //obtengo el final de la url que tiene informacion del archivo con el que trabajare        
            $lastdirarchivo= $arraydirarchivo[count($arraydirarchivo)-1];
            $nombrearchivoarray = explode(".",$lastdirarchivo);                             
            rename($archivo, $dir_subida.'/subidas/'.$lastdirarchivo);     
            rename($dir_subida.$nombrearchivoarray[0], $dir_subida.'subidas/'.$nombrearchivoarray[0]);           */
        }

    } else {  
        echo "<script>$('#registros').append( 'Error al cargar los registros.' );</script>";
    }

    $con->close();  
    session_destroy(); 
    
    }
}

 if ($flagarchivocomprimido == "no"){

    echo "<h1 style='border: 10px solid white;margin-top:1.4%; padding: 13px;margin-left: 9%; border-radius: 22px;font-family:  Roboto, Helvetica, Arial, sans-serif;
    margin-right:  9%; color:  white; text-align:center; '>Registros de Infracciones</h1>";               
        
    echo "<h2 style='color: white;padding-left:  11%;margin-top:  3%;font-family: Roboto, Helvetica, Arial, sans-serif;'>No hay ningun archivo a descomprimir en la carpeta de Registros: ".$dir_subida."</h2>";    

    echo "<style type='text/css'> ";
    echo "   html { ";
    echo "  font-family: &quot;Roboto&quot Helvetica, Arial, sans-serif;";
    echo "    background-color: #9aca3c } ";
    echo "   </style> ";
    echo "</html>";
    echo "<div style='width:  100%;margin-left: 29%;margin-top: 2.5%; margin-right:  30%;'>";
        echo "<button onclick=window.location.href='./cargar.php' style='
                width: 20%;
                border-radius: 9px;
                border: 1px solid green;
                font-size:  18px;
                padding: 12px;
                text-align:  center;
                background-color:  white;
                margin-top: 1%;
                font-weight:  100;
                font-family: Roboto, Helvetica, Arial, sans-serif;
                '>Reintentar</button>";
        
        echo "<button onclick=window.location.href='./index.html' style='
                width: 20%;
                border-radius: 9px;
                border: 1px solid green;
                font-size:  18px;
                background-color:  white;
                padding: 12px;
                margin-top: 1%;
                text-align:  center;
                margin-left:  1.3%;
                '>Volver al Menu Principal</button>";
        
    echo "</div>";      
echo "</html>";
}


echo "<script type='text/javascript'>";
echo "$(window).load(function() {";
echo "  $('.loader').fadeOut('slow');";
echo "});";
echo "</script>";

echo "<style type='text/css'> ";

echo "@import url(https://fonts.googleapis.com/css?family=Roboto:400,300,600,400italic);";
echo "* {";
echo "  box-sizing: border-box;";
echo "  -webkit-box-sizing: border-box;";
echo "  -moz-box-sizing: border-box;";
echo "  -webkit-font-smoothing: antialiased;";
echo "  -moz-font-smoothing: antialiased;";
echo "  -o-font-smoothing: antialiased;";
echo "  font-smoothing: antialiased;";
echo "  text-rendering: optimizeLegibility;";
echo "  } ";
echo "</style";

?>

