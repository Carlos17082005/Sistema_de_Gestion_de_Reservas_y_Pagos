<?php
    function test_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
    
    function conexionBD()  {
        try  {
            $servername = "localhost";
            $username = "root";
            $password = "rootroot";
            $dbname = "reservas";

            $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return $conn;

        }  catch (PDOException $e)  {
            throw $e;
        }
    }

    function login($conn, $usuario, $password)  {
        try  {
            $sql = $conn->prepare("SELECT dni, nombre FROM clientes WHERE email = (:email);");

            $sql->bindParam(':email', $usuario);
            
            $sql->execute();

            $sql->setFetchMode(PDO::FETCH_ASSOC);
            $resultado=$sql->fetchAll();

            if (isset($resultado[0]['dni']) && substr($resultado[0]['dni'], 0, 4) == $password)  {
                return [$resultado[0]['dni'], $resultado[0]['nombre'], true];
            }  else  {
                return ['', '', false];
            }
            
        }  catch (PDOException $e)  {
            throw $e;
        }
    }

    function error($e)  {
        $error = $e -> errorInfo;
        $codigo_error = $error[1];

        switch ($codigo_error)  {
            case 1062:
                $text = 'Error: Primary key duplicada';
                break;
            case 1452:
                $text = 'Error: Foreing key no encontrada';
                break;
            case 1064:
                $text = 'Error en la sintaxis SQL';
                break;
            // 1054  Campo desconocido
            // 1054  Unknown column
            // 1048 Column 'id_reserva' cannot be null
            default:
                $text = '';
        }
        
        if ($text == "")  {
            echo '<p style="text-align: center; color: red;">' . $e->getMessage() . '</p>';
        }  else  {
            echo '<p style="text-align: center; color: red;">' . $text . '</p>';
        }
    }
    
    function comprar($conn, $id, $cantidad)  {
        try  {
            $stmt = $conn->prepare("select asientos_disponibles from vuelos where id_vuelo = :id;");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $resultado=$stmt->fetchAll();

            $sql = $conn->prepare("UPDATE vuelos SET asientos_disponibles = (:total) WHERE id_vuelo = (:id);"); 
            $total = $resultado[0]['asientos_disponibles'] - $cantidad;
            $sql->bindParam(':total', $total);
            $sql->bindParam(':id', $id);
            $sql->execute(); 

        }  catch (PDOException $e)  {
            throw $e;
        }
    }

    function id_reserva($conn)  {
        try  {
            $stmt = $conn->prepare("select max(id_reserva) as max from reservas;");
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $resultado=$stmt->fetchAll();
            $reserva = $resultado[0]['max'];
            $reserva = 'R'.str_pad((intval(substr($reserva, 1)) + 1), 4, "0", STR_PAD_LEFT);

            return $reserva;

        }  catch (PDOException $e)  {
            throw $e;
        }
    }

    function registrar($conn, $id, $cantidad, $reserva)  {
        try  {
            $stmt = $conn->prepare("select precio_asiento from vuelos where id_vuelo = :id;");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $resultado=$stmt->fetchAll();
            $precio = $cantidad * $resultado[0]['precio_asiento'];

            $sql = $conn->prepare("INSERT INTO reservas (id_reserva, id_vuelo, dni_cliente, fecha_reserva, num_asientos, preciototal) VALUES (:id_r, :id_v, :dni, :fecha, :num_a, :precio)");

            $dni = $_SESSION['dni'];
            $fecha = $_COOKIE['fecha'];

            $sql->bindParam(':id_r', $reserva);
            $sql->bindParam(':id_v', $id);
            $sql->bindParam(':dni', $dni);
            $sql->bindParam(':fecha', $fecha);
            $sql->bindParam(':num_a', $cantidad);
            $sql->bindParam(':precio', $precio);

            $sql->execute();

        }  catch (PDOException $e)  {
            throw $e;
        }
    }
?>