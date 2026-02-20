<?php
    function desplegableVuelos()  {
        try  {
            $conn = conexionBD();

            $stmt = $conn->prepare("SELECT id_vuelo, origen, destino, nombre_aerolinea, fechahorasalida FROM vuelos v, aerolineas a WHERE v.id_aerolinea = a.id_aerolinea and asientos_disponibles > 0;");
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $resultado=$stmt->fetchAll();
            foreach($resultado as $row) {
                echo '<option value="'.$row['id_vuelo'].'">'.$row['origen'].' - '.$row['destino'].' - '.$row['nombre_aerolinea'].' - '.$row['fechahorasalida'].'</option><br>';
            }

        }  catch  (PDOException $e)  {
            throw $e;

        }  finally  {
            if ($conn !== null) {
                $conn = null;
            }
        }
    }

    function stock($conn, $id)  {
        try  {
            $stmt = $conn->prepare("select asientos_disponibles from vuelos where id_vuelo = :id;");
            $stmt->bindParam(':id', $id);
            $stmt->execute();

            $stmt->setFetchMode(PDO::FETCH_ASSOC);
            $resultado=$stmt->fetchAll();
            return $resultado[0]['asientos_disponibles'];

        }  catch (PDOException $e)  {
            throw $e;
        }
    }

?>