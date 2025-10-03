<?php
require_once '../config/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado']);
    exit();
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

try {
    // Buscar todas as categorias ativas
    $sql = "SELECT categoria_id as id, categoria_nome as nome, categoria_descricao as descricao 
            FROM EPI_CATEGORIAS 
            WHERE categoria_status = 'ativo' 
            ORDER BY categoria_nome";
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        throw new Exception('Erro na consulta: ' . mysqli_error($conn));
    }
    
    $categorias = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $categorias[] = $row;
    }
    
    $response = [
        'status' => 'success',
        'data' => $categorias
    ];
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => 'Erro ao buscar categorias: ' . $e->getMessage()
    ];
} finally {
    mysqli_close($conn);
}

header('Content-Type: application/json');
echo json_encode($response);
?>