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

// Parâmetros de busca e filtro
$busca = isset($_GET['busca']) ? mysqli_real_escape_string($conn, $_GET['busca']) : '';
$filtro_status = isset($_GET['status']) ? mysqli_real_escape_string($conn, $_GET['status']) : '';
$filtro_tipo = isset($_GET['tipo']) ? mysqli_real_escape_string($conn, $_GET['tipo']) : '';

try {
    // Primeiro, vamos verificar se a view existe
    $sql_check_view = "SHOW TABLES LIKE 'VW_ASO_FUNCIONARIO'";
    $result_check = mysqli_query($conn, $sql_check_view);
    
    $usar_view = (mysqli_num_rows($result_check) > 0);
    
    // Condições de busca
    $where = "WHERE 1=1";
    
    if ($usar_view) {
        // Usando a view
        $table_source = "VW_ASO_FUNCIONARIO";
        
        if (!empty($busca)) {
            $where .= " AND (
                FUNCIONARIO_NOME LIKE '%$busca%' OR 
                FUNCIONARIO_CPF LIKE '%$busca%'
            )";
        }

        if (!empty($filtro_status)) {
            $where .= " AND STATUS_VENCIMENTO = '$filtro_status'";
        }

        if (!empty($filtro_tipo)) {
            $where .= " AND ASO_TIPO_EXAME = '$filtro_tipo'";
        }
    } else {
        // Usando JOIN direto das tabelas
        $table_source = "FUN_ASO a INNER JOIN FUN_FUNCIONARIO f ON a.ASO_FUNCIONARIO_ID = f.FUN_ID";
        $where .= " AND f.FUN_STATUS = 'ativo' AND a.ASO_STATUS = 'ATIVO'";
        
        if (!empty($busca)) {
            $where .= " AND (
                f.FUN_NOME_COMPLETO LIKE '%$busca%' OR 
                f.FUN_CPF LIKE '%$busca%'
            )";
        }

        if (!empty($filtro_tipo)) {
            $where .= " AND a.ASO_TIPO_EXAME = '$filtro_tipo'";
        }
        
        // Para filtro de status, precisamos calcular na query
        if (!empty($filtro_status)) {
            switch($filtro_status) {
                case 'VENCIDO':
                    $where .= " AND a.ASO_DATA_VALIDADE < CURDATE()";
                    break;
                case 'VENCE_30_DIAS':
                    $where .= " AND DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 30 AND a.ASO_DATA_VALIDADE >= CURDATE()";
                    break;
                case 'VENCE_60_DIAS':
                    $where .= " AND DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 60 AND DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) > 30";
                    break;
                case 'VIGENTE':
                    $where .= " AND DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) > 60";
                    break;
            }
        }
    }

    // Consulta para contar o total de registros
    $sql_count = "SELECT COUNT(*) as total FROM $table_source $where";
    $result_count = mysqli_query($conn, $sql_count);
    
    if (!$result_count) {
        throw new Exception('Erro na consulta de contagem: ' . mysqli_error($conn));
    }
    
    $row_count = mysqli_fetch_assoc($result_count);
    $total_registros = $row_count['total'];

    // Calcula o total de páginas
    $total_paginas = ceil($total_registros / $registros_por_pagina);

    // Consulta para buscar os registros
    if ($usar_view) {
        // Query usando a view
        $sql = "SELECT 
                    ASO_ID as aso_id,
                    ASO_FUNCIONARIO_ID as funcionario_id,
                    FUNCIONARIO_NOME as funcionario_nome,
                    FUNCIONARIO_CPF as funcionario_cpf,
                    FUNCIONARIO_FUNCAO as funcionario_funcao,
                    ASO_TIPO_EXAME as tipo_exame,
                    ASO_DATA_EXAME as data_exame,
                    ASO_DATA_VALIDADE as data_validade,
                    ASO_RESULTADO as resultado,
                    ASO_MEDICO_RESPONSAVEL as medico_responsavel,
                    ASO_CRM_MEDICO as crm_medico,
                    ASO_CLINICA_EXAME as clinica_exame,
                    ASO_OBSERVACOES as observacoes,
                    ASO_RESTRICOES as restricoes,
                    ASO_EXAMES_REALIZADOS as exames_realizados,
                    ASO_NUMERO_DOCUMENTO as numero_documento,
                    ASO_ARQUIVO_PATH as arquivo_path,
                    ASO_STATUS as status_aso,
                    STATUS_VENCIMENTO as status_vencimento,
                    DIAS_PARA_VENCIMENTO as dias_para_vencimento,
                    ASO_CREATED_AT as created_at,
                    ASO_UPDATED_AT as updated_at
                FROM $table_source 
                $where
                ORDER BY ASO_DATA_VALIDADE ASC, FUNCIONARIO_NOME ASC
                LIMIT $offset, $registros_por_pagina";      
    } else {
        // Query usando JOIN direto
        $sql = "SELECT 
                    a.ASO_ID as aso_id,
                    a.ASO_FUNCIONARIO_ID as funcionario_id,
                    f.FUN_NOME_COMPLETO as funcionario_nome,
                    f.FUN_CPF as funcionario_cpf,
                    f.FUN_FUNCAO as funcionario_funcao,
                    a.ASO_TIPO_EXAME as tipo_exame,
                    a.ASO_DATA_EXAME as data_exame,
                    a.ASO_DATA_VALIDADE as data_validade,
                    a.ASO_RESULTADO as resultado,
                    a.ASO_MEDICO_RESPONSAVEL as medico_responsavel,
                    a.ASO_CRM_MEDICO as crm_medico,
                    a.ASO_CLINICA_EXAME as clinica_exame,
                    a.ASO_OBSERVACOES as observacoes,
                    a.ASO_RESTRICOES as restricoes,
                    a.ASO_EXAMES_REALIZADOS as exames_realizados,
                    a.ASO_NUMERO_DOCUMENTO as numero_documento,
                    a.ASO_ARQUIVO_PATH as arquivo_path,
                    a.ASO_STATUS as status_aso,
                    a.ASO_CREATED_AT as created_at,
                    a.ASO_UPDATED_AT as updated_at,
                    -- Calcular status de vencimento
                    CASE 
                        WHEN a.ASO_DATA_VALIDADE < CURDATE() THEN 'VENCIDO'
                        WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) <= 30 THEN 'VENCE_30_DIAS'
                        WHEN DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) BETWEEN 31 AND 60 THEN 'VENCE_60_DIAS'
                        ELSE 'VIGENTE'
                    END as status_vencimento,
                    DATEDIFF(a.ASO_DATA_VALIDADE, CURDATE()) as dias_para_vencimento
                FROM $table_source 
                $where
                ORDER BY a.ASO_DATA_VALIDADE ASC, f.FUN_NOME_COMPLETO ASC
                LIMIT $offset, $registros_por_pagina";
    }

    $result = mysqli_query($conn, $sql);
    
    if (!$result) {
        throw new Exception('Erro na consulta principal: ' . mysqli_error($conn));
    }
    
    $asos = array();

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Formatar CPF
            $cpf = preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $row['funcionario_cpf']);
            
            $asos[] = array(
                'aso_id' => $row['aso_id'],
                'funcionario_id' => $row['funcionario_id'],
                'funcionario_nome' => $row['funcionario_nome'],
                'funcionario_cpf' => $cpf,
                'funcionario_funcao' => $row['funcionario_funcao'],
                'tipo_exame' => $row['tipo_exame'],
                'data_exame' => $row['data_exame'],
                'data_validade' => $row['data_validade'],
                'resultado' => $row['resultado'],
                'medico_responsavel' => $row['medico_responsavel'],
                'crm_medico' => $row['crm_medico'],
                'clinica_exame' => $row['clinica_exame'],
                'observacoes' => $row['observacoes'],
                'restricoes' => $row['restricoes'],
                'exames_realizados' => $row['exames_realizados'],
                'numero_documento' => $row['numero_documento'],
                'arquivo_path' => $row['arquivo_path'],
                'status_aso' => $row['status_aso'],
                'status_vencimento' => $row['status_vencimento'],
                'dias_para_vencimento' => $row['dias_para_vencimento'],
                'created_at' => $row['created_at'],
                'updated_at' => $row['updated_at']
            );
        }
    }

    $response = array(
        'status' => 'success',
        'asos' => $asos,
        'paginacao' => array(
            'pagina_atual' => $pagina,
            'total_paginas' => $total_paginas,
            'registros_por_pagina' => $registros_por_pagina,
            'total_registros' => $total_registros
        )
    );

} catch (Exception $e) {
    $response = array(
        'status' => 'error',
        'message' => 'Erro ao buscar ASO: ' . $e->getMessage()
    );
}

// Fecha a conexão
mysqli_close($conn);

// Retorna os resultados como JSON
header('Content-Type: application/json');
echo json_encode($response);
?>