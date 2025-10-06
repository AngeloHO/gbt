<?php
require_once '../config/conexao.php';
session_start();

// Estabelecer conexão com o banco
$mysqli = connect_local_mysqli('gebert');

// Verificar se a conexão foi estabelecida
if (!$mysqli || $mysqli->connect_error) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Erro de conexão com o banco de dados'
    ]);
    exit;
}

// Verificar se o usuário está logado
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'message' => 'Usuário não autorizado']);
    exit;
}

// Verificar se é uma requisição GET
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método não permitido']);
    exit;
}

// Obter funcionario_id da URL
$funcionario_id = isset($_GET['funcionario_id']) ? (int)$_GET['funcionario_id'] : 0;

if ($funcionario_id <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'ID do funcionário é obrigatório']);
    exit;
}

try {
    // Verificar se o funcionário existe
    $stmt = $mysqli->prepare("SELECT FUN_ID, FUN_NOME_COMPLETO, FUN_FUNCAO FROM FUN_FUNCIONARIO WHERE FUN_ID = ?");
    $stmt->bind_param("i", $funcionario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['status' => 'error', 'message' => 'Funcionário não encontrado']);
        exit;
    }
    
    $funcionario = $result->fetch_assoc();
    
    // Buscar avaliações do funcionário (ordenado por data, mais recente primeiro)
    $sql = "SELECT 
                a.AVA_ID,
                a.AVA_AVALIADO_POR,
                a.AVA_DATA_FEEDBACK,
                a.AVA_CARGO,
                a.AVA_QUALIDADE_TRABALHO,
                a.AVA_PRODUTIVIDADE,
                a.AVA_COLABORACAO,
                a.AVA_COMUNICACAO,
                a.AVA_COMPROMETIMENTO,
                a.AVA_PONTOS_FORTES,
                a.AVA_PONTOS_MELHORIA,
                a.AVA_SUGESTAO_EVOLUCAO,
                a.AVA_CREATED_AT,
                u.USU_NOME as CRIADO_POR
            FROM FUN_AVALIACOES_FEEDBACK a
            LEFT JOIN usu_usuario u ON a.AVA_CREATED_BY = u.USU_ID
            WHERE a.AVA_FUNCIONARIO_ID = ?
            ORDER BY a.AVA_DATA_FEEDBACK DESC, a.AVA_CREATED_AT DESC";
    
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $funcionario_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $avaliacoes = [];
    while ($row = $result->fetch_assoc()) {
        // Calcular média da avaliação
        $notas = array_filter([
            $row['AVA_QUALIDADE_TRABALHO'],
            $row['AVA_PRODUTIVIDADE'],
            $row['AVA_COLABORACAO'],
            $row['AVA_COMUNICACAO'],
            $row['AVA_COMPROMETIMENTO']
        ]);
        
        $media = 0;
        $classificacao = 'Não avaliado';
        
        if (count($notas) > 0) {
            $media = array_sum($notas) / count($notas);
            
            if ($media >= 4.5) {
                $classificacao = 'Excelente';
            } elseif ($media >= 3.5) {
                $classificacao = 'Satisfatório';
            } elseif ($media >= 2.5) {
                $classificacao = 'Regular';
            } else {
                $classificacao = 'Insatisfatório';
            }
        }
        
        // Formatar data para exibição
        $data_formatada = date('d/m/Y', strtotime($row['AVA_DATA_FEEDBACK']));
        $data_criacao = date('d/m/Y H:i', strtotime($row['AVA_CREATED_AT']));
        
        $avaliacoes[] = [
            'id' => $row['AVA_ID'],
            'avaliado_por' => $row['AVA_AVALIADO_POR'],
            'data_feedback' => $data_formatada,
            'data_criacao' => $data_criacao,
            'cargo' => $row['AVA_CARGO'],
            'avaliacoes' => [
                'qualidade_trabalho' => $row['AVA_QUALIDADE_TRABALHO'],
                'produtividade' => $row['AVA_PRODUTIVIDADE'],
                'colaboracao' => $row['AVA_COLABORACAO'],
                'comunicacao' => $row['AVA_COMUNICACAO'],
                'comprometimento' => $row['AVA_COMPROMETIMENTO']
            ],
            'feedback' => [
                'pontos_fortes' => $row['AVA_PONTOS_FORTES'],
                'pontos_melhoria' => $row['AVA_PONTOS_MELHORIA'],
                'sugestao_evolucao' => $row['AVA_SUGESTAO_EVOLUCAO']
            ],
            'media_geral' => round($media, 1),
            'classificacao' => $classificacao,
            'criterios_avaliados' => count($notas),
            'criado_por' => $row['CRIADO_POR']
        ];
    }
    
    // Separar avaliação atual (mais recente) do histórico
    $avaliacao_atual = count($avaliacoes) > 0 ? $avaliacoes[0] : null;
    $historico = count($avaliacoes) > 1 ? array_slice($avaliacoes, 1) : [];
    
    echo json_encode([
        'status' => 'success',
        'data' => [
            'funcionario' => [
                'id' => $funcionario['FUN_ID'],
                'nome' => $funcionario['FUN_NOME_COMPLETO'],
                'funcao' => $funcionario['FUN_FUNCAO']
            ],
            'avaliacao_atual' => $avaliacao_atual,
            'historico' => $historico,
            'total_avaliacoes' => count($avaliacoes)
        ]
    ]);
    
} catch (Exception $e) {
    error_log("Erro ao buscar avaliações: " . $e->getMessage());
    echo json_encode([
        'status' => 'error',
        'message' => 'Erro interno do servidor'
    ]);
}
?>