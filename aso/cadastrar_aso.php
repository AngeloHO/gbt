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
    $response = array('status' => 'error', 'message' => 'Método não permitido');
    echo json_encode($response);
    exit;
}

// Conecta ao banco de dados
$conn = connect_local_mysqli('gebert');

try {
    // Recebe os dados do formulário
    $funcionario_id = intval($_POST['funcionario_id']);
    $tipo_exame = mysqli_real_escape_string($conn, $_POST['tipo_exame']);
    $data_exame = mysqli_real_escape_string($conn, $_POST['data_exame']);
    $data_validade = mysqli_real_escape_string($conn, $_POST['data_validade']);
    $resultado = mysqli_real_escape_string($conn, $_POST['resultado']);
    $medico_responsavel = mysqli_real_escape_string($conn, $_POST['medico_responsavel']);
    $crm_medico = mysqli_real_escape_string($conn, $_POST['crm_medico'] ?? '');
    $clinica_exame = mysqli_real_escape_string($conn, $_POST['clinica_exame'] ?? '');
    $exames_realizados = mysqli_real_escape_string($conn, $_POST['exames_realizados'] ?? '');
    $restricoes = mysqli_real_escape_string($conn, $_POST['restricoes'] ?? '');
    $observacoes = mysqli_real_escape_string($conn, $_POST['observacoes'] ?? '');
    $numero_documento = mysqli_real_escape_string($conn, $_POST['numero_documento'] ?? '');
    $user_id = $_SESSION['user_id'];
    
    // Validações básicas
    if (empty($funcionario_id) || empty($tipo_exame) || empty($data_exame) || 
        empty($data_validade) || empty($resultado) || empty($medico_responsavel)) {
        throw new Exception('Todos os campos obrigatórios devem ser preenchidos');
    }

    // Verifica se a data de validade é posterior à data do exame
    if (strtotime($data_validade) <= strtotime($data_exame)) {
        throw new Exception('A data de validade deve ser posterior à data do exame');
    }

    // Verifica se o funcionário existe e está ativo
    $sql_funcionario = "SELECT FUN_ID, FUN_NOME_COMPLETO FROM FUN_FUNCIONARIO WHERE FUN_ID = $funcionario_id AND FUN_STATUS = 'ativo'";
    $result_funcionario = mysqli_query($conn, $sql_funcionario);
    
    if (mysqli_num_rows($result_funcionario) === 0) {
        throw new Exception('Funcionário não encontrado ou inativo');
    }

    // Tratamento do arquivo (se enviado)
    $arquivo_path = null;
    if (isset($_FILES['arquivo_aso']) && $_FILES['arquivo_aso']['error'] === UPLOAD_ERR_OK) {
        $arquivo = $_FILES['arquivo_aso'];
        
        // Validações do arquivo
        $tamanho_max = 5 * 1024 * 1024; // 5MB
        $tipos_permitidos = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
        
        if ($arquivo['size'] > $tamanho_max) {
            throw new Exception('Arquivo muito grande. Tamanho máximo: 5MB');
        }
        
        if (!in_array($arquivo['type'], $tipos_permitidos)) {
            throw new Exception('Tipo de arquivo não permitido. Use PDF, JPG ou PNG');
        }
        
        // Criar diretório se não existir
        $diretorio_upload = '../uploads/aso/';
        if (!file_exists($diretorio_upload)) {
            mkdir($diretorio_upload, 0777, true);
        }
        
        // Gerar nome único para o arquivo
        $extensao = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $nome_arquivo = 'aso_' . $funcionario_id . '_' . date('Y-m-d_H-i-s') . '.' . $extensao;
        $caminho_completo = $diretorio_upload . $nome_arquivo;
        
        // Move o arquivo
        if (move_uploaded_file($arquivo['tmp_name'], $caminho_completo)) {
            $arquivo_path = 'uploads/aso/' . $nome_arquivo;
        } else {
            throw new Exception('Erro ao fazer upload do arquivo');
        }
    }

    // Inicia transação
    mysqli_begin_transaction($conn);

    // Desativa ASOs anteriores do mesmo funcionário se for admissional ou periódico
    if (in_array($tipo_exame, ['Admissional', 'Periódico'])) {
        $sql_desativar = "UPDATE FUN_ASO 
                         SET ASO_STATUS = 'CANCELADO', 
                             ASO_UPDATED_AT = CURRENT_TIMESTAMP,
                             ASO_UPDATED_BY = $user_id
                         WHERE ASO_FUNCIONARIO_ID = $funcionario_id 
                         AND ASO_STATUS = 'ATIVO'
                         AND ASO_TIPO_EXAME IN ('Admissional', 'Periódico')";
        
        if (!mysqli_query($conn, $sql_desativar)) {
            throw new Exception('Erro ao desativar ASOs anteriores');
        }
    }

    // Insere o novo ASO
    $sql_insert = "INSERT INTO FUN_ASO (
        ASO_FUNCIONARIO_ID,
        ASO_TIPO_EXAME,
        ASO_DATA_EXAME,
        ASO_DATA_VALIDADE,
        ASO_RESULTADO,
        ASO_MEDICO_RESPONSAVEL,
        ASO_CRM_MEDICO,
        ASO_CLINICA_EXAME,
        ASO_OBSERVACOES,
        ASO_RESTRICOES,
        ASO_EXAMES_REALIZADOS,
        ASO_NUMERO_DOCUMENTO,
        ASO_ARQUIVO_PATH,
        ASO_STATUS,
        ASO_CREATED_BY,
        ASO_UPDATED_BY
    ) VALUES (
        $funcionario_id,
        '$tipo_exame',
        '$data_exame',
        '$data_validade',
        '$resultado',
        '$medico_responsavel',
        '$crm_medico',
        '$clinica_exame',
        '$observacoes',
        '$restricoes',
        '$exames_realizados',
        '$numero_documento',
        " . ($arquivo_path ? "'$arquivo_path'" : "NULL") . ",
        'ATIVO',
        $user_id,
        $user_id
    )";

    if (!mysqli_query($conn, $sql_insert)) {
        throw new Exception('Erro ao cadastrar ASO: ' . mysqli_error($conn));
    }

    // Confirma a transação
    mysqli_commit($conn);

    $response = array(
        'status' => 'success',
        'message' => 'ASO cadastrado com sucesso!',
        'id' => mysqli_insert_id($conn)
    );

} catch (Exception $e) {
    // Reverte a transação em caso de erro
    if (isset($conn)) {
        mysqli_rollback($conn);
    }
    
    // Remove arquivo se foi feito upload
    if (isset($caminho_completo) && file_exists($caminho_completo)) {
        unlink($caminho_completo);
    }
    
    $response = array(
        'status' => 'error',
        'message' => $e->getMessage()
    );
}

// Fecha a conexão
if (isset($conn)) {
    mysqli_close($conn);
}

// Retorna a resposta como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>