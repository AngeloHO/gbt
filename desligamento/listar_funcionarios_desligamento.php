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

// Busca funcionários ativos para o select
$sql = "SELECT 
            fun_id, 
            fun_nome_completo, 
            fun_cpf, 
            fun_funcao,
            fun_salario
        FROM FUN_FUNCIONARIO 
        WHERE fun_status = 'ativo'
        ORDER BY fun_nome_completo ASC";

$result = mysqli_query($conn, $sql);

$funcionarios = array();

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Formata o CPF
        $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $row['fun_cpf']);
        
        // Formata o salário
        $salario = 'R$ ' . number_format($row['fun_salario'], 2, ',', '.');
        
        $funcionarios[] = array(
            'id' => $row['fun_id'],
            'nome' => $row['fun_nome_completo'],
            'cpf' => $cpf,
            'funcao' => $row['fun_funcao'],
            'salario' => $salario,
            'salario_numerico' => $row['fun_salario']
        );
    }
}

$response = array(
    'status' => 'success',
    'data' => $funcionarios
);

echo json_encode($response);

mysqli_close($conn);
?>