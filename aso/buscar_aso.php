<?php
require_once '../config/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $response = array('status' => 'error', 'message' => 'Usuário não autenticado');
    echo json_encode($response);
    exit;
}

// Verifica se foi enviado o ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $response = array('status' => 'error', 'message' => 'ID do ASO não informado');
    echo json_encode($response);
    exit;
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

try {
    $aso_id = intval($_GET['id']);
    
    // Busca o ASO com dados do funcionário
    $sql = "SELECT 
                a.ASO_ID as aso_id,
                a.ASO_FUNCIONARIO_ID as funcionario_id,
                f.FUN_NOME_COMPLETO as funcionario_nome,
                f.FUN_CPF as funcionario_cpf,
                f.FUN_FUNCAO as funcionario_funcao,
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
                -- Calcular status de vencimento
                CASE 
                    WHEN a.ASO_DATA_VALIDADE < CURDATE() THEN 'VENCIDO'
                    WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 30 THEN 'VENCE_30_DIAS'
                    WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) BETWEEN 31 AND 60 THEN 'VENCE_60_DIAS'
                    ELSE 'VIGENTE'
                END as status_vencimento,
                DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) as dias_para_vencimento
            FROM FUN_ASO a
            INNER JOIN FUN_FUNCIONARIO f ON a.ASO_FUNCIONARIO_ID = f.FUN_ID
            WHERE a.ASO_ID = $aso_id";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) === 0) {
        throw new Exception('ASO não encontrado');
    }

    $row = mysqli_fetch_assoc($result);

    // Formatar CPF
    $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $row['funcionario_cpf']);

    $aso = array(
        'aso_id' => $row['aso_id'],
        'funcionario_id' => $row['funcionario_id'],
        'funcionario_nome' => $row['funcionario_nome'],
        'funcionario_cpf' => $cpf,
        'funcionario_funcao' => $row['funcionario_funcao'],
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

    $response = array(
        'status' => 'success',
        'aso' => $aso
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