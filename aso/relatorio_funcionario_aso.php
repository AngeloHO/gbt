<?php
require_once '../config/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}

// Verifica se foi enviado o ID do funcionário
if (!isset($_GET['funcionario_id']) || empty($_GET['funcionario_id'])) {
    echo "ID do funcionário não informado";
    exit;
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

$funcionario_id = intval($_GET['funcionario_id']);

// Busca dados do funcionário
$sql_funcionario = "SELECT 
                      FUN_NOME_COMPLETO as nome,
                      FUN_CPF as cpf,
                      FUN_FUNCAO as funcao,
                      FUN_DATA_ADMISSAO as data_admissao,
                      FUN_STATUS as status
                    FROM FUN_FUNCIONARIO 
                    WHERE FUN_ID = $funcionario_id";

$result_funcionario = mysqli_query($conn, $sql_funcionario);

if (mysqli_num_rows($result_funcionario) === 0) {
    echo "Funcionário não encontrado";
    exit;
}

$funcionario = mysqli_fetch_assoc($result_funcionario);

// Busca histórico completo
$sql_historico = "SELECT 
                    a.*,
                    CASE 
                        WHEN a.ASO_STATUS != 'ATIVO' THEN 'HISTÓRICO'
                        WHEN a.ASO_DATA_VALIDADE < CURDATE() THEN 'VENCIDO'
                        WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 30 THEN 'VENCE EM 30 DIAS'
                        WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 60 THEN 'VENCE EM 60 DIAS'
                        ELSE 'VIGENTE'
                    END as status_vencimento
                  FROM FUN_ASO a
                  WHERE a.ASO_FUNCIONARIO_ID = $funcionario_id
                  ORDER BY a.ASO_DATA_EXAME DESC";

$result_historico = mysqli_query($conn, $sql_historico);

// Define headers para download do PDF (ou CSV)
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="historico_aso_' . preg_replace('/[^a-zA-Z0-9]/', '_', $funcionario['nome']) . '_' . date('Y-m-d') . '.csv"');

// Abre output para escrita
$output = fopen('php://output', 'w');

// Escreve BOM para UTF-8
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Cabeçalho do relatório
fputcsv($output, array('RELATÓRIO COMPLETO DE ASO - ' . strtoupper($funcionario['nome'])), ';');
fputcsv($output, array(''), ';'); // Linha em branco

// Dados do funcionário
fputcsv($output, array('DADOS DO FUNCIONÁRIO'), ';');
fputcsv($output, array('Nome', $funcionario['nome']), ';');
fputcsv($output, array('CPF', preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $funcionario['cpf'])), ';');
fputcsv($output, array('Função', $funcionario['funcao']), ';');
fputcsv($output, array('Data Admissão', $funcionario['data_admissao'] ? date('d/m/Y', strtotime($funcionario['data_admissao'])) : 'Não informado'), ';');
fputcsv($output, array('Status', strtoupper($funcionario['status'])), ';');
fputcsv($output, array(''), ';'); // Linha em branco

// Cabeçalhos do histórico de ASO
$headers = array(
    'Data Exame',
    'Tipo Exame',
    'Data Validade',
    'Resultado',
    'Status',
    'Médico Responsável',
    'CRM',
    'Clínica',
    'Número Documento',
    'Exames Realizados',
    'Restrições',
    'Observações',
    'Status ASO',
    'Cadastrado em'
);

fputcsv($output, array('HISTÓRICO DE ASO'), ';');
fputcsv($output, $headers, ';');

// Mapear resultados para texto legível
$resultado_map = array(
    'APTO' => 'Apto',
    'INAPTO' => 'Inapto',
    'APTO_COM_RESTRICOES' => 'Apto com Restrições'
);

// Escreve os dados do histórico
if (mysqli_num_rows($result_historico) > 0) {
    while ($row = mysqli_fetch_assoc($result_historico)) {
        $data_exame = date('d/m/Y', strtotime($row['ASO_DATA_EXAME']));
        $data_validade = date('d/m/Y', strtotime($row['ASO_DATA_VALIDADE']));
        $resultado = isset($resultado_map[$row['ASO_RESULTADO']]) ? $resultado_map[$row['ASO_RESULTADO']] : $row['ASO_RESULTADO'];
        $created_at = date('d/m/Y H:i', strtotime($row['ASO_CREATED_AT']));
        
        $linha = array(
            $data_exame,
            $row['ASO_TIPO_EXAME'],
            $data_validade,
            $resultado,
            $row['status_vencimento'],
            $row['ASO_MEDICO_RESPONSAVEL'],
            $row['ASO_CRM_MEDICO'] ?: 'Não informado',
            $row['ASO_CLINICA_EXAME'] ?: 'Não informado',
            $row['ASO_NUMERO_DOCUMENTO'] ?: 'Não informado',
            $row['ASO_EXAMES_REALIZADOS'] ?: 'Não informado',
            $row['ASO_RESTRICOES'] ?: 'Nenhuma',
            $row['ASO_OBSERVACOES'] ?: 'Nenhuma',
            $row['ASO_STATUS'],
            $created_at
        );
        
        fputcsv($output, $linha, ';');
    }
} else {
    fputcsv($output, array('Nenhum ASO encontrado para este funcionário'), ';');
}

// Estatísticas
fputcsv($output, array(''), ';'); // Linha em branco
fputcsv($output, array('ESTATÍSTICAS'), ';');

$sql_stats = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN ASO_RESULTADO = 'APTO' THEN 1 ELSE 0 END) as apto,
                SUM(CASE WHEN ASO_RESULTADO = 'INAPTO' THEN 1 ELSE 0 END) as inapto,
                SUM(CASE WHEN ASO_RESULTADO = 'APTO_COM_RESTRICOES' THEN 1 ELSE 0 END) as restricoes,
                SUM(CASE WHEN ASO_STATUS = 'ATIVO' THEN 1 ELSE 0 END) as ativo
              FROM FUN_ASO 
              WHERE ASO_FUNCIONARIO_ID = $funcionario_id";

$result_stats = mysqli_query($conn, $sql_stats);
$stats = mysqli_fetch_assoc($result_stats);

fputcsv($output, array('Total de ASO', $stats['total']), ';');
fputcsv($output, array('ASO Ativos', $stats['ativo']), ';');
fputcsv($output, array('Resultados Apto', $stats['apto']), ';');
fputcsv($output, array('Resultados Inapto', $stats['inapto']), ';');
fputcsv($output, array('Resultados com Restrições', $stats['restricoes']), ';');

fputcsv($output, array(''), ';'); // Linha em branco
fputcsv($output, array('Relatório gerado em: ' . date('d/m/Y H:i:s')), ';');
fputcsv($output, array('Sistema Gebert - Controle de ASO'), ';');

fclose($output);
mysqli_close($conn);
exit;
?>