<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Credentials: true');

$servername = "localhost";
$username = "guilh";
$password = "1234";
$dbname = "taskForGreenn";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro na conexão com o banco de dados: ' . $e->getMessage()]);
    die();
}

$sql = "SELECT * FROM tarefas WHERE data_vencimento = '2024-01-22'";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erro ao executar consulta SQL: ' . $e->getMessage()]);
}
?>