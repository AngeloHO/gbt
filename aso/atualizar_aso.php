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
    $aso_id = intval($_POST['aso_id']);
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
    if (empty($aso_id)) {
        throw new Exception('ID do ASO não informado');
    }
    
    if (empty($funcionario_id) || empty($tipo_exame) || empty($data_exame) || 
        empty($data_validade) || empty($resultado) || empty($medico_responsavel)) {
        throw new Exception('Todos os campos obrigatórios devem ser preenchidos');
    }

    // Verifica se a data de validade é posterior à data do exame
    if (strtotime($data_validade) <= strtotime($data_exame)) {
        throw new Exception('A data de validade deve ser posterior à data do exame');
    }

    // Verifica se o ASO existe
    $sql_check = "SELECT ASO_ID, ASO_ARQUIVO_PATH FROM FUN_ASO WHERE ASO_ID = $aso_id";
    $result_check = mysqli_query($conn, $sql_check);
    
    if (mysqli_num_rows($result_check) === 0) {
        throw new Exception('ASO não encontrado');
    }
    
    $aso_atual = mysqli_fetch_assoc($result_check);
    $arquivo_path_atual = $aso_atual['ASO_ARQUIVO_PATH'];

    // Verifica se o funcionário existe e está ativo
    $sql_funcionario = "SELECT FUN_ID FROM FUN_FUNCIONARIO WHERE FUN_ID = $funcionario_id AND FUN_STATUS = 'ativo'";
    $result_funcionario = mysqli_query($conn, $sql_funcionario);
    
    if (mysqli_num_rows($result_funcionario) === 0) {
        throw new Exception('Funcionário não encontrado ou inativo');
    }

    // Tratamento do arquivo (se enviado)
    $arquivo_path = $arquivo_path_atual; // Mantém o arquivo atual por padrão
    
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
            // Remove arquivo anterior se existir
            if ($arquivo_path_atual && file_exists('../' . $arquivo_path_atual)) {
                unlink('../' . $arquivo_path_atual);
            }
            
            $arquivo_path = 'uploads/aso/' . $nome_arquivo;
        } else {
            throw new Exception('Erro ao fazer upload do arquivo');
        }
    }

    // Inicia transação
    mysqli_begin_transaction($conn);

    // Atualiza o ASO
    $sql_update = "UPDATE FUN_ASO SET
        ASO_FUNCIONARIO_ID = $funcionario_id,
        ASO_TIPO_EXAME = '$tipo_exame',
        ASO_DATA_EXAME = '$data_exame',
        ASO_DATA_VALIDADE = '$data_validade',
        ASO_RESULTADO = '$resultado',
        ASO_MEDICO_RESPONSAVEL = '$medico_responsavel',
        ASO_CRM_MEDICO = '$crm_medico',
        ASO_CLINICA_EXAME = '$clinica_exame',
        ASO_OBSERVACOES = '$observacoes',
        ASO_RESTRICOES = '$restricoes',
        ASO_EXAMES_REALIZADOS = '$exames_realizados',
        ASO_NUMERO_DOCUMENTO = '$numero_documento',
        ASO_ARQUIVO_PATH = " . ($arquivo_path ? "'$arquivo_path'" : "NULL") . ",
        ASO_UPDATED_AT = CURRENT_TIMESTAMP,
        ASO_UPDATED_BY = $user_id
        WHERE ASO_ID = $aso_id";

    if (!mysqli_query($conn, $sql_update)) {
        throw new Exception('Erro ao atualizar ASO: ' . mysqli_error($conn));
    }

    // Confirma a transação
    mysqli_commit($conn);

    $response = array(
        'status' => 'success',
        'message' => 'ASO atualizado com sucesso!'
    );

} catch (Exception $e) {
    // Reverte a transação em caso de erro
    if (isset($conn)) {
        mysqli_rollback($conn);
    }
    
    // Remove arquivo novo se foi feito upload
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