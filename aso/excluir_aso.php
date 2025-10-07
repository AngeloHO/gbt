<?php
require_once '../config/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $response = array('status' => 'error', 'message' => 'Usuário não autenticado');
    echo json_encode($response);
    exit;
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = array('status' => 'error', 'message' => 'Método não permitido');
    echo json_encode($response);
    exit;
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

try {
    // Verifica se foi enviado o ID
    if (!isset($_POST['id']) || empty($_POST['id'])) {
        throw new Exception('ID do ASO não informado');
    }

    $aso_id = intval($_POST['id']);
    $user_id = $_SESSION['user_id'];

    // Busca informações do ASO antes de excluir
    $sql_select = "SELECT ASO_ID, ASO_ARQUIVO_PATH, ASO_FUNCIONARIO_ID 
                   FROM FUN_ASO 
                   WHERE ASO_ID = $aso_id";
    
    $result_select = mysqli_query($conn, $sql_select);
    
    if (mysqli_num_rows($result_select) === 0) {
        throw new Exception('ASO não encontrado');
    }
    
    $aso = mysqli_fetch_assoc($result_select);
    $arquivo_path = $aso['ASO_ARQUIVO_PATH'];

    // Inicia transação
    mysqli_begin_transaction($conn);

    // Exclui o ASO (ou marca como excluído dependendo da estratégia)
    // Aqui vou usar exclusão física, mas poderia ser apenas alteração de status
    $sql_delete = "DELETE FROM FUN_ASO WHERE ASO_ID = $aso_id";

    if (!mysqli_query($conn, $sql_delete)) {
        throw new Exception('Erro ao excluir ASO: ' . mysqli_error($conn));
    }

    // Remove o arquivo físico se existir
    if ($arquivo_path && file_exists('../' . $arquivo_path)) {
        if (!unlink('../' . $arquivo_path)) {
            // Log do erro, mas não falha a operação
            error_log("Erro ao remover arquivo: " . $arquivo_path);
        }
    }

    // Confirma a transação
    mysqli_commit($conn);

    $response = array(
        'status' => 'success',
        'message' => 'ASO excluído com sucesso!'
    );

} catch (Exception $e) {
    // Reverte a transação em caso de erro
    if (isset($conn)) {
        mysqli_rollback($conn);
    }
    
    $response = array(
        'status' => 'error',
        'message' => $e->getMessage()
    );
}

// Fecha a conexão
if (isset($conn)) {
    mysqli_close($conn);
}

// Retorna a resposta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>