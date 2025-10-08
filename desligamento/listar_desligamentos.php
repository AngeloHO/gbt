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
$filtro_tipo = isset($_GET['tipo']) ? mysqli_real_escape_string($conn, $_GET['tipo']) : '';
$data_inicio = isset($_GET['data_inicio']) ? mysqli_real_escape_string($conn, $_GET['data_inicio']) : '';
$data_fim = isset($_GET['data_fim']) ? mysqli_real_escape_string($conn, $_GET['data_fim']) : '';

// Condições de busca
$where = "WHERE 1=1";

if (!empty($busca)) {
    $where .= " AND (
        f.fun_nome_completo LIKE '%$busca%' OR 
        f.fun_cpf LIKE '%$busca%' OR
        d.des_motivo LIKE '%$busca%'
    )";
}

if (!empty($filtro_status)) {
    $where .= " AND d.des_status = '$filtro_status'";
}

if (!empty($filtro_tipo)) {
    $where .= " AND d.des_tipo_desligamento = '$filtro_tipo'";
}

if (!empty($data_inicio)) {
    $where .= " AND d.des_data_desligamento >= '$data_inicio'";
}

if (!empty($data_fim)) {
    $where .= " AND d.des_data_desligamento <= '$data_fim'";
}

// Consulta para contar o total de registros
$sql_count = "SELECT COUNT(d.des_id) as total 
              FROM DES_DESLIGAMENTO d 
              INNER JOIN FUN_FUNCIONARIO f ON d.des_funcionario_id = f.fun_id 
              $where";
$result_count = mysqli_query($conn, $sql_count);
$row_count = mysqli_fetch_assoc($result_count);
$total_registros = $row_count['total'];

// Calcula o total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Consulta para buscar os registros
$sql = "SELECT 
            d.des_id,
            d.des_data_solicitacao,
            d.des_data_desligamento,
            d.des_tipo_desligamento,
            d.des_status,
            d.des_motivo,
            f.fun_id,
            f.fun_nome_completo,
            f.fun_cpf,
            f.fun_funcao
        FROM DES_DESLIGAMENTO d 
        INNER JOIN FUN_FUNCIONARIO f ON d.des_funcionario_id = f.fun_id 
        $where
        ORDER BY d.des_data_criacao DESC
        LIMIT $offset, $registros_por_pagina";

$result = mysqli_query($conn, $sql);

// Array para armazenar os resultados
$desligamentos = array();

// Mapeamento de tipos de desligamento
$tipos_desligamento = array(
    'demissao_sem_justa_causa' => 'Demissão sem Justa Causa',
    'demissao_com_justa_causa' => 'Demissão com Justa Causa',
    'pedido_demissao' => 'Pedido de Demissão',
    'termino_contrato' => 'Término de Contrato',
    'aposentadoria' => 'Aposentadoria',
    'morte' => 'Morte',
    'acordo_mutuo' => 'Acordo Mútuo'
);

// Mapeamento de status
$status_classes = array(
    'solicitado' => 'warning',
    'em_andamento' => 'info',
    'finalizado' => 'success',
    'cancelado' => 'danger'
);

$status_texto = array(
    'solicitado' => 'Solicitado',
    'em_andamento' => 'Em Andamento',
    'finalizado' => 'Finalizado',
    'cancelado' => 'Cancelado'
);

// Processa os resultados
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        // Formata datas
        $data_solicitacao = date('d/m/Y', strtotime($row['des_data_solicitacao']));
        $data_desligamento = date('d/m/Y', strtotime($row['des_data_desligamento']));
        
        // Formata CPF
        $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $row['fun_cpf']);
        
        // Determina o tipo e status
        $tipo = isset($tipos_desligamento[$row['des_tipo_desligamento']]) ? 
                $tipos_desligamento[$row['des_tipo_desligamento']] : 'Não definido';
        
        $status_class = isset($status_classes[$row['des_status']]) ? 
                       $status_classes[$row['des_status']] : 'secondary';
        
        $status = isset($status_texto[$row['des_status']]) ? 
                 $status_texto[$row['des_status']] : 'Indefinido';
        
        $desligamentos[] = array(
            'des_id' => $row['des_id'],
            'funcionario_id' => $row['fun_id'],
            'funcionario_nome' => $row['fun_nome_completo'],
            'funcionario_cpf' => $cpf,
            'funcionario_funcao' => $row['fun_funcao'],
            'data_solicitacao' => $data_solicitacao,
            'data_desligamento' => $data_desligamento,
            'tipo_desligamento' => $tipo,
            'tipo_desligamento_codigo' => $row['des_tipo_desligamento'],
            'status' => $status,
            'status_codigo' => $row['des_status'],
            'status_class' => $status_class,
            'motivo' => $row['des_motivo']
        );
    }
}

// Resposta JSON
$response = array(
    'status' => 'success',
    'data' => $desligamentos,
    'pagination' => array(
        'pagina_atual' => $pagina,
        'total_paginas' => $total_paginas,
        'total_registros' => $total_registros,
        'registros_por_pagina' => $registros_por_pagina
    )
);

echo json_encode($response);

mysqli_close($conn);
?>