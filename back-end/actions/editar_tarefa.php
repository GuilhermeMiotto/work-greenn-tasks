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

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);

    if (isset($data['id'], $data['titulo'], $data['descricao'], $data['data_vencimento'])) {
        $id = $data['id'];
        $titulo = $data['titulo'];
        $descricao = $data['descricao'];

        $dataVencimento = DateTime::createFromFormat('d/m/Y', $data['data_vencimento']);
        $dataVencimentoFormatted = $dataVencimento ? $dataVencimento->format('Y-m-d') : null;

        $stmt = $conn->prepare("UPDATE Tarefas SET titulo=?, descricao=?, data_vencimento=? WHERE id=?");
        $stmt->bind_param("sssi", $titulo, $descricao, $dataVencimentoFormatted, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'message' => 'Tarefa atualizada com sucesso.']);
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
