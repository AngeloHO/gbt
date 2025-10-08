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

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // Recebe os dados do formulário
    $funcionario_id = mysqli_real_escape_string($conn, $_POST['funcionario_id']);
    $data_solicitacao = mysqli_real_escape_string($conn, $_POST['data_solicitacao']);
    $data_desligamento = mysqli_real_escape_string($conn, $_POST['data_desligamento']);
    $tipo_desligamento = mysqli_real_escape_string($conn, $_POST['tipo_desligamento']);
    $motivo = mysqli_real_escape_string($conn, $_POST['motivo']);
    $observacoes = mysqli_real_escape_string($conn, $_POST['observacoes']);
    
    // Dados aviso prévio
    $aviso_previo = mysqli_real_escape_string($conn, $_POST['aviso_previo']);
    $dias_aviso_previo = (int)$_POST['dias_aviso_previo'];
    
    $usuario_solicitante = $_SESSION['user_id'];
    
    // Validações básicas
    if (empty($funcionario_id) || empty($data_solicitacao) || empty($data_desligamento) || empty($tipo_desligamento)) {
        $response = array('status' => 'error', 'message' => 'Campos obrigatórios não preenchidos');
        echo json_encode($response);
        exit;
    }
    
    // Verifica se o funcionário existe e está ativo
    $sql_check = "SELECT fun_id, fun_nome_completo, fun_status FROM FUN_FUNCIONARIO WHERE fun_id = '$funcionario_id'";
    $result_check = mysqli_query($conn, $sql_check);
    
    if (mysqli_num_rows($result_check) == 0) {
        $response = array('status' => 'error', 'message' => 'Funcionário não encontrado');
        echo json_encode($response);
        exit;
    }
    
    $funcionario = mysqli_fetch_assoc($result_check);
    
    // Verifica se o funcionário está ativo
    if ($funcionario['fun_status'] !== 'ativo') {
        $response = array('status' => 'error', 'message' => 'Apenas funcionários ativos podem ser desligados');
        echo json_encode($response);
        exit;
    }
    
    // Verifica se já existe um desligamento em andamento para este funcionário
    $sql_check_desligamento = "SELECT des_id FROM DES_DESLIGAMENTO WHERE des_funcionario_id = '$funcionario_id' AND des_status IN ('solicitado', 'em_andamento')";
    $result_check_desligamento = mysqli_query($conn, $sql_check_desligamento);
    
    if (mysqli_num_rows($result_check_desligamento) > 0) {
        $response = array('status' => 'error', 'message' => 'Já existe um processo de desligamento em andamento para este funcionário');
        echo json_encode($response);
        exit;
    }
    
    // Insere o desligamento
    $sql = "INSERT INTO DES_DESLIGAMENTO (
                des_funcionario_id, 
                des_data_solicitacao, 
                des_data_desligamento, 
                des_tipo_desligamento, 
                des_motivo, 
                des_observacoes,
                des_aviso_previo,
                des_dias_aviso_previo,
                des_usuario_solicitante
            ) VALUES (
                '$funcionario_id',
                '$data_solicitacao',
                '$data_desligamento',
                '$tipo_desligamento',
                '$motivo',
                '$observacoes',
                '$aviso_previo',
                '$dias_aviso_previo',
                '$usuario_solicitante'
            )";
    
    if (mysqli_query($conn, $sql)) {
        $desligamento_id = mysqli_insert_id($conn);
        
        // Registra no histórico
        $sql_historico = "INSERT INTO DES_HISTORICO (his_desligamento_id, his_acao, his_usuario_id, his_observacoes) 
                         VALUES ('$desligamento_id', 'criacao', '$usuario_solicitante', 'Desligamento cadastrado')";
        mysqli_query($conn, $sql_historico);
        
        $response = array(
            'status' => 'success', 
            'message' => 'Desligamento cadastrado com sucesso!',
            'desligamento_id' => $desligamento_id
        );
    } else {
        $response = array('status' => 'error', 'message' => 'Erro ao cadastrar desligamento: ' . mysqli_error($conn));
    }
    
    echo json_encode($response);
    
} else {
    $response = array('status' => 'error', 'message' => 'Método não permitido');
    echo json_encode($response);
}

mysqli_close($conn);
?>