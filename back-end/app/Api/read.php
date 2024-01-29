<?php
include_once 'config.php';

$result = $conn->query("SELECT * FROM tarefas");

$tarefas = array();
while ($row = $result->fetch_assoc()) {
    $tarefas[] = $row;
}

echo json_encode($tarefas);

$conn->close();