<?php
require_once '../config/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $response = array('status' => 'error', 'message' => 'Usuário não autenticado');
    echo json_encode($response);
    exit;
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

try {
    // Total de funcionários ativos
    $sql_total_funcionarios = "SELECT COUNT(*) as total FROM FUN_FUNCIONARIO WHERE FUN_STATUS = 'ativo'";
    $result_total = mysqli_query($conn, $sql_total_funcionarios);
    $total_funcionarios = $result_total ? mysqli_fetch_assoc($result_total)['total'] : 0;

    // Usar uma única query para calcular todas as estatísticas
    $sql_estatisticas = "SELECT 
        COUNT(*) as total_aso,
        SUM(CASE WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) < 0 THEN 1 ELSE 0 END) as vencidos,
        SUM(CASE WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) >= 0 AND DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 30 THEN 1 ELSE 0 END) as vence30,
        SUM(CASE WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) > 30 THEN 1 ELSE 0 END) as vigentes
    FROM FUN_ASO a 
    INNER JOIN FUN_FUNCIONARIO f ON a.ASO_FUNCIONARIO_ID = f.FUN_ID 
    WHERE f.FUN_STATUS = 'ativo' AND a.ASO_STATUS = 'ATIVO'";
    
    $result_estatisticas = mysqli_query($conn, $sql_estatisticas);
    
    if ($result_estatisticas) {
        $data = mysqli_fetch_assoc($result_estatisticas);
        $vigentes = (int)$data['vigentes'];
        $vence30 = (int)$data['vence30'];
        $vencidos = (int)$data['vencidos'];
    } else {
        $vigentes = 0;
        $vence30 = 0;
        $vencidos = 0;
    }

    // Funcionários sem ASO
    $sql_sem_aso = "SELECT COUNT(*) as total 
                    FROM FUN_FUNCIONARIO f 
                    LEFT JOIN FUN_ASO a ON f.FUN_ID = a.ASO_FUNCIONARIO_ID AND a.ASO_STATUS = 'ATIVO'
                    WHERE f.FUN_STATUS = 'ativo' AND a.ASO_ID IS NULL";
    $result_sem_aso = mysqli_query($conn, $sql_sem_aso);
    $sem_aso = $result_sem_aso ? mysqli_fetch_assoc($result_sem_aso)['total'] : 0;

    $response = array(
        'status' => 'success',
        'vigentes' => (int)$vigentes,
        'vence30' => (int)$vence30,
        'vencidos' => (int)$vencidos,
        'total_funcionarios' => (int)$total_funcionarios,
        'sem_aso' => (int)$sem_aso
    );

} catch (Exception $e) {
    $response = array(
        'status' => 'error',
        'message' => 'Erro ao buscar estatísticas: ' . $e->getMessage()
    );
}

// Fecha a conexão
mysqli_close($conn);

// Retorna os resultados como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>