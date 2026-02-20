<?php 
    include 'funciones/funciones.php';
    session_start();

	$fecha = date("Y-m-d H-i-s");
    setcookie("fecha", $fecha, time() + 3600, '/');  // Actualiza la fecha cada vez que se entra en la pagina
?>
<html>
   
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <title>RESERVAS VUELOS</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
 </head>
   
 <body>

    <div class="container ">
        <!--Aplicacion-->
		<div class="card border-success mb-3" style="max-width: 30rem;">
		<div class="card-header">Menú Usuario </div>
		<div class="card-body">

		<B>Email Cliente: </B><?php echo $_SESSION['email']; ?>    <BR>
		<B>Nombre Cliente: </B><?php echo $_SESSION['nombre']; ?>    <BR>
		<B>Fecha: </B><?php echo $_COOKIE['fecha']; ?>    <BR><BR>
	  
		<!--Formulario con enlaces -->
		<div>
			<form id="inicio" name="inicio" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post" class="card-body">

				<input type="submit" value="Reservar Vuelos" name="reservar" class="btn btn-warning disabled">
				<input type="submit" value="Consultar Reserva" name="consultar" class="btn btn-warning disabled">
				<input type="submit" value="Salir" name="salir" class="btn btn-warning disabled">

			</form>
		</div>	
		
       
		
		  
	</div>  
	<?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['salir']))  {
                session_unset();
                session_destroy();
                setcookie(session_name(), '', time() - 3600, '/');
				setcookie("fecha", '', time() - 3600, '/');
				setcookie("id_reserva", '', time() - 3600, '/');
                
                header("Location: index.php");
                exit;

            }  elseif (isset($_POST['reservar']))  {
				header("Location: vreservas.php");
                exit;

            }  elseif (isset($_POST['consultar']))  {
				header("Location: vconsultas.php");
                exit;
				
            }
		}
    ?>
	  
     
   </body>
   
</html>


