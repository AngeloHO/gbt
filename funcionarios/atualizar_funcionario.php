<?php
require_once '../config/conexao.php';
session_start();

// Configurações para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Registre todas as tentativas de acesso para depuração
file_put_contents('../debug_log.txt', date('Y-m-d H:i:s') . " - Acesso ao atualizar_funcionario.php\n", FILE_APPEND);
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

// Verifica se o CPF já existe no banco para outro funcionário
$cpf = $dados['cpf'];
$sql_check = "SELECT fun_id, fun_status FROM FUN_FUNCIONARIO WHERE fun_cpf = '$cpf' AND fun_id != $id";
$result_check = mysqli_query($conn, $sql_check);

if (mysqli_num_rows($result_check) > 0) {
    $row = mysqli_fetch_assoc($result_check);
    
    // Verificar se o status é "ativo"
    if (strtolower($row['fun_status']) == 'ativo') {
        $response = array('status' => 'error', 'message' => 'Este CPF já está cadastrado para outro funcionário ativo');
        echo json_encode($response);
        exit;
    }
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

// ID do usuário que está editando
$usuario_edicao = $_SESSION['user_id'];

// Monta a query de atualização
$sql = "UPDATE FUN_FUNCIONARIO SET 
    fun_nome_completo = '{$dados['nome']}',
    fun_cpf = '{$dados['cpf']}',
    fun_rg = '{$dados['rg']}',
    fun_data_nascimento = $data_nascimento,
    fun_telefone = '{$dados['telefone']}',
    fun_email = '{$dados['email']}',
    fun_genero = " . (!empty($dados['genero']) ? "'{$dados['genero']}'" : "NULL") . ",
    fun_cep = '{$dados['cep']}',
    fun_endereco = '{$dados['endereco']}',
    fun_numero = '{$dados['numero']}',
    fun_complemento = '{$dados['complemento']}',
    fun_bairro = '{$dados['bairro']}',
    fun_cidade = '{$dados['cidade']}',
    fun_estado = '{$dados['estado']}',
    fun_funcao = " . (!empty($dados['funcao']) ? "'{$dados['funcao']}'" : "NULL") . ",
    fun_departamento = " . (!empty($dados['departamento']) ? "'{$dados['departamento']}'" : "NULL") . ",
    fun_data_admissao = $data_admissao,
    fun_turno = " . (!empty($dados['turno']) ? "'{$dados['turno']}'" : "NULL") . ",
    fun_salario = $salario,
    fun_observacoes = '{$dados['observacoes']}',
    fun_cert_vigilante = $cert_vigilante,
    fun_cert_reciclagem = $cert_reciclagem,
    fun_cert_arma_fogo = $cert_arma,
    fun_cert_seg_pessoal = $cert_seg_pessoal,
    fun_usuario_atualizacao = $usuario_edicao,
    fun_data_atualizacao = NOW()
    WHERE fun_id = $id";

// Executa a query
if (mysqli_query($conn, $sql)) {
    $response = array(
        'status' => 'success', 
        'message' => 'Dados do funcionário atualizados com sucesso!',
        'id' => $id
    );
} else {
    $response = array(
        'status' => 'error', 
        'message' => 'Erro ao atualizar dados do funcionário: ' . mysqli_error($conn),
        'query' => $sql
    );
}

// Fecha a conexão
mysqli_close($conn);

// Registre a resposta para depuração
file_put_contents('../debug_log.txt', "Resposta atualizar: " . json_encode($response) . "\n\n", FILE_APPEND);

// Certifica-se de que nada mais será executado após isso
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
echo json_encode($response);
exit();
?>