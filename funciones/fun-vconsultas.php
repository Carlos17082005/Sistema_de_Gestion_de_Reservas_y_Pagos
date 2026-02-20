<?php
    function desplegableReservas($dni)  {
        try  {
            $conn = conexionBD();
            $stmt = $conn->prepare("SELECT id_reserva FROM reservas WHERE dni_cliente = (:dni) GROUP BY id_reserva");
            $stmt->bindParam(':dni', $dni);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $resultado=$stmt->fetchAll();
            foreach($resultado as $row) {
                echo '<option value="'.$row['id_reserva'].'">'.$row['id_reserva'].'</option><br>';
            }

        }  catch  (PDOException $e)  {
            throw $e;

        }  finally  {
            if ($conn !== null) {
                $conn = null;
            }
        }
    }

    function consulta($conn, $id)  {
        try  {
            $stmt = $conn->prepare("SELECT origen, destino, fechahorasalida, fechahorallegada, nombre_aerolinea, num_asientos FROM vuelos v, aerolineas a, reservas r WHERE v.id_aerolinea = a.id_aerolinea and v.id_vuelo = r.id_vuelo and id_reserva = (:id)");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $resultado=$stmt->fetchAll();
            echo '<table>
                    <tr><th>Aerolinea</th><th>Origen</th><th>Destino</th><th>salida</th><th>legada</th><th>Asientos</th></tr>';
            foreach($resultado as $row) {
                echo '<tr><td>'.$row['nombre_aerolinea'].'</td><td>'.$row['origen'].'</td><td>'.$row['destino'].'</td><td>'.$row['fechahorasalida'].'</td><td>'.$row['fechahorallegada'].'</td><td>'.$row['num_asientos'].'</td></tr>';
            }
            echo '</table>';
        }  catch  (PDOException $e)  {
            throw $e;

        }
    }
?>