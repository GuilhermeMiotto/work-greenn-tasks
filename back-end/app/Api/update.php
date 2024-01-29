<?php
include_once 'config.php';

$data = json_decode(file_get_contents('php://input'));

$id = $data->id;
$nome = $data->nome;

$sql = "UPDATE tarefas SET nome='$nome' WHERE id=$id";

if ($conn->query($sql) === TRUE) {
    echo json_encode(array("message" => "Tarefa atualizada com sucesso"));
} else {
    echo json_encode(array("error" => "Erro ao atualizar a tarefa: " . $conn->error));
}

$conn->close();