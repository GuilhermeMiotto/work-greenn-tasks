<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: DELETE');
header('Content-Type: application/json');


$servername = "localhost";
$username = "guilh";
$password = "1234";
$dbname = "taskForGreenn";


$conn = new mysqli($servername, $username, $password, $dbname);


if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {

    $taskId = $_GET['id'];


    $stmt = $conn->prepare("DELETE FROM Tarefas WHERE id = ?");
    $stmt->bind_param("i", $taskId);
    $stmt->execute();


    if ($stmt->affected_rows > 0) {
        echo json_encode(['status' => 'success', 'message' => 'Tarefa excluída com sucesso.']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Erro ao excluir tarefa.']);
    }


    $stmt->close();
}

$conn->close();
