<?php
include ('../conn/conn.php');

if (isset($_GET['mark'])) {
    $mark = $_GET['mark'];

    try {

        $query = "DELETE FROM tbl_mark WHERE tbl_mark_id = '$mark'";

        $stmt = $conn->prepare($query);

        $query_execute = $stmt->execute();

        if ($query_execute) {
            header("Location: http://localhost/interactive-map-marker/");
        } else {
            header("Location: http://localhost/interactive-map-marker/");
        }

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>