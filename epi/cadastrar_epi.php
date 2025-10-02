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
    $campos_obrigatorios = ['nome', 'categoria'];
    foreach ($campos_obrigatorios as $campo) {
        if (empty($_POST[$campo])) {
            throw new Exception("Campo '$campo' é obrigatório");
        }
    }
    
    // Escapar dados de entrada
    $nome = mysqli_real_escape_string($conn, $_POST['nome']);
    $descricao = mysqli_real_escape_string($conn, $_POST['descricao'] ?? '');
    $categoria = mysqli_real_escape_string($conn, $_POST['categoria']);
    $fabricante = mysqli_real_escape_string($conn, $_POST['fabricante'] ?? '');
    $tamanho = mysqli_real_escape_string($conn, $_POST['tamanho'] ?? '');
    $observacoes = mysqli_real_escape_string($conn, $_POST['observacoes'] ?? '');
    $usuario_cadastro = intval($_SESSION['user_id']);
    
    // Inserir EPI (versão simplificada)
    $sql = "INSERT INTO EPI_EQUIPAMENTOS (
                epi_nome, epi_descricao, epi_categoria, epi_fabricante, 
                epi_tamanho, epi_observacoes, epi_status, epi_usuario_cadastro,
                epi_estoque_atual, epi_estoque_minimo
            ) VALUES (
                '$nome', '$descricao', '$categoria', '$fabricante',
                '$tamanho', '$observacoes', 'ativo', $usuario_cadastro,
                1, 1
            )";
    
    if (!mysqli_query($conn, $sql)) {
        throw new Exception('Erro ao cadastrar EPI: ' . mysqli_error($conn));
    }
    
    $epi_id = mysqli_insert_id($conn);
    
    $response = [
        'status' => 'success',
        'message' => 'EPI cadastrado com sucesso!',
        'id' => $epi_id
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