<?php
// Database Configuration
$host = 'localhost';
$username = 'root';
$password = '';
$dbname = 'ra_enterprises_india';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    // If database not found, try connecting without DB name and create it
    if ($e->getCode() == 1049) {
         try {
            $pdo = new PDO("mysql:host=$host", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
            $pdo->exec("USE `$dbname`");
         } catch (PDOException $ex) {
             die("Connection failed: " . $ex->getMessage());
         }
    } else {
        die("Connection failed: " . $e->getMessage());
    }
}
?>
