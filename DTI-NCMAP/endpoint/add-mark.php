<?php
include("../conn/conn.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mark_name'], $_POST['mark_long'], $_POST['mark_lat'])) {
        $markName = $_POST['mark_name'];
        $markLong = $_POST['mark_long'];
        $markLat = $_POST['mark_lat'];

        try {
            $stmt = $conn->prepare("INSERT INTO tbl_mark (mark_name, mark_long, mark_lat) VALUES (:mark_name, :mark_long, :mark_lat)");
            
            $stmt->bindParam(":mark_name", $markName, PDO::PARAM_STR); 
            $stmt->bindParam(":mark_long", $markLong, PDO::PARAM_STR);
            $stmt->bindParam(":mark_lat", $markLat, PDO::PARAM_STR);

            $stmt->execute();

            header("Location: http://localhost/interactive-map-marker/");

            exit();
        } catch (PDOException $e) {
            echo "Error:" . $e->getMessage();
        }

    } else {
        echo "
            <script>
                alert('Please fill in all fields!');
                window.location.href = 'http://localhost/interactive-map-marker/';
            </script>
        ";
    }
}
?>
