<?php
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'guilh');
define('DB_PASSWORD', '1234');
define('DB_NAME', 'taskForGreenn');

$conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

if ($conn->connect_error) {
    die("Erro de Conexão: " . $conn->connect_error);
}