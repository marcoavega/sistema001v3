<?php
// api/logs.php
ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../models/Database.php';
session_start();

$db = (new Database())->getConnection();

// 🧠 Obtener parámetros GET con valores por defecto
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$size = isset($_GET['size']) ? max(1, intval($_GET['size'])) : 999999999999999;
$offset = ($page - 1) * $size;

try {
    // Obtener el total de registros
    $countStmt = $db->prepare("SELECT COUNT(*) as total FROM user_logs");
    $countStmt->execute();
    $total = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
    $lastPage = ceil($total / $size);

    // Consulta con JOIN para traer nombre de usuario
    $sql = "
        SELECT 
            ul.id,
            ul.user_id,
            u.username,
            ul.action,
            ul.`timestamp`
        FROM user_logs ul
        LEFT JOIN users u ON ul.user_id = u.user_id
        ORDER BY ul.`timestamp` DESC
        LIMIT :size OFFSET :offset
    ";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':size', $size, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Respuesta con estructura compatible con Tabulator remoto
    echo json_encode([
        'data' => $logs,
        'last_page' => $lastPage
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Excepción de base de datos',
        'message' => $e->getMessage()
    ]);
}
