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

// Parâmetros do relatório
$tipo_relatorio = $_GET['tipo'] ?? 'geral';
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$status = $_GET['status'] ?? '';
$tipo_desligamento = $_GET['tipo_desligamento'] ?? '';
$formato = $_GET['formato'] ?? 'json'; // json, csv, pdf

// Constrói a query base
$where = "WHERE 1=1";

if (!empty($data_inicio)) {
    $where .= " AND d.des_data_desligamento >= '$data_inicio'";
}

if (!empty($data_fim)) {
    $where .= " AND d.des_data_desligamento <= '$data_fim'";
}

if (!empty($status)) {
    $where .= " AND d.des_status = '$status'";
}

if (!empty($tipo_desligamento)) {
    $where .= " AND d.des_tipo_desligamento = '$tipo_desligamento'";
}

// Diferentes tipos de relatório
switch ($tipo_relatorio) {
    case 'geral':
        $sql = "SELECT 
                    d.des_id,
                    f.fun_nome_completo,
                    f.fun_cpf,
                    f.fun_funcao,
                    d.des_data_solicitacao,
                    d.des_data_desligamento,
                    d.des_tipo_desligamento,
                    d.des_status,
                    d.des_valor_total,
                    d.des_motivo,
                    u.usu_nome as solicitante
                FROM DES_DESLIGAMENTO d
                INNER JOIN FUN_FUNCIONARIO f ON d.des_funcionario_id = f.fun_id
                LEFT JOIN usu_usuario u ON d.des_usuario_solicitante = u.usu_id
                $where
                ORDER BY d.des_data_criacao DESC";
        break;
        
    case 'por_tipo':
        $sql = "SELECT 
                    d.des_tipo_desligamento,
                    COUNT(*) as total,
                    SUM(d.des_valor_total) as valor_total,
                    AVG(d.des_valor_total) as valor_medio
                FROM DES_DESLIGAMENTO d
                INNER JOIN FUN_FUNCIONARIO f ON d.des_funcionario_id = f.fun_id
                $where
                GROUP BY d.des_tipo_desligamento
                ORDER BY total DESC";
        break;
        
    case 'por_mes':
        $sql = "SELECT 
                    DATE_FORMAT(d.des_data_desligamento, '%Y-%m') as mes,
                    COUNT(*) as total,
                    SUM(d.des_valor_total) as valor_total,
                    AVG(d.des_valor_total) as valor_medio
                FROM DES_DESLIGAMENTO d
                INNER JOIN FUN_FUNCIONARIO f ON d.des_funcionario_id = f.fun_id
                $where
                GROUP BY DATE_FORMAT(d.des_data_desligamento, '%Y-%m')
                ORDER BY mes DESC";
        break;
        
    default:
        $response = array('status' => 'error', 'message' => 'Tipo de relatório não reconhecido');
        echo json_encode($response);
        exit;
}

$result = mysqli_query($conn, $sql);

if (!$result) {
    $response = array('status' => 'error', 'message' => 'Erro ao executar consulta: ' . mysqli_error($conn));
    echo json_encode($response);
    exit;
}

$dados = array();
while ($row = mysqli_fetch_assoc($result)) {
    // Formatações específicas por tipo de relatório
    if ($tipo_relatorio === 'geral') {
        // Mapeamento de tipos de desligamento
        $tipos_desligamento = array(
            'demissao_sem_justa_causa' => 'Demissão sem Justa Causa',
            'demissao_com_justa_causa' => 'Demissão com Justa Causa',
            'pedido_demissao' => 'Pedido de Demissão',
            'termino_contrato' => 'Término de Contrato',
            'aposentadoria' => 'Aposentadoria',
            'morte' => 'Morte',
            'acordo_mutuo' => 'Acordo Mútuo'
        );
        
        $status_texto = array(
            'solicitado' => 'Solicitado',
            'em_andamento' => 'Em Andamento',
            'finalizado' => 'Finalizado',
            'cancelado' => 'Cancelado'
        );
        
        $row['fun_cpf'] = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $row['fun_cpf']);
        $row['des_data_solicitacao'] = date('d/m/Y', strtotime($row['des_data_solicitacao']));
        $row['des_data_desligamento'] = date('d/m/Y', strtotime($row['des_data_desligamento']));
        $row['des_tipo_desligamento'] = $tipos_desligamento[$row['des_tipo_desligamento']] ?? $row['des_tipo_desligamento'];
        $row['des_status'] = $status_texto[$row['des_status']] ?? $row['des_status'];
        $row['des_valor_total'] = 'R$ ' . number_format($row['des_valor_total'], 2, ',', '.');
    } elseif (in_array($tipo_relatorio, ['por_tipo', 'por_mes'])) {
        // Formatação para relatórios agregados
        if (isset($row['valor_total'])) {
            $row['valor_total'] = 'R$ ' . number_format($row['valor_total'], 2, ',', '.');
        }
        if (isset($row['valor_medio'])) {
            $row['valor_medio'] = 'R$ ' . number_format($row['valor_medio'], 2, ',', '.');
        }
        if ($tipo_relatorio === 'por_tipo') {
            $tipos_desligamento = array(
                'demissao_sem_justa_causa' => 'Demissão sem Justa Causa',
                'demissao_com_justa_causa' => 'Demissão com Justa Causa',
                'pedido_demissao' => 'Pedido de Demissão',
                'termino_contrato' => 'Término de Contrato',
                'aposentadoria' => 'Aposentadoria',
                'morte' => 'Morte',
                'acordo_mutuo' => 'Acordo Mútuo'
            );
            $row['des_tipo_desligamento'] = $tipos_desligamento[$row['des_tipo_desligamento']] ?? $row['des_tipo_desligamento'];
        }
    }
    
    $dados[] = $row;
}

// Retorna os dados conforme o formato solicitado
if ($formato === 'csv') {
    // Gera CSV
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="relatorio_desligamentos_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // Cabeçalho CSV
    if (!empty($dados)) {
        fputcsv($output, array_keys($dados[0]));
        
        // Dados
        foreach ($dados as $row) {
            fputcsv($output, $row);
        }
    }
    
    fclose($output);
    
} elseif ($formato === 'pdf') {
    // Para PDF seria necessário uma biblioteca como TCPDF ou mPDF
    // Por enquanto, retorna erro
    $response = array('status' => 'error', 'message' => 'Formato PDF não implementado');
    echo json_encode($response);
    
} else {
    // Retorna JSON (padrão)
    $response = array(
        'status' => 'success',
        'tipo_relatorio' => $tipo_relatorio,
        'filtros' => array(
            'data_inicio' => $data_inicio,
            'data_fim' => $data_fim,
            'status' => $status,
            'tipo_desligamento' => $tipo_desligamento
        ),
        'total_registros' => count($dados),
        'data' => $dados
    );
    
    echo json_encode($response);
}

mysqli_close($conn);
?>