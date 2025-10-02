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

// Pegar dados do JSON
$input = json_decode(file_get_contents('php://input'), true);

if (!isset($input['entrega_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'ID da entrega não fornecido']);
    exit();
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

try {
    $entrega_id = intval($input['entrega_id']);
    $usuario_atualizacao = intval($_SESSION['user_id']);
    
    // Buscar dados da entrega
    $sql_entrega = "SELECT 
                        e.entrega_status,
                        ep.epi_nome
                    FROM EPI_ENTREGAS e
                    INNER JOIN EPI_EQUIPAMENTOS ep ON e.entrega_epi_id = ep.epi_id
                    WHERE e.entrega_id = $entrega_id";
    
    $result_entrega = mysqli_query($conn, $sql_entrega);
    
    if (mysqli_num_rows($result_entrega) == 0) {
        throw new Exception('Entrega não encontrada');
    }
    
    $entrega_dados = mysqli_fetch_assoc($result_entrega);
    
    if ($entrega_dados['entrega_status'] !== 'entregue') {
        throw new Exception('Esta entrega não pode ser devolvida (status: ' . $entrega_dados['entrega_status'] . ')');
    }
    
    // Marcar entrega como devolvida (versão simplificada - sem controle de estoque)
    $sql_devolucao = "UPDATE EPI_ENTREGAS 
                      SET entrega_status = 'devolvido',
                          entrega_data_devolucao = CURRENT_DATE(),
                          entrega_usuario_atualizacao = $usuario_atualizacao,
                          entrega_data_atualizacao = NOW()
                      WHERE entrega_id = $entrega_id";
    
    if (!mysqli_query($conn, $sql_devolucao)) {
        throw new Exception('Erro ao marcar devolução: ' . mysqli_error($conn));
    }
    
    $response = [
        'status' => 'success',
        'message' => 'Devolução registrada com sucesso!'
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