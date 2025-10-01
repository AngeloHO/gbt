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

// Parâmetros de paginação
$pagina = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$registros_por_pagina = isset($_GET['limite']) ? intval($_GET['limite']) : 10;
$offset = ($pagina - 1) * $registros_por_pagina;

// Parâmetros de busca
$busca = isset($_GET['busca']) ? mysqli_real_escape_string($conn, $_GET['busca']) : '';
$filtro_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';

// Condições de busca
$where = "WHERE 1=1";
if (!empty($busca)) {
    $where .= " AND (
        fun_nome_completo LIKE '%$busca%' OR 
        fun_cpf LIKE '%$busca%' OR 
        fun_email LIKE '%$busca%' OR
        fun_telefone LIKE '%$busca%'
    )";
}

if (!empty($filtro_status)) {
    $where .= " AND fun_status = '$filtro_status'";
}

// Consulta para contar o total de registros
$sql_count = "SELECT COUNT(fun_id) as total FROM FUN_FUNCIONARIO $where";
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_registros = $row_count['total'];

// Calcula o total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta para buscar os registros
$sql = "SELECT 
            fun_id, 
            fun_nome_completo, 
            fun_cpf, 
            fun_funcao, 
            fun_telefone, 
            fun_status
        FROM FUN_FUNCIONARIO 
        $where
        ORDER BY fun_nome_completo ASC
        LIMIT $offset, $registros_por_pagina";

$result = mysqli_query($conn, $sql);

// Array para armazenar os resultados
$funcionarios = array();

// Mapeamento de funções para nomes mais amigáveis
$funcoes = array(
    'vigilante' => 'Vigilante',
    'porteiro' => 'Porteiro',
    'seguranca' => 'Segurança',
    'supervisor' => 'Supervisor',
    'coordenador' => 'Coordenador',
    'administrador' => 'Administrador',
    'outro' => 'Outro'
);

// Mapeamento de status para classes Bootstrap
$status_classes = array(
    'ativo' => 'success',
    'inativo' => 'danger',
    'ferias' => 'info',
    'licenca' => 'warning'
);

// Mapeamento de status para texto amigável
$status_texto = array(
    'ativo' => 'Ativo',
    'inativo' => 'Inativo',
    'ferias' => 'Em férias',
    'licenca' => 'Em licença'
);

// Processa os resultados
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Formata o CPF
        $cpf = $row['fun_cpf'];
        
        // Determina a função (cargo)
        $funcao = isset($funcoes[$row['fun_funcao']]) ? $funcoes[$row['fun_funcao']] : 'Não definido';
        
        // Determina a classe e texto do status
        $status_classe = isset($status_classes[$row['fun_status']]) ? $status_classes[$row['fun_status']] : 'secondary';
        $status_texto_formatado = isset($status_texto[$row['fun_status']]) ? $status_texto[$row['fun_status']] : 'Não definido';
        
        // Adiciona ao array de funcionários
        $funcionarios[] = array(
            'id' => $row['fun_id'],
            'nome' => $row['fun_nome_completo'],
            'cpf' => $cpf,
            'funcao' => $funcao,
            'telefone' => $row['fun_telefone'] ?: 'Não informado',
            'status' => $row['fun_status'],
            'status_classe' => $status_classe,
            'status_texto' => $status_texto_formatado
        );
    }
}

// Prepara a resposta
$response = array(
    'status' => 'success',
    'funcionarios' => $funcionarios,
    'paginacao' => array(
        'pagina_atual' => $pagina,
        'total_paginas' => $total_paginas,
        'registros_por_pagina' => $registros_por_pagina,
        'total_registros' => $total_registros
    )
);

// Fecha a conexão
mysqli_close($conn);

// Retorna os resultados como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>