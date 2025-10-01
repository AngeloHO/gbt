<?php
require_once '../config/conexao.php';
session_start();

// Verificação de autenticação
if (!isset($_SESSION['user_id'])) {
    $response = array('status' => 'error', 'message' => 'Usuário não autenticado');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Verificar se é uma requisição AJAX
$headers = getallheaders();
$isAjax = isset($headers['X-Requested-With']) && $headers['X-Requested-With'] == 'XMLHttpRequest';
if (!$isAjax) {
    $response = array('status' => 'error', 'message' => 'Acesso inválido');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Verificar se o ID foi fornecido
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $response = array('status' => 'error', 'message' => 'ID do funcionário não fornecido');
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

$id = intval($_GET['id']);

// Conectar ao banco de dados
$conn = connect_local_mysqli('gebert');

// Buscar dados do funcionário
$sql = "SELECT * FROM FUN_FUNCIONARIO WHERE fun_id = $id";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    $response = array('status' => 'error', 'message' => 'Funcionário não encontrado');
    header('Content-Type: application/json');
    echo json_encode($response);
    mysqli_close($conn);
    exit();
}

$funcionario = mysqli_fetch_assoc($result);

// Converter dados para o formato adequado para exibição
$funcionario['formattedData'] = array(
    'nome' => $funcionario['fun_nome_completo'],
    'cpf' => $funcionario['fun_cpf'],
    'rg' => $funcionario['fun_rg'],
    'dataNascimento' => $funcionario['fun_data_nascimento'],
    'telefone' => $funcionario['fun_telefone'],
    'email' => $funcionario['fun_email'],
    'genero' => $funcionario['fun_genero'],
    'cep' => $funcionario['fun_cep'],
    'endereco' => $funcionario['fun_endereco'],
    'numero' => $funcionario['fun_numero'],
    'complemento' => $funcionario['fun_complemento'],
    'bairro' => $funcionario['fun_bairro'],
    'cidade' => $funcionario['fun_cidade'],
    'estado' => $funcionario['fun_estado'],
    'funcao' => $funcionario['fun_funcao'],
    'departamento' => $funcionario['fun_departamento'],
    'dataAdmissao' => $funcionario['fun_data_admissao'],
    'status' => $funcionario['fun_status'],
    'turno' => $funcionario['fun_turno'],
    'salario' => number_format((float)$funcionario['fun_salario'], 2, ',', '.'),
    'observacoes' => $funcionario['fun_observacoes'],
    'certificacoes' => array(
        'vigilante' => $funcionario['fun_cert_vigilante'] == 1,
        'reciclagem' => $funcionario['fun_cert_reciclagem'] == 1,
        'armadefogo' => $funcionario['fun_cert_arma_fogo'] == 1,
        'segurancapessoal' => $funcionario['fun_cert_seg_pessoal'] == 1
    )
);

// Retornar dados em formato JSON
$response = array('status' => 'success', 'data' => $funcionario['formattedData']);
header('Content-Type: application/json');
echo json_encode($response);
mysqli_close($conn);
exit();
?>