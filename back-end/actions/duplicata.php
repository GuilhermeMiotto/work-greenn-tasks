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

if (isset($_GET['taskId']) && is_numeric($_GET['taskId'])) {
    $taskId = $_GET['taskId'];

    $sql = "SELECT *, DATE_FORMAT(data_vencimento, '%Y-%m-%d %H:%i:%s') as formatted_data_vencimento FROM tarefas WHERE id = :taskId";

    try {
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':taskId', $taskId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            $result['data_vencimento'] = $result['formatted_data_vencimento'] ?? null;
            unset($result['formatted_data_vencimento']);

            echo json_encode($result);
        } else {
            echo json_encode(['error' => 'Tarefa não encontrada']);
        }
    } catch (PDOException $e) {
        echo json_encode(['error' => 'Erro ao executar consulta SQL: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['error' => 'ID da tarefa inválido']);
}
?>
