<?php
require_once '../config/conexao.php';
session_start();

// Configurações para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Registre todas as tentativas de acesso para depuração
file_put_contents('../debug_log.txt', date('Y-m-d H:i:s') . " - Acesso ao alterar_status_funcionario.php\n", FILE_APPEND);
file_put_contents('../debug_log.txt', "Headers: " . json_encode(getallheaders()) . "\n", FILE_APPEND);
file_put_contents('../debug_log.txt', "POST: " . json_encode($_POST) . "\n", FILE_APPEND);

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    $response = array('status' => 'error', 'message' => 'Usuário não autenticado');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response = array('status' => 'error', 'message' => 'Método de requisição inválido');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
}

// Verifica se o ID foi fornecido
if (!isset($_POST['id']) || empty($_POST['id'])) {
    $response = array('status' => 'error', 'message' => 'ID do funcionário não fornecido');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$id = intval($_POST['id']);

// Verifica se o status foi fornecido
$novo_status = isset($_POST['status']) ? strtolower($_POST['status']) : null;
if (!$novo_status || !in_array($novo_status, ['ativo', 'inativo'])) {
    // Se não foi fornecido um status válido, vamos alternar com base no status atual
    // Conecta ao banco de dados
    $conn = connect_local_mysqli('gebert');
    
    // Verificar o status atual do funcionário
    $sql_check = "SELECT fun_status FROM FUN_FUNCIONARIO WHERE fun_id = $id";
    $result_check = mysqli_query($conn, $sql_check);
    
    if (!$result_check || mysqli_num_rows($result_check) == 0) {
        $response = array('status' => 'error', 'message' => 'Funcionário não encontrado');
        header('Content-Type: application/json');
        echo json_encode($response);
        mysqli_close($conn);
        exit();
    }
    
    $row = mysqli_fetch_assoc($result_check);
    $status_atual = strtolower($row['fun_status']);
    
    // Definir o novo status (inverso do atual)
    $novo_status = ($status_atual == 'ativo') ? 'inativo' : 'ativo';
} else {
    // Conecta ao banco de dados
    $conn = connect_local_mysqli('gebert');
}

// ID do usuário que está alterando o status
$usuario_atualizacao = $_SESSION['user_id'];

// Atualizar o status do funcionário
$sql = "UPDATE FUN_FUNCIONARIO SET 
    fun_status = '$novo_status',
    fun_usuario_atualizacao = $usuario_atualizacao,
    fun_data_atualizacao = NOW()
    WHERE fun_id = $id";

// Executa a query
if (mysqli_query($conn, $sql)) {
    $mensagem = $novo_status == 'ativo' ? 'Funcionário ativado com sucesso!' : 'Funcionário inativado com sucesso!';
    $response = array(
        'status' => 'success',
        'message' => $mensagem,
        'id' => $id,
        'novo_status' => $novo_status,
        'status_classe' => $novo_status == 'ativo' ? 'success' : 'danger',
        'status_texto' => ucfirst($novo_status)
    );
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Erro ao alterar status do funcionário: ' . mysqli_error($conn)
    );
}

// Fecha a conexão
mysqli_close($conn);

// Registre a resposta para depuração
file_put_contents('../debug_log.txt', "Resposta alterar status: " . json_encode($response) . "\n\n", FILE_APPEND);

// Certifica-se de que nada mais será executado após isso
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
echo json_encode($response);
exit();
