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

// Verifica se foi fornecido um ID do desligamento
if (!isset($_POST['desligamento_id']) || empty($_POST['desligamento_id'])) {
    $response = array('status' => 'error', 'message' => 'ID do desligamento não fornecido');
    echo json_encode($response);
    exit;
}

$desligamento_id = intval($_POST['desligamento_id']);
$funcionario_id = intval($_POST['funcionario_id']);
$data_solicitacao = $_POST['data_solicitacao'];
$data_desligamento = $_POST['data_desligamento'];
$tipo_desligamento = $_POST['tipo_desligamento'];
$aviso_previo = $_POST['aviso_previo'];
$dias_aviso_previo = intval($_POST['dias_aviso_previo']);
$motivo = strtoupper(trim($_POST['motivo']));
$observacoes = strtoupper(trim($_POST['observacoes']));
$usuario_id = $_SESSION['user_id'];

// Validações básicas
if (empty($funcionario_id)) {
    $response = array('status' => 'error', 'message' => 'Funcionário é obrigatório');
    echo json_encode($response);
    exit;
}

if (empty($data_solicitacao)) {
    $response = array('status' => 'error', 'message' => 'Data de solicitação é obrigatória');
    echo json_encode($response);
    exit;
}

if (empty($data_desligamento)) {
    $response = array('status' => 'error', 'message' => 'Data de desligamento é obrigatória');
    echo json_encode($response);
    exit;
}

if (empty($tipo_desligamento)) {
    $response = array('status' => 'error', 'message' => 'Tipo de desligamento é obrigatório');
    echo json_encode($response);
    exit;
}

// Verificar se o desligamento existe e pode ser editado
$sql_verificar = "SELECT des_status FROM DES_DESLIGAMENTO WHERE des_id = '$desligamento_id'";
$result_verificar = mysqli_query($conn, $sql_verificar);

if (mysqli_num_rows($result_verificar) == 0) {
    $response = array('status' => 'error', 'message' => 'Desligamento não encontrado');
    echo json_encode($response);
    exit;
}

$desligamento_atual = mysqli_fetch_assoc($result_verificar);

// Verificar se o desligamento pode ser editado (apenas se não estiver finalizado)
if ($desligamento_atual['des_status'] === 'finalizado') {
    $response = array('status' => 'error', 'message' => 'Não é possível editar um desligamento finalizado');
    echo json_encode($response);
    exit;
}

// Verificar se o funcionário existe e está ativo
$sql_funcionario = "SELECT fun_nome_completo, fun_status FROM FUN_FUNCIONARIO WHERE fun_id = '$funcionario_id'";
$result_funcionario = mysqli_query($conn, $sql_funcionario);

if (mysqli_num_rows($result_funcionario) == 0) {
    $response = array('status' => 'error', 'message' => 'Funcionário não encontrado');
    echo json_encode($response);
    exit;
}

$funcionario = mysqli_fetch_assoc($result_funcionario);

// Para funcionários que já estão inativos, verificar se este é o mesmo desligamento que os desligou
if ($funcionario['fun_status'] === 'inativo') {
    $sql_check_desligamento = "SELECT des_id FROM DES_DESLIGAMENTO WHERE des_funcionario_id = '$funcionario_id' AND des_status = 'finalizado'";
    $result_check = mysqli_query($conn, $sql_check_desligamento);
    
    if (mysqli_num_rows($result_check) > 0) {
        $desligamento_finalizado = mysqli_fetch_assoc($result_check);
        if ($desligamento_finalizado['des_id'] != $desligamento_id) {
            $response = array('status' => 'error', 'message' => 'Este funcionário já possui um desligamento finalizado');
            echo json_encode($response);
            exit;
        }
    }
}

// Validar datas
$data_hoje = date('Y-m-d');
if ($data_solicitacao > $data_hoje) {
    $response = array('status' => 'error', 'message' => 'Data de solicitação não pode ser futura');
    echo json_encode($response);
    exit;
}

if ($data_desligamento < $data_solicitacao) {
    $response = array('status' => 'error', 'message' => 'Data de desligamento não pode ser anterior à data de solicitação');
    echo json_encode($response);
    exit;
}

// Iniciar transação
mysqli_begin_transaction($conn);

try {
    // Atualizar o desligamento
    $sql_update = "UPDATE DES_DESLIGAMENTO SET 
                      des_funcionario_id = '$funcionario_id',
                      des_data_solicitacao = '$data_solicitacao',
                      des_data_desligamento = '$data_desligamento',
                      des_tipo_desligamento = '$tipo_desligamento',
                      des_aviso_previo = '$aviso_previo',
                      des_dias_aviso_previo = '$dias_aviso_previo',
                      des_motivo = '$motivo',
                      des_observacoes = '$observacoes',
                      des_data_atualizacao = NOW()
                   WHERE des_id = '$desligamento_id'";
    
    if (!mysqli_query($conn, $sql_update)) {
        throw new Exception('Erro ao atualizar desligamento: ' . mysqli_error($conn));
    }

    // Registrar no histórico
    $acao_historico = "DADOS ATUALIZADOS";
    $observacoes_historico = "DADOS DO DESLIGAMENTO FORAM ATUALIZADOS";
    
    $sql_historico = "INSERT INTO DES_HISTORICO (
                         his_desligamento_id, 
                         his_acao, 
                         his_usuario_id, 
                         his_observacoes, 
                         his_data_acao
                      ) VALUES (
                         '$desligamento_id',
                         '$acao_historico',
                         '$usuario_id',
                         '$observacoes_historico',
                         NOW()
                      )";
    
    if (!mysqli_query($conn, $sql_historico)) {
        throw new Exception('Erro ao registrar histórico: ' . mysqli_error($conn));
    }

    // Confirmar transação
    mysqli_commit($conn);
    
    $response = array(
        'status' => 'success', 
        'message' => 'Desligamento atualizado com sucesso!'
    );

} catch (Exception $e) {
    // Desfazer transação em caso de erro
    mysqli_rollback($conn);
    
    $response = array(
        'status' => 'error', 
        'message' => $e->getMessage()
    );
}

// Fechar conexão
mysqli_close($conn);

// Retornar resposta
echo json_encode($response);
?>