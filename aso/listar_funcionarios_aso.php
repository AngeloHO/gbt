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

try {
    // Busca funcionários ativos
    $sql = "SELECT 
                FUN_ID as id,
                FUN_NOME_COMPLETO as nome,
                FUN_CPF as cpf,
                FUN_FUNCAO as funcao
            FROM FUN_FUNCIONARIO 
            WHERE FUN_STATUS = 'ativo'
            ORDER BY FUN_NOME_COMPLETO ASC";

    $result = mysqli_query($conn, $sql);
    $funcionarios = array();

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Formatar CPF
            $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $row['cpf']);
            
            $funcionarios[] = array(
                'id' => $row['id'],
                'nome' => $row['nome'],
                'cpf' => $cpf,
                'funcao' => $row['funcao']
            );
        }
    }

    $response = array(
        'status' => 'success',
        'funcionarios' => $funcionarios
    );

} catch (Exception $e) {
    $response = array(
        'status' => 'error',
        'message' => 'Erro ao buscar funcionários: ' . $e->getMessage()
    );
}

// Fecha a conexão
mysqli_close($conn);

// Retorna os resultados como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>