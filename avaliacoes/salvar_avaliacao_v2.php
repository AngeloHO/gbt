<?php
require_once '../config/conexao.php';

// Função auxiliar para log de erro (caso não exista)
if (!function_exists('log_erro')) {
    function log_erro($message) {
        error_log($message);
    }
}

// Função melhorada de conexão
function connect_secure_mysqli($database = 'gebert') {
    $servidor = 'localhost';
    $login = 'root';
    $senha = '';
    
    try {
        $connection = new mysqli($servidor, $login, $senha, $database);
        
        if ($connection->connect_error) {
            throw new Exception("Erro de conexão: " . $connection->connect_error);
        }
        
        if (!$connection->set_charset("utf8")) {
            throw new Exception("Erro ao definir charset: " . $connection->error);
        }
        
        return $connection;
        
    } catch (Exception $e) {
        error_log("Erro na conexão MySQL: " . $e->getMessage());
        return false;
    }
}

session_start();

// Usar a função melhorada
$mysqli = connect_secure_mysqli('gebert');

// Verificar se a conexão foi estabelecida
if (!$mysqli) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Erro de conexão com o banco de dados. Verifique se o MySQL está rodando.'
    ]);
    exit;
}

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autorizado']);
    exit;
}

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit;
}

// Obter dados do POST
$input = json_decode(file_get_contents('php://input'), true);

// Validar dados obrigatórios
$required_fields = ['funcionario_id', 'avaliado_por', 'data_feedback', 'cargo'];
foreach ($required_fields as $field) {
    if (empty($input[$field])) {
        echo json_encode(['status' => 'error', 'message' => "Campo obrigatório: {$field}"]);
        exit;
    }
}

// Validar se pelo menos uma avaliação foi preenchida
$avaliacoes = ['qualidade_trabalho', 'produtividade', 'colaboracao', 'comunicacao', 'comprometimento'];
$tem_avaliacao = false;
foreach ($avaliacoes as $avaliacao) {
    if (!empty($input[$avaliacao]) && $input[$avaliacao] >= 1 && $input[$avaliacao] <= 5) {
        $tem_avaliacao = true;
        break;
    }
}

if (!$tem_avaliacao) {
    echo json_encode(['status' => 'error', 'message' => 'É necessário preencher pelo menos uma avaliação de desempenho']);
    exit;
}

try {
    // Verificar se o funcionário existe e está ativo
    $stmt = $mysqli->prepare("SELECT FUN_ID, FUN_NOME FROM FUN_FUNCIONARIO WHERE FUN_ID = ? AND FUN_STATUS = 'A'");
    
    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . $mysqli->error);
    }
    
    $stmt->bind_param("i", $input['funcionario_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Funcionário não encontrado ou inativo']);
        exit;
    }
    
    // Preparar dados para inserção
    $funcionario_id = (int)$input['funcionario_id'];
    $avaliado_por = strtoupper(trim($input['avaliado_por']));
    $data_feedback = $input['data_feedback'];
    $cargo = strtoupper(trim($input['cargo']));
    
    // Avaliações (permitir NULL se não preenchido)
    $qualidade_trabalho = !empty($input['qualidade_trabalho']) ? (int)$input['qualidade_trabalho'] : null;
    $produtividade = !empty($input['produtividade']) ? (int)$input['produtividade'] : null;
    $colaboracao = !empty($input['colaboracao']) ? (int)$input['colaboracao'] : null;
    $comunicacao = !empty($input['comunicacao']) ? (int)$input['comunicacao'] : null;
    $comprometimento = !empty($input['comprometimento']) ? (int)$input['comprometimento'] : null;
    
    // Feedback qualitativo (converter para maiúsculo)
    $pontos_fortes = strtoupper(trim($input['pontos_fortes'] ?? ''));
    $pontos_melhoria = strtoupper(trim($input['pontos_melhoria'] ?? ''));
    $sugestao_evolucao = strtoupper(trim($input['sugestao_evolucao'] ?? ''));
    
    $created_by = $_SESSION['user_id'];
    
    // Inserir avaliação no banco
    $sql = "INSERT INTO FUN_AVALIACOES_FEEDBACK (
        AVA_FUNCIONARIO_ID,
        AVA_AVALIADO_POR,
        AVA_DATA_FEEDBACK,
        AVA_CARGO,
        AVA_QUALIDADE_TRABALHO,
        AVA_PRODUTIVIDADE,
        AVA_COLABORACAO,
        AVA_COMUNICACAO,
        AVA_COMPROMETIMENTO,
        AVA_PONTOS_FORTES,
        AVA_PONTOS_MELHORIA,
        AVA_SUGESTAO_EVOLUCAO,
        AVA_CREATED_BY
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $mysqli->prepare($sql);
    
    if (!$stmt) {
        throw new Exception("Erro na preparação da consulta: " . $mysqli->error);
    }
    
    $stmt->bind_param(
        "isssiiiiisssi",
        $funcionario_id,
        $avaliado_por,
        $data_feedback,
        $cargo,
        $qualidade_trabalho,
        $produtividade,
        $colaboracao,
        $comunicacao,
        $comprometimento,
        $pontos_fortes,
        $pontos_melhoria,
        $sugestao_evolucao,
        $created_by
    );
    
    if ($stmt->execute()) {
        $avaliacao_id = $mysqli->insert_id;
        
        // Calcular média das avaliações para retorno
        $avaliacoes_preenchidas = array_filter([
            $qualidade_trabalho,
            $produtividade,
            $colaboracao,
            $comunicacao,
            $comprometimento
        ]);
        
        $media = 0;
        if (count($avaliacoes_preenchidas) > 0) {
            $media = array_sum($avaliacoes_preenchidas) / count($avaliacoes_preenchidas);
        }
        
        echo json_encode([
            'status' => 'success',
            'message' => 'Avaliação salva com sucesso!',
            'data' => [
                'avaliacao_id' => $avaliacao_id,
                'media_geral' => round($media, 1),
                'criterios_avaliados' => count($avaliacoes_preenchidas)
            ]
        ]);
        
    } else {
        throw new Exception("Erro ao salvar avaliação: " . $stmt->error);
    }
    
} catch (Exception $e) {
    error_log("Erro ao salvar avaliação: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro interno do servidor: ' . $e->getMessage()
    ]);
}
?>