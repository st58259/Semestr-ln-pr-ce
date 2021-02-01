
<?php
$servername = "localhost";
$userdb = "root";
$passdb = "";
$dbname = "gallery";
$inRegErr = false;
$outRegErr = false;
$inLogErr = false;


try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $userdb, $passdb);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

?>