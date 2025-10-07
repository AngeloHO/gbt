<?php
require_once '../config/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $response = array('status' => 'error', 'message' => 'Usuário não autenticado');
    echo json_encode($response);
    exit;
}

// Verifica se foi enviado o ID do funcionário
if (!isset($_GET['funcionario_id']) || empty($_GET['funcionario_id'])) {
    $response = array('status' => 'error', 'message' => 'ID do funcionário não informado');
    echo json_encode($response);
    exit;
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

try {
    $funcionario_id = intval($_GET['funcionario_id']);
    
    // Busca dados do funcionário
    $sql_funcionario = "SELECT 
                          FUN_ID as id,
                          FUN_NOME_COMPLETO as nome,
                          FUN_CPF as cpf,
                          FUN_FUNCAO as funcao,
                          FUN_STATUS as status
                        FROM FUN_FUNCIONARIO 
                        WHERE FUN_ID = $funcionario_id";

    $result_funcionario = mysqli_query($conn, $sql_funcionario);

    if (mysqli_num_rows($result_funcionario) === 0) {
        throw new Exception('Funcionário não encontrado');
    }

    $funcionario = mysqli_fetch_assoc($result_funcionario);
    
    // Formatar CPF
    $funcionario['cpf'] = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $funcionario['cpf']);
    
    // Busca todo o histórico de ASO do funcionário (incluindo cancelados)
    $sql_historico = "SELECT 
                        a.ASO_ID as aso_id,
                        a.ASO_FUNCIONARIO_ID as funcionario_id,
                        a.ASO_TIPO_EXAME as tipo_exame,
                        a.ASO_DATA_EXAME as data_exame,
                        a.ASO_DATA_VALIDADE as data_validade,
                        a.ASO_RESULTADO as resultado,
                        a.ASO_MEDICO_RESPONSAVEL as medico_responsavel,
                        a.ASO_CRM_MEDICO as crm_medico,
                        a.ASO_CLINICA_EXAME as clinica_exame,
                        a.ASO_OBSERVACOES as observacoes,
                        a.ASO_RESTRICOES as restricoes,
                        a.ASO_EXAMES_REALIZADOS as exames_realizados,
                        a.ASO_NUMERO_DOCUMENTO as numero_documento,
                        a.ASO_ARQUIVO_PATH as arquivo_path,
                        a.ASO_STATUS as status_aso,
                        a.ASO_CREATED_AT as created_at,
                        a.ASO_UPDATED_AT as updated_at,
                        -- Calcular status de vencimento apenas para ASO ativos
                        CASE 
                            WHEN a.ASO_STATUS != 'ATIVO' THEN 'HISTORICO'
                            WHEN a.ASO_DATA_VALIDADE < CURDATE() THEN 'VENCIDO'
                            WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 30 THEN 'VENCE_30_DIAS'
                            WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 60 THEN 'VENCE_60_DIAS'
                            ELSE 'VIGENTE'
                        END as status_vencimento,
                        DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) as dias_para_vencimento
                      FROM FUN_ASO a
                      WHERE a.ASO_FUNCIONARIO_ID = $funcionario_id
                      ORDER BY a.ASO_DATA_EXAME DESC, a.ASO_CREATED_AT DESC";

    $result_historico = mysqli_query($conn, $sql_historico);
    $historico = array();

    if (mysqli_num_rows($result_historico) > 0) {
        while ($row = mysqli_fetch_assoc($result_historico)) {
            $historico[] = array(
                'aso_id' => $row['aso_id'],
                'funcionario_id' => $row['funcionario_id'],
                'tipo_exame' => $row['tipo_exame'],
                'data_exame' => $row['data_exame'],
                'data_validade' => $row['data_validade'],
                'resultado' => $row['resultado'],
                'medico_responsavel' => $row['medico_responsavel'],
                'crm_medico' => $row['crm_medico'],
                'clinica_exame' => $row['clinica_exame'],
                'observacoes' => $row['observacoes'],
                'restricoes' => $row['restricoes'],
                'exames_realizados' => $row['exames_realizados'],
                'numero_documento' => $row['numero_documento'],
                'arquivo_path' => $row['arquivo_path'],
                'status_aso' => $row['status_aso'],
                'status_vencimento' => $row['status_vencimento'],
                'dias_para_vencimento' => $row['dias_para_vencimento'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            );
        }
    }

    // Estatísticas do funcionário
    $sql_stats = "SELECT 
                    COUNT(*) as total_aso,
                    SUM(CASE WHEN ASO_STATUS = 'ATIVO' THEN 1 ELSE 0 END) as aso_ativo,
                    SUM(CASE WHEN ASO_RESULTADO = 'APTO' THEN 1 ELSE 0 END) as total_apto,
                    SUM(CASE WHEN ASO_RESULTADO = 'INAPTO' THEN 1 ELSE 0 END) as total_inapto,
                    SUM(CASE WHEN ASO_RESULTADO = 'APTO_COM_RESTRICOES' THEN 1 ELSE 0 END) as total_restricoes,
                    MIN(ASO_DATA_EXAME) as primeiro_exame,
                    MAX(ASO_DATA_EXAME) as ultimo_exame
                  FROM FUN_ASO 
                  WHERE ASO_FUNCIONARIO_ID = $funcionario_id";
    
    $result_stats = mysqli_query($conn, $sql_stats);
    $stats = mysqli_fetch_assoc($result_stats);

    $response = array(
        'status' => 'success',
        'funcionario' => $funcionario,
        'historico' => $historico,
        'estatisticas' => array(
            'total_aso' => (int)$stats['total_aso'],
            'aso_ativo' => (int)$stats['aso_ativo'],
            'total_apto' => (int)$stats['total_apto'],
            'total_inapto' => (int)$stats['total_inapto'],
            'total_restricoes' => (int)$stats['total_restricoes'],
            'primeiro_exame' => $stats['primeiro_exame'],
            'ultimo_exame' => $stats['ultimo_exame']
        )
    );

} catch (Exception $e) {
    $response = array(
        'status' => 'error',
        'message' => $e->getMessage()
    );
}

// Fecha a conexão
mysqli_close($conn);

// Retorna os resultados como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>