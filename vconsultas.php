<?php 
    include 'funciones/funciones.php';
    include 'funciones/fun-vconsultas.php';
    session_start();

	$fecha = date("Y-m-d");
    setcookie("fecha", $fecha, time() + 3600, '/');  // Actualiza la fecha cada vez que se entra en la pagina
?>
<html>
   
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
     <title>RESERVAS VUELOS</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css">
    <style>
        table, tr {
            border: 2px solid black;
        }
    </style>
 </head>
   
 <body>
   

    <div class="container ">
        <!--Aplicacion-->
		<div class="card border-success mb-3" style="max-width: 30rem;">
		<div class="card-header">Consultar Reservas</div>
		<div class="card-body">
	  	  

	<!-- INICIO DEL FORMULARIO -->
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
	
		<B>Email Cliente: </B><?php echo $_SESSION['email']; ?>    <BR>
		<B>Nombre Cliente: </B><?php echo $_SESSION['nombre']; ?>    <BR>
		<B>Fecha: </B><?php echo $_COOKIE['fecha']; ?>    <BR><BR>
		
		<B>Numero Reserva</B><select name="reserva" class="form-control">
			<?php
                try {
                    desplegableReservas($_SESSION['dni']);
                }
                catch(PDOException $e)  {
                    error($e);
                }
            ?>
			</select>	
		<BR><BR><BR><BR><BR><BR><BR>
		<div>
			<input type="submit" value="Consultar Reserva" name="consultar" class="btn btn-warning disabled">
			<input type="submit" value="Volver" name="volver" class="btn btn-warning disabled">
		</div>		
	</form>
	
	<!-- FIN DEL FORMULARIO -->
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<button type="submit" name="cerrar" value="cerrar" class="btn btn-warning disabled">Cerrar sesion</button>
	</form>
  </body>
   	<?php
   		$carrito = array();
		if (isset($_SESSION['carrito'])) { $carrito = unserialize($_SESSION['carrito']); } 

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['cerrar']))  {
                session_unset();
                session_destroy();
                setcookie(session_name(), '', time() - 3600, '/');
				setcookie("fecha", '', time() - 3600, '/');
				setcookie("id_reserva", '', time() - 3600, '/');
                
                header("Location: index.php");
                exit;

            }  elseif (isset($_POST['volver']))  {
				header("Location: vinicio.php");
                exit;

            }  elseif (isset($_POST['consultar']))  {
				try {
                    $conn = conexionBD();
                    $conn->beginTransaction();

					$id = test_input($_POST['reserva']);
					consulta($conn, $id);

                }  catch(PDOException $e)  {
                    $conn->rollback();

                    error($e);

                }  finally  {
                    if ($conn !== null) {
                        $conn = null;
                    }
                } 
				
            }
		}
    ?>
</html>

