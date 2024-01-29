<?php

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT');
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'], $data['completed'])) {
        $id = $data['id'];
        $completed = $data['completed'];

        $stmt = $conn->prepare("UPDATE Tarefas SET completed=? WHERE id=?");
        $stmt->bind_param("ii", $completed, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Estado da tarefa atualizado com sucesso.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Nenhuma alteração realizada ou tarefa não encontrada.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Dados incompletos']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
}

$conn->close();
?>
