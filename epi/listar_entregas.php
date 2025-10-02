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
    // Buscar todas as entregas com informações do funcionário e EPI
    $sql = "SELECT 
                e.entrega_id as id,
                e.entrega_quantidade as quantidade,
                e.entrega_data_entrega as data_entrega,
                e.entrega_data_prevista_devolucao as data_prevista,
                e.entrega_data_devolucao as data_devolucao,
                e.entrega_motivo_entrega as motivo,
                e.entrega_observacoes as observacoes,
                e.entrega_assinatura_funcionario as assinatura,
                e.entrega_status as status,
                e.entrega_data_cadastro as data_cadastro,
                f.fun_nome_completo as funcionario_nome,
                f.fun_cpf as funcionario_cpf,
                ep.epi_nome as epi_nome,
                ep.epi_categoria as epi_categoria
            FROM EPI_ENTREGAS e
            INNER JOIN FUN_FUNCIONARIO f ON e.entrega_funcionario_id = f.fun_id
            INNER JOIN EPI_EQUIPAMENTOS ep ON e.entrega_epi_id = ep.epi_id
            ORDER BY e.entrega_data_entrega DESC";
    
    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        throw new Exception('Erro na consulta: ' . mysqli_error($conn));
    }
    
    $entregas = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $entregas[] = $row;
    }
    
    $response = [
        'status' => 'success',
        'data' => $entregas
    ];
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => 'Erro ao buscar entregas: ' . $e->getMessage()
    ];
} finally {
    mysqli_close($conn);
}

header('Content-Type: application/json');
echo json_encode($response);
?>