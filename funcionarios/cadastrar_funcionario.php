<?php
require_once '../config/conexao.php';
session_start();

// Configurações para depuração
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Registre todas as tentativas de acesso para depuração
file_put_contents('../debug_log.txt', date('Y-m-d H:i:s') . " - Acesso ao cadastrar_funcionario.php\n", FILE_APPEND);
file_put_contents('../debug_log.txt', "Headers: " . json_encode(getallheaders()) . "\n", FILE_APPEND);
file_put_contents('../debug_log.txt', "POST: " . json_encode($_POST) . "\n", FILE_APPEND);

// Verificação da chamada AJAX foi removida para testes

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

// Verifica se o CPF já existe no banco e se o status é ativo
$cpf = $dados['cpf'];
$sql_check = "SELECT fun_id, fun_status FROM FUN_FUNCIONARIO WHERE fun_cpf = '$cpf'";
$result_check = mysqli_query($conn, $sql_check);

$readmissao = false;
$funcionario_id = null;

if (mysqli_num_rows($result_check) > 0) {
    $row = mysqli_fetch_assoc($result_check);
    $funcionario_id = $row['fun_id'];
    
    // Verificar se já existe um funcionário ativo com este CPF, mas apenas se não estiver em modo de edição
    // ou se for uma edição de um funcionário diferente
    if (strtolower($row['fun_status']) == 'ativo') {
        // Se temos um ID no POST (estamos em modo de edição) e o ID corresponde ao funcionário encontrado
        // então permitimos continuar pois é a edição do mesmo funcionário
        if (isset($dados['id']) && $dados['id'] == $funcionario_id) {
            // É o mesmo funcionário sendo editado, então podemos continuar
            file_put_contents('../debug_log.txt', date('Y-m-d H:i:s') . " - Editando funcionário existente com CPF: $cpf (ID: $funcionario_id)\n", FILE_APPEND);
            $readmissao = false; // Não é readmissão, é edição
        } else {
            // CPF pertence a outro funcionário ativo
            $response = array('status' => 'error', 'message' => 'Este CPF já está cadastrado para um funcionário ativo');
            echo json_encode($response);
            exit;
        }
    } else {
        // Se chegarmos aqui, o funcionário existe mas está inativo, então podemos prosseguir
        // Registramos no log que estamos readmitindo um funcionário
        file_put_contents('../debug_log.txt', date('Y-m-d H:i:s') . " - Readmitindo funcionário com CPF: $cpf (ID: $funcionario_id)\n", FILE_APPEND);
        $readmissao = true;
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

// ID do usuário que está cadastrando
$usuario_cadastro = $_SESSION['user_id'];

// Se for fornecido um ID e não for um processo de readmissão, 
// então estamos tentando editar um funcionário via cadastrar_funcionario.php (erro de URL)
// Neste caso, redirecionamos para o atualizar_funcionario.php
if (isset($dados['id']) && !empty($dados['id']) && !$readmissao) {
    $funcionario_id = $dados['id'];
    file_put_contents('../debug_log.txt', date('Y-m-d H:i:s') . " - Redirecionando para atualizar_funcionario.php com ID: $funcionario_id\n", FILE_APPEND);
    
    // Respondemos com erro para que o cliente reenvie para a URL correta
    $response = array(
        'status' => 'redirect', 
        'message' => 'Esta operação é uma edição, não um novo cadastro. Use a URL atualizar_funcionario.php', 
        'redirect' => 'atualizar_funcionario.php'
    );
    echo json_encode($response);
    exit;
}

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
            'ativo', /* Define explicitamente como ativo para todos os novos cadastros */
            " . (!empty($dados['turno']) ? "'{$dados['turno']}'" : "NULL") . ",
            $salario,
            '{$dados['observacoes']}',
            $cert_vigilante,
            $cert_reciclagem,
            $cert_arma,
            $cert_seg_pessoal,
            $usuario_cadastro
        )";

// Verificar se é readmissão (update) ou novo cadastro (insert)
if ($readmissao) {
    // Prepara a query de UPDATE para readmissão
    $sql_update = "UPDATE FUN_FUNCIONARIO SET 
        fun_nome_completo = '{$dados['nome']}',
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
        fun_status = 'ativo',
        fun_turno = " . (!empty($dados['turno']) ? "'{$dados['turno']}'" : "NULL") . ",
        fun_salario = $salario,
        fun_observacoes = '{$dados['observacoes']}',
        fun_cert_vigilante = $cert_vigilante,
        fun_cert_reciclagem = $cert_reciclagem,
        fun_cert_arma_fogo = $cert_arma,
        fun_cert_seg_pessoal = $cert_seg_pessoal,
        fun_usuario_cadastro = $usuario_cadastro
        WHERE fun_id = $funcionario_id";
        
    $sql = $sql_update; // Atualiza a variável sql para log de erros
    
    // Executa o UPDATE
    if (mysqli_query($conn, $sql_update)) {
        $response = array(
            'status' => 'success', 
            'message' => 'Funcionário readmitido com sucesso!',
            'id' => $funcionario_id,
            'readmissao' => true
        );
    } else {
        $response = array(
            'status' => 'error', 
            'message' => 'Erro ao readmitir funcionário: ' . mysqli_error($conn),
            'query' => $sql_update
        );
    }
} else {
    // Executa a query de INSERT para novo cadastro
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
}

// Fecha a conexão
mysqli_close($conn);

// Registre a resposta para depuração
file_put_contents('../debug_log.txt', "Resposta: " . json_encode($response) . "\n\n", FILE_APPEND);

// Certifica-se de que nada mais será executado após isso
header('Content-Type: application/json');
header('Cache-Control: no-cache, no-store, must-revalidate');
echo json_encode($response);
exit();
?>