<?php
include_once 'config.php';

$data = json_decode(file_get_contents('php://input'));

$nome = $data->nome;

$sql = "INSERT INTO tarefas (nome) VALUES ('$nome')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(array("message" => "Tarefa criada com sucesso"));
} else {
    echo json_encode(array("error" => "Erro ao criar a tarefa: " . $conn->error));
}

$conn->close();