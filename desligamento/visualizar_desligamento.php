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

// Verifica se foi fornecido um ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $response = array('status' => 'error', 'message' => 'ID do desligamento não fornecido');
    echo json_encode($response);
    exit;
}

$desligamento_id = intval($_GET['id']);

// Busca os dados do desligamento
$sql = "SELECT 
            d.*,
            f.fun_nome_completo,
            f.fun_cpf,
            f.fun_rg,
            f.fun_funcao,
            f.fun_salario,
            f.fun_data_admissao,
            f.fun_telefone,
            f.fun_email,
            f.fun_status,
            u1.usu_nome as solicitante_nome,
            u2.usu_nome as aprovador_nome
        FROM DES_DESLIGAMENTO d 
        INNER JOIN FUN_FUNCIONARIO f ON d.des_funcionario_id = f.fun_id 
        LEFT JOIN usu_usuario u1 ON d.des_usuario_solicitante = u1.usu_id
        LEFT JOIN usu_usuario u2 ON d.des_usuario_aprovacao = u2.usu_id
        WHERE d.des_id = '$desligamento_id'";

$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) == 0) {
    $response = array('status' => 'error', 'message' => 'Desligamento não encontrado');
    echo json_encode($response);
    exit;
}

$desligamento = mysqli_fetch_assoc($result);

// Busca o histórico do desligamento
$sql_historico = "SELECT 
                     h.*,
                     u.usu_nome as usuario_nome
                  FROM DES_HISTORICO h
                  LEFT JOIN usu_usuario u ON h.his_usuario_id = u.usu_id
                  WHERE h.his_desligamento_id = '$desligamento_id'
                  ORDER BY h.his_data_acao DESC";

$result_historico = mysqli_query($conn, $sql_historico);
$historico = array();

while ($row = mysqli_fetch_assoc($result_historico)) {
    $historico[] = array(
        'acao' => $row['his_acao'],
        'usuario' => $row['usuario_nome'],
        'observacoes' => $row['his_observacoes'],
        'data' => date('d/m/Y H:i', strtotime($row['his_data_acao']))
    );
}

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

// Mapeamento de aviso prévio
$aviso_previo = array(
    'trabalhado' => 'Trabalhado',
    'indenizado' => 'Indenizado',
    'nao_aplicavel' => 'Não Aplicável'
);

// Mapeamento de status
$status_texto = array(
    'solicitado' => 'Solicitado',
    'em_andamento' => 'Em Andamento',
    'finalizado' => 'Finalizado',
    'cancelado' => 'Cancelado'
);

// Formata os dados para resposta
$dados_formatados = array(
    'desligamento' => array(
        'id' => $desligamento['des_id'],
        'data_solicitacao' => date('d/m/Y', strtotime($desligamento['des_data_solicitacao'])),
        'data_desligamento' => date('d/m/Y', strtotime($desligamento['des_data_desligamento'])),
        'tipo_desligamento' => $tipos_desligamento[$desligamento['des_tipo_desligamento']] ?? 'Não definido',
        'tipo_codigo' => $desligamento['des_tipo_desligamento'],
        'motivo' => $desligamento['des_motivo'],
        'observacoes' => $desligamento['des_observacoes'],
        'aviso_previo' => $aviso_previo[$desligamento['des_aviso_previo']] ?? 'Não definido',
        'aviso_previo_codigo' => $desligamento['des_aviso_previo'],
        'dias_aviso_previo' => $desligamento['des_dias_aviso_previo'],
        'status' => $status_texto[$desligamento['des_status']] ?? 'Indefinido',
        'status_codigo' => $desligamento['des_status'],
        'solicitante' => $desligamento['solicitante_nome'],
        'aprovador' => $desligamento['aprovador_nome'],
        'data_aprovacao' => $desligamento['des_data_aprovacao'] ? date('d/m/Y H:i', strtotime($desligamento['des_data_aprovacao'])) : null,
        'data_criacao' => date('d/m/Y H:i', strtotime($desligamento['des_data_criacao'])),
        'documentos_entregues' => $desligamento['des_documentos_entregues'] ? json_decode($desligamento['des_documentos_entregues'], true) : null,
        'equipamentos_devolvidos' => $desligamento['des_equipamentos_devolvidos'] ? json_decode($desligamento['des_equipamentos_devolvidos'], true) : null
    ),
    'funcionario' => array(
        'id' => $desligamento['des_funcionario_id'],
        'nome' => $desligamento['fun_nome_completo'],
        'cpf' => preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $desligamento['fun_cpf']),
        'rg' => $desligamento['fun_rg'],
        'funcao' => $desligamento['fun_funcao'],
        'salario' => 'R$ ' . number_format($desligamento['fun_salario'], 2, ',', '.'),
        'data_admissao' => date('d/m/Y', strtotime($desligamento['fun_data_admissao'])),
        'telefone' => $desligamento['fun_telefone'],
        'email' => $desligamento['fun_email'],
        'status_atual' => $desligamento['fun_status']
    ),
    'historico' => $historico
);

$response = array(
    'status' => 'success',
    'data' => $dados_formatados
);

echo json_encode($response);

mysqli_close($conn);
?>