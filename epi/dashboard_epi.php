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
    // Buscar dados para o dashboard (versão simplificada)
    $dashboard = [];
    
    // Total de EPIs
    $sql_total = "SELECT COUNT(*) as total FROM EPI_EQUIPAMENTOS WHERE epi_status = 'ativo'";
    $result_total = mysqli_query($conn, $sql_total);
    $dashboard['total_epis'] = mysqli_fetch_assoc($result_total)['total'];
    
    // EPIs ativos
    $dashboard['epis_ativos'] = $dashboard['total_epis'];
    
    // Entregas deste mês
    $sql_entregas = "SELECT COUNT(*) as total FROM EPI_ENTREGAS 
                     WHERE MONTH(entrega_data_entrega) = MONTH(CURRENT_DATE()) 
                     AND YEAR(entrega_data_entrega) = YEAR(CURRENT_DATE())";
    $result_entregas = mysqli_query($conn, $sql_entregas);
    $dashboard['entregas_mes'] = mysqli_fetch_assoc($result_entregas)['total'];
    
    $response = [
        'status' => 'success',
        'data' => $dashboard
    ];
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => 'Erro ao carregar dashboard: ' . $e->getMessage()
    ];
} finally {
    mysqli_close($conn);
}

header('Content-Type: application/json');
echo json_encode($response);
?>