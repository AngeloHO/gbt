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

// Consultas para estatísticas
$stats = array();

// Total de desligamentos
$sql_total = "SELECT COUNT(*) as total FROM DES_DESLIGAMENTO";
$result_total = mysqli_query($conn, $sql_total);
$stats['total_desligamentos'] = mysqli_fetch_assoc($result_total)['total'];

// Desligamentos por status
$sql_status = "SELECT des_status, COUNT(*) as total FROM DES_DESLIGAMENTO GROUP BY des_status";
$result_status = mysqli_query($conn, $sql_status);
$stats['por_status'] = array();
while ($row = mysqli_fetch_assoc($result_status)) {
    $stats['por_status'][$row['des_status']] = $row['total'];
}

// Desligamentos por tipo
$sql_tipo = "SELECT des_tipo_desligamento, COUNT(*) as total FROM DES_DESLIGAMENTO GROUP BY des_tipo_desligamento";
$result_tipo = mysqli_query($conn, $sql_tipo);
$stats['por_tipo'] = array();
while ($row = mysqli_fetch_assoc($result_tipo)) {
    $stats['por_tipo'][$row['des_tipo_desligamento']] = $row['total'];
}

// Desligamentos no mês atual
$sql_mes = "SELECT COUNT(*) as total FROM DES_DESLIGAMENTO WHERE MONTH(des_data_desligamento) = MONTH(CURDATE()) AND YEAR(des_data_desligamento) = YEAR(CURDATE())";
$result_mes = mysqli_query($conn, $sql_mes);
$stats['mes_atual'] = mysqli_fetch_assoc($result_mes)['total'];

// Desligamentos no ano atual
$sql_ano = "SELECT COUNT(*) as total FROM DES_DESLIGAMENTO WHERE YEAR(des_data_desligamento) = YEAR(CURDATE())";
$result_ano = mysqli_query($conn, $sql_ano);
$stats['ano_atual'] = mysqli_fetch_assoc($result_ano)['total'];

// Desligamentos pendentes (solicitado + em_andamento)
$sql_pendentes = "SELECT COUNT(*) as total FROM DES_DESLIGAMENTO WHERE des_status IN ('solicitado', 'em_andamento')";
$result_pendentes = mysqli_query($conn, $sql_pendentes);
$stats['pendentes'] = mysqli_fetch_assoc($result_pendentes)['total'];

// Média de dias entre solicitação e desligamento
$sql_media_dias = "SELECT AVG(DATEDIFF(des_data_desligamento, des_data_solicitacao)) as media FROM DES_DESLIGAMENTO WHERE des_status = 'finalizado'";
$result_media_dias = mysqli_query($conn, $sql_media_dias);
$media_dias = mysqli_fetch_assoc($result_media_dias)['media'];
$stats['media_dias_processo'] = $media_dias ? round($media_dias, 1) : 0;

// Desligamentos por mês nos últimos 12 meses
$sql_por_mes = "SELECT 
                   DATE_FORMAT(des_data_desligamento, '%Y-%m') as mes,
                   COUNT(*) as total
                FROM DES_DESLIGAMENTO 
                WHERE des_data_desligamento >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH)
                GROUP BY DATE_FORMAT(des_data_desligamento, '%Y-%m')
                ORDER BY mes";
$result_por_mes = mysqli_query($conn, $sql_por_mes);
$stats['ultimos_12_meses'] = array();
while ($row = mysqli_fetch_assoc($result_por_mes)) {
    $stats['ultimos_12_meses'][] = array(
        'mes' => $row['mes'],
        'total' => $row['total']
    );
}

// Top 5 motivos de desligamento
$sql_motivos = "SELECT 
                   des_tipo_desligamento,
                   COUNT(*) as total
                FROM DES_DESLIGAMENTO 
                GROUP BY des_tipo_desligamento
                ORDER BY total DESC
                LIMIT 5";
$result_motivos = mysqli_query($conn, $sql_motivos);
$stats['top_motivos'] = array();
while ($row = mysqli_fetch_assoc($result_motivos)) {
    $stats['top_motivos'][] = array(
        'tipo' => $row['des_tipo_desligamento'],
        'total' => $row['total']
    );
}

$response = array(
    'status' => 'success',
    'data' => $stats
);

echo json_encode($response);

mysqli_close($conn);
?>