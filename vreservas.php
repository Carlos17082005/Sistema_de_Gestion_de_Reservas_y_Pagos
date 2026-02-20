<?php 
    include 'funciones/funciones.php';
	include 'funciones/fun-vreservas.php';
	include 'pagos/fun-pagar.php';
    session_start();

	$fecha = date("Y-m-d");
    setcookie("fecha", $fecha, time() + 3600, '/');  // Actualiza la fecha cada vez que se entra en la pagina
	$carrito = array();
	if (isset($_SESSION['carrito'])) { $carrito = unserialize($_SESSION['carrito']); }
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
		<div class="card-header">Reservar Vuelos</div>
		<div class="card-body">
	  	  
	<div>
	<!-- INICIO DEL FORMULARIO -->
		<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">

		
			<B>Email Cliente: </B><?php echo $_SESSION['email']; ?>    <BR>
			<B>Nombre Cliente: </B><?php echo $_SESSION['nombre']; ?>    <BR>
			<B>Fecha: </B><?php echo $_COOKIE['fecha']; ?>    <BR><BR>
			
			<B>Vuelos</B><select name="vuelos" class="form-control">
				<?php
					try {
						desplegableVuelos();
					}
					catch(PDOException $e)  {
						error($e);
					}
				?>
				</select>	
			<BR> 
			<B>Número Asientos</B><input type="number" name="asientos" size="3" min="1" max="100" value="1" class="form-control">
			<BR><BR><BR><BR><BR>
				<input type="submit" value="Agregar a Cesta" name="agregar" class="btn btn-warning disabled">
				<input type="submit" value="Vaciar Cesta" name="vaciar" class="btn btn-warning disabled">
				<input type="submit" value="Volver" name="volver" class="btn btn-warning disabled">
		</form>
		<form name="frm" action="https://sis-t.redsys.es:25443/sis/realizarPago" method="POST" target="_blank">
			<?php
				list($version, $params, $signature) = pagar();
			?>

			<input type="hidden" name="Ds_SignatureVersion" value="<?php echo $version; ?>"/>
			<input type="hidden" name="Ds_MerchantParameters" value="<?php echo $params; ?>"/>
			<input type="hidden" name="Ds_Signature" value="<?php echo $signature; ?>"/>
			
			<?php
				try {
					$conn = conexionBD();
					$conn->beginTransaction();

					$correcto = false;
					foreach($carrito as $id => $cantidad)  {
						$correcto = $cantidad <= stock($conn, $id);
						if (!$correcto)  { break; }
					}

					if ($correcto)  {
						echo '<input type="submit" value="Comprar" name="comprar" class="btn btn-warning disabled">';

					}  else  {
						echo '<input type="submit" value="Comprar" name="comprar" disabled class="btn btn-warning disabled">';
						if ($carrito != [])  {
							echo '<p style="text-align: center; color: red;">Error: No hay sufienentes asientos disponibles en uno de sus vuelos</p>';
						}
					}
				}  catch(PDOException $e)  {
					error($e);

				}  finally  {
					if ($conn !== null) {
						$conn = null;
					}
				}
			?>
		</form>
	</div>
	
	<!-- FIN DEL FORMULARIO -->
	<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
		<input type="submit" value="Cerrar Sesion" name="cerrar" class="btn btn-warning disabled">
	</form>
  </body>
   	<?php
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

            }  elseif (isset($_POST['vaciar']))  {
				$carrito = [];
				
            }  elseif (isset($_POST['agregar']))  {
				$id = test_input($_POST['vuelos']);
                $cantidad = (int)test_input($_POST['asientos']);
                
                if (isset($carrito[$id])) {
                    $carrito[$id] += $cantidad;
                } else {
                    $carrito[$id] = $cantidad;
                }
            }
			$_SESSION['carrito'] = serialize($carrito);
			header("Location: vreservas.php");
        	exit;
		}
		echo '<br><br><h3>Carrito</h3>';
		var_dump($carrito);
        
    ?>
</html>

