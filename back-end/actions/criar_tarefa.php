<?php

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Credentials: true');

$servername = "localhost";
$username = "guilh";
$password = "1234";
$dbname = "taskForGreenn";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conn->connect_error);
}

$data = json_decode(file_get_contents("php://input"));


if (empty($data->titulo) || empty($data->descricao) || empty($data->data_vencimento)) {
    echo json_encode(['error' => 'Todos os campos são obrigatórios.']);
    exit;
}

$titulo = $conn->real_escape_string($data->titulo);
$descricao = $conn->real_escape_string($data->descricao);


$dataVencimento = DateTime::createFromFormat('d/m/Y', $data->data_vencimento);
$dataVencimentoFormatted = $dataVencimento ? $dataVencimento->format('Y-m-d') : null;


$sql = "INSERT INTO Tarefas (titulo, descricao, data_vencimento) VALUES ('$titulo', '$descricao', '$dataVencimentoFormatted')";
$result = $conn->query($sql);

if ($result) {

    $newTaskId = $conn->insert_id;

    $sql = "SELECT * FROM Tarefas WHERE id = $newTaskId";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo json_encode($row);
    } else {
        echo json_encode(['error' => 'Erro ao buscar a tarefa recém-criada.']);
    }
} else {
    echo json_encode(['error' => 'Erro ao criar a tarefa.']);
}

$conn->close();
