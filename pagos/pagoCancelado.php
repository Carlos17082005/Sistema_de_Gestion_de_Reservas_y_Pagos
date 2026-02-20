<?php

include 'signatureUtils/signature.php';
include '../funciones/funciones.php';

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
    <title>PAGO COMPLETADO</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
 </head>
   
 <body>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
		<div>
			<input type="submit" value="Cerrar Sesion" name="cerrar" class="btn btn-warning disabled">
			<input type="submit" value="Volver" name="volver" class="btn btn-warning disabled">
		</div>		
	</form>
</html>

<?php
	if ($_SERVER["REQUEST_METHOD"] == "POST") {
		if (isset($_POST['cerrar']))  {
			session_unset();
			session_destroy();
			setcookie(session_name(), '', time() - 3600, '/');
			setcookie("fecha", '', time() - 3600, '/');
			setcookie("id_reserva", '', time() - 3600, '/');
			
			header("Location: ../index.php");
			exit;

		}  elseif (isset($_POST['volver']))  {
			header("Location: ../vinicio.php");
			exit;

		}
	}

	$jsonParams = json_decode(file_get_contents('php://input'), true);
	$receivedParams = array_merge($_GET, $_POST, is_array($jsonParams) ? $jsonParams : []);

	if(empty($receivedParams)) {
		die("No se recibió respuesta");
	}
			
	$kc = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7'; //Clave recuperada de CANALES

	$version = $receivedParams["Ds_SignatureVersion"];
	$datos = $receivedParams["Ds_MerchantParameters"];
	$signatureRecibida = $receivedParams["Ds_Signature"];
	$decodec = Utils::base64_url_decode_safe($datos);	
	$data = json_decode($decodec, true);

	$order = empty($data['Ds_Order']) ? $data['DS_ORDER'] : $data['Ds_Order'];
	$firma = Signature::createMerchantSignature($kc, $datos, $order);	

	try {
		Signature::checkSignatures($signatureRecibida, $firma);
		echo '<p style="text-align: center; color: red;">No se ha podido realizar la compra</p>';

	} catch (Exception $e) {
		echo '<p style="text-align: center; color: red;">No se ha podido realizar la compra</p>';
	}

?>