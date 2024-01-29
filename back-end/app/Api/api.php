<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Credentials: true');


$servername = "localhost";  
$username = "guilh"; 
$password = "1234";   
$dbname = "taskForGreenn";     


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}


if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $result = $conn->query("SELECT * FROM Tarefas");
    $tasks = [];

    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    echo json_encode($tasks);
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    $titulo = $data['titulo'];
    $descricao = $data['descricao'];
    

    $dataVencimento = DateTime::createFromFormat('d/m/Y', $data['data_vencimento']);
    $dataVencimentoFormatted = $dataVencimento ? $dataVencimento->format('Y-m-d') : null;

    $stmt = $conn->prepare("INSERT INTO Tarefas (titulo, descricao, data_vencimento) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $titulo, $descricao, $dataVencimentoFormatted);
    $stmt->execute();

    echo json_encode(['status' => 'success', 'message' => 'Tarefa adicionada com sucesso.']);
}

$conn->close();

?>