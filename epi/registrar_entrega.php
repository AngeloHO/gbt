<?php
require_once '../config/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado']);
    exit();
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit();
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

try {
    // Validar campos obrigatórios
    $campos_obrigatorios = ['funcionario_id', 'epi_id', 'data_entrega'];
    foreach ($campos_obrigatorios as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("Campo '$campo' é obrigatório");
        }
    }
    
    $funcionario_id = intval($_POST['funcionario_id']);
    $epi_id = intval($_POST['epi_id']);
    $data_entrega = $_POST['data_entrega'];
    $data_prevista = !empty($_POST['data_prevista']) ? "'" . $_POST['data_prevista'] . "'" : 'NULL';
    $motivo = mysqli_real_escape_string($conn, $_POST['motivo'] ?? '');
    $observacoes = mysqli_real_escape_string($conn, $_POST['observacoes'] ?? '');
    $assinatura = isset($_POST['assinatura']) ? 1 : 0;
    $usuario_entrega = intval($_SESSION['user_id']);
    
    // Verificar se o funcionário existe e está ativo
    $sql_funcionario = "SELECT fun_nome_completo FROM FUN_FUNCIONARIO WHERE fun_id = $funcionario_id AND fun_status = 'ativo'";
    $result_funcionario = mysqli_query($conn, $sql_funcionario);
    if (mysqli_num_rows($result_funcionario) == 0) {
        throw new Exception('Funcionário não encontrado ou inativo');
    }
    
    // Verificar se o EPI existe e está ativo
    $sql_epi = "SELECT epi_nome FROM EPI_EQUIPAMENTOS WHERE epi_id = $epi_id AND epi_status = 'ativo'";
    $result_epi = mysqli_query($conn, $sql_epi);
    if (mysqli_num_rows($result_epi) == 0) {
        throw new Exception('EPI não encontrado ou inativo');
    }
    
    // Inserir a entrega (versão simplificada - sem controle de estoque)
    $sql_entrega = "INSERT INTO EPI_ENTREGAS (
                        entrega_funcionario_id, entrega_epi_id, entrega_quantidade,
                        entrega_data_entrega, entrega_data_prevista_devolucao,
                        entrega_motivo_entrega, entrega_observacoes,
                        entrega_assinatura_funcionario, entrega_status,
                        entrega_usuario_entrega
                    ) VALUES (
                        $funcionario_id, $epi_id, 1,
                        '$data_entrega', $data_prevista,
                        '$motivo', '$observacoes',
                        $assinatura, 'entregue',
                        $usuario_entrega
                    )";
    
    if (!mysqli_query($conn, $sql_entrega)) {
        throw new Exception('Erro ao registrar entrega: ' . mysqli_error($conn));
    }
    
    $entrega_id = mysqli_insert_id($conn);
    
    $response = [
        'status' => 'success',
        'message' => 'Entrega registrada com sucesso!',
        'id' => $entrega_id
    ];
    
} catch (Exception $e) {
    $response = [
        'status' => 'error',
        'message' => $e->getMessage()
    ];
} finally {
    mysqli_close($conn);
}

header('Content-Type: application/json');
echo json_encode($response);
?>