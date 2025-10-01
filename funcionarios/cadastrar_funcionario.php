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
    $response = array('status' => 'error', 'message' => 'Método de requisição inválido');
    echo json_encode($response);
    exit;
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

// Função para sanitizar inputs
function sanitize($conn, $input) {
    if (is_array($input)) {
        $sanitized = [];
        foreach ($input as $key => $value) {
            $sanitized[$key] = sanitize($conn, $value);
        }
        return $sanitized;
    }
    return mysqli_real_escape_string($conn, trim($input));
}

// Recebe e sanitiza os dados do formulário
$dados = sanitize($conn, $_POST);

// Verifica os campos obrigatórios
if (empty($dados['nome']) || empty($dados['cpf']) || empty($dados['rg'])) {
    $response = array('status' => 'error', 'message' => 'Os campos Nome, CPF e RG são obrigatórios');
    echo json_encode($response);
    exit;
}

// Verifica se o CPF já existe no banco
$cpf = $dados['cpf'];
$sql_check = "SELECT fun_id FROM FUN_FUNCIONARIO WHERE fun_cpf = '$cpf'";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    $response = array('status' => 'error', 'message' => 'Este CPF já está cadastrado');
    echo json_encode($response);
    exit;
}

// Prepara as certificações (checkboxes)
$cert_vigilante = isset($dados['certificacoes']) && in_array('vigilante', $dados['certificacoes']) ? 1 : 0;
$cert_reciclagem = isset($dados['certificacoes']) && in_array('reciclagem', $dados['certificacoes']) ? 1 : 0;
$cert_arma = isset($dados['certificacoes']) && in_array('armadefogo', $dados['certificacoes']) ? 1 : 0;
$cert_seg_pessoal = isset($dados['certificacoes']) && in_array('segurancapessoal', $dados['certificacoes']) ? 1 : 0;

// Trata os campos de data
$data_nascimento = !empty($dados['dataNascimento']) ? "'{$dados['dataNascimento']}'" : "NULL";
$data_admissao = !empty($dados['dataAdmissao']) ? "'{$dados['dataAdmissao']}'" : "NULL";

// Trata o campo salário (converte de formato brasileiro para formato SQL)
$salario = !empty($dados['salario']) ? str_replace(['R$', '.', ','], ['', '', '.'], $dados['salario']) : "NULL";
if ($salario !== "NULL") {
    $salario = floatval($salario);
    $salario = "$salario"; // Converte de volta para string para a query
}

// ID do usuário que está cadastrando
$usuario_cadastro = $_SESSION['user_id'];

// Monta a query de inserção
$sql = "INSERT INTO FUN_FUNCIONARIO (
            fun_nome_completo, 
            fun_cpf, 
            fun_rg, 
            fun_data_nascimento, 
            fun_telefone, 
            fun_email, 
            fun_genero,
            fun_cep, 
            fun_endereco, 
            fun_numero, 
            fun_complemento, 
            fun_bairro, 
            fun_cidade, 
            fun_estado,
            fun_funcao, 
            fun_departamento, 
            fun_data_admissao, 
            fun_status, 
            fun_turno,
            fun_salario, 
            fun_observacoes,
            fun_cert_vigilante,
            fun_cert_reciclagem,
            fun_cert_arma_fogo,
            fun_cert_seg_pessoal,
            fun_usuario_cadastro
        ) VALUES (
            '{$dados['nome']}',
            '{$dados['cpf']}',
            '{$dados['rg']}',
            $data_nascimento,
            '{$dados['telefone']}',
            '{$dados['email']}',
            " . (!empty($dados['genero']) ? "'{$dados['genero']}'" : "NULL") . ",
            '{$dados['cep']}',
            '{$dados['endereco']}',
            '{$dados['numero']}',
            '{$dados['complemento']}',
            '{$dados['bairro']}',
            '{$dados['cidade']}',
            '{$dados['estado']}',
            " . (!empty($dados['funcao']) ? "'{$dados['funcao']}'" : "NULL") . ",
            " . (!empty($dados['departamento']) ? "'{$dados['departamento']}'" : "NULL") . ",
            $data_admissao,
            " . (!empty($dados['status']) ? "'{$dados['status']}'" : "'ativo'") . ",
            " . (!empty($dados['turno']) ? "'{$dados['turno']}'" : "NULL") . ",
            $salario,
            '{$dados['observacoes']}',
            $cert_vigilante,
            $cert_reciclagem,
            $cert_arma,
            $cert_seg_pessoal,
            $usuario_cadastro
        )";

// Executa a query
if (mysqli_query($conn, $sql)) {
    $id_inserido = mysqli_insert_id($conn);
    $response = array(
        'status' => 'success', 
        'message' => 'Funcionário cadastrado com sucesso!',
        'id' => $id_inserido
    );
} else {
    $response = array(
        'status' => 'error', 
        'message' => 'Erro ao cadastrar funcionário: ' . mysqli_error($conn),
        'query' => $sql
    );
}

// Fecha a conexão
mysqli_close($conn);

// Retorna a resposta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>