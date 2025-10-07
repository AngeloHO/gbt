<?php
require_once '../config/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

// Parâmetros de filtro
$filtro_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$filtro_tipo = isset($_GET['tipo']) ? mysqli_real_escape_string($conn, $_GET['tipo']) : '';
$data_inicio = isset($_GET['data_inicio']) ? mysqli_real_escape_string($conn, $_GET['data_inicio']) : '';
$data_fim = isset($_GET['data_fim']) ? mysqli_real_escape_string($conn, $_GET['data_fim']) : '';

// Monta a query com filtros
$where = "WHERE 1=1";

if (!empty($filtro_status)) {
    $where .= " AND STATUS_VENCIMENTO = '$filtro_status'";
}

if (!empty($filtro_tipo)) {
    $where .= " AND ASO_TIPO_EXAME = '$filtro_tipo'";
}

if (!empty($data_inicio)) {
    $where .= " AND ASO_DATA_EXAME >= '$data_inicio'";
}

if (!empty($data_fim)) {
    $where .= " AND ASO_DATA_EXAME <= '$data_fim'";
}

$sql = "SELECT 
            FUNCIONARIO_NOME,
            FUNCIONARIO_CPF,
            FUNCIONARIO_FUNCAO,
            ASO_TIPO_EXAME,
            ASO_DATA_EXAME,
            ASO_DATA_VALIDADE,
            ASO_RESULTADO,
            ASO_MEDICO_RESPONSAVEL,
            ASO_CRM_MEDICO,
            ASO_CLINICA_EXAME,
            STATUS_VENCIMENTO,
            DIAS_PARA_VENCIMENTO
        FROM VW_ASO_FUNCIONARIO 
        $where
        ORDER BY ASO_DATA_VALIDADE ASC, FUNCIONARIO_NOME ASC";

$result = mysqli_query($conn, $sql);

// Define headers para download do CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="relatorio_aso_' . date('Y-m-d') . '.csv"');

// Abre output para escrita
$output = fopen('php://output', 'w');

// Escreve BOM para UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Cabeçalhos do CSV
$headers = array(
    'Funcionário',
    'CPF',
    'Função',
    'Tipo Exame',
    'Data Exame',
    'Data Validade',
    'Resultado',
    'Médico Responsável',
    'CRM',
    'Clínica',
    'Status Vencimento',
    'Dias para Vencimento'
);

fputcsv($output, $headers, ';');

// Mapear status e resultados para texto legível
$status_map = array(
    'VIGENTE' => 'Vigente',
    'VENCE_30_DIAS' => 'Vence em 30 dias',
    'VENCE_60_DIAS' => 'Vence em 60 dias',
    'VENCIDO' => 'Vencido'
);

$resultado_map = array(
    'APTO' => 'Apto',
    'INAPTO' => 'Inapto',
    'APTO_COM_RESTRICOES' => 'Apto com Restrições'
);

// Escreve os dados
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Formatar CPF
        $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $row['FUNCIONARIO_CPF']);
        
        // Formatar datas
        $data_exame = date('d/m/Y', strtotime($row['ASO_DATA_EXAME']));
        $data_validade = date('d/m/Y', strtotime($row['ASO_DATA_VALIDADE']));
        
        // Mapear status e resultado
        $status = isset($status_map[$row['STATUS_VENCIMENTO']]) ? $status_map[$row['STATUS_VENCIMENTO']] : $row['STATUS_VENCIMENTO'];
        $resultado = isset($resultado_map[$row['ASO_RESULTADO']]) ? $resultado_map[$row['ASO_RESULTADO']] : $row['ASO_RESULTADO'];
        
        $linha = array(
            $row['FUNCIONARIO_NOME'],
            $cpf,
            $row['FUNCIONARIO_FUNCAO'],
            $row['ASO_TIPO_EXAME'],
            $data_exame,
            $data_validade,
            $resultado,
            $row['ASO_MEDICO_RESPONSAVEL'],
            $row['ASO_CRM_MEDICO'],
            $row['ASO_CLINICA_EXAME'],
            $status,
            $row['DIAS_PARA_VENCIMENTO']
        );
        
        fputcsv($output, $linha, ';');
    }
}

fclose($output);
mysqli_close($conn);
exit;
?>