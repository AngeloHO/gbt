<?php
require_once '../config/conexao.php';
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autenticado']);
    exit;
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

// Busca funcionários ativos
$sql = "SELECT fun_id, fun_nome_completo, fun_cpf, fun_funcao, fun_telefone 
        FROM FUN_FUNCIONARIO 
        WHERE fun_status = 'ativo' 
        ORDER BY fun_nome_completo";

$result = mysqli_query($conn, $sql);

// Array para armazenar os funcionários
$funcionarios = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $funcionarios[] = array(
            'id' => $row['fun_id'],
            'nome' => $row['fun_nome_completo'],
            'cpf' => $row['fun_cpf'],
            'funcao' => $row['fun_funcao'] ?? 'Não definida',
            'telefone' => $row['fun_telefone'] ?? 'Não informado'
        );
    }
}

// Retorna os dados em JSON
header('Content-Type: application/json');
echo json_encode([
    'status' => 'success',
    'data' => $funcionarios,
    'total' => count($funcionarios)
]);

// Fecha a conexão
mysqli_close($conn);
?>