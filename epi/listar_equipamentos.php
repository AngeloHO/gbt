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
    // Verificar se foi solicitado um EPI específico
    $epi_id = isset($_GET['id']) ? intval($_GET['id']) : null;
    
    if ($epi_id) {
        // Buscar EPI específico
        $sql = "SELECT 
                    epi_id as id,
                    epi_nome as nome,
                    epi_descricao as descricao,
                    epi_categoria as categoria_id,
                    epi_categoria as categoria,
                    epi_fabricante as fabricante,
                    epi_tamanho as tamanho,
                    epi_status as status,
                    epi_data_cadastro as data_cadastro
                FROM EPI_EQUIPAMENTOS
                WHERE epi_id = $epi_id";
    } else {
        // Buscar todos os equipamentos
        $sql = "SELECT 
                    epi_id as id,
                    epi_nome as nome,
                    epi_descricao as descricao,
                    epi_categoria as categoria_id,
                    epi_categoria as categoria,
                    epi_fabricante as fabricante,
                    epi_tamanho as tamanho,
                    epi_status as status,
                    epi_data_cadastro as data_cadastro
                FROM EPI_EQUIPAMENTOS
                ORDER BY epi_nome";
    }
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        throw new Exception('Erro na consulta: ' . mysqli_error($conn));
    }
    
    $equipamentos = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $equipamentos[] = $row;
    }
    
    $response = [
        'status' => 'success',
        'data' => $equipamentos
    ];
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => 'Erro ao buscar equipamentos: ' . $e->getMessage()
    ];
} finally {
    mysqli_close($conn);
}

header('Content-Type: application/json');
echo json_encode($response);
?>