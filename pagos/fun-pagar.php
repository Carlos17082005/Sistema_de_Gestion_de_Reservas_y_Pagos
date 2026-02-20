<?php

// Se incluye la librería
include 'signatureUtils/signature.php';

	function pagar()  {
		//Datos de configuración
		$version = "HMAC_SHA512_V2";
		$kc = 'sq7HjrUOBfKmC576ILgskD5srU870gJ7'; //Clave recuperada de CANALES

		// Valores de entrada que no hemos cmbiado para ningun ejemplo
		$fuc = "263100000";
		$terminal = "12";
		$moneda = "978";
		$transactionType = "0";
		$url = ""; // URL para recibir notificaciones del pago
		$order = time();
		$amount = calcularPrecioTotal() * 100;

		$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https://" : "http://";
		$domain = $_SERVER['HTTP_HOST'];

		// Si tu proyecto está en una subcarpeta, esto la incluirá
		$currentDir = dirname($_SERVER['PHP_SELF']); 

		// Limpiamos la ruta para asegurar que termine en la carpeta raíz del proyecto
		// y añadimos la ruta relativa al archivo de éxito
		$urlOK = $protocol . $domain . $currentDir . "/pagos/pagoCorrecto.php";
		$urlKO = $protocol . $domain . $currentDir . "/pagos/pagoCancelado.php";

		// Se Rellenan los campos
		$data = array(
			"DS_MERCHANT_AMOUNT" => $amount,
			"DS_MERCHANT_ORDER" => $order,
			"DS_MERCHANT_MERCHANTCODE" => $fuc,
			"DS_MERCHANT_CURRENCY" => $moneda,
			"DS_MERCHANT_TRANSACTIONTYPE" => $transactionType,
			"DS_MERCHANT_TERMINAL" => $terminal,
			"DS_MERCHANT_MERCHANTURL" => $url,
			"DS_MERCHANT_URLOK" => $urlOK,
			"DS_MERCHANT_URLKO" => $urlKO
		);

		// Se generan los parámetros de la petición
		$params = Utils::base64_url_encode_safe(json_encode($data));
		$signature = Signature::createMerchantSignature($kc, $params, $order);

		return [$version, $params, $signature];
	}

	function calcularPrecioTotal()  {
		$carrito = array();
		if (isset($_SESSION['carrito'])) { $carrito = unserialize($_SESSION['carrito']); }
		$precioTotal = 0;

		try {
			$conn = conexionBD();
			$conn->beginTransaction();

			foreach($carrito as $id => $cantidad)  {
				$stmt = $conn->prepare("select precio_asiento from vuelos where id_vuelo = :id;");
				$stmt->bindParam(':id', $id);
				$stmt->execute();

				$stmt->setFetchMode(PDO::FETCH_ASSOC);
				$resultado=$stmt->fetchAll();
				$precioTotal += $cantidad * $resultado[0]['precio_asiento'];
			}
			return $precioTotal;

		}  catch(PDOException $e)  {
			error($e);

		}  finally  {
			if ($conn !== null) {
				$conn = null;
			}
		}
	}
?>