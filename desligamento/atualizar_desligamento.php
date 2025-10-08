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

// Verifica se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $desligamento_id = intval($_POST['desligamento_id']);
    $acao = mysqli_real_escape_string($conn, $_POST['acao']);
    $observacoes = mysqli_real_escape_string($conn, $_POST['observacoes'] ?? '');
    $usuario_id = $_SESSION['user_id'];
    
    // Verifica se o desligamento existe
    $sql_check = "SELECT des_id, des_status FROM DES_DESLIGAMENTO WHERE des_id = '$desligamento_id'";
    $result_check = mysqli_query($conn, $sql_check);
    
    if (mysqli_num_rows($result_check) == 0) {
        $response = array('status' => 'error', 'message' => 'Desligamento não encontrado');
        echo json_encode($response);
        exit;
    }
    
    $desligamento = mysqli_fetch_assoc($result_check);
    $status_atual = $desligamento['des_status'];
    
    // Define o novo status baseado na ação
    $novo_status = $status_atual;
    $mensagem_acao = '';
    
    switch ($acao) {
        case 'aprovar':
            if ($status_atual == 'solicitado') {
                $novo_status = 'em_andamento';
                $mensagem_acao = 'Desligamento aprovado';
                // Atualiza também o usuário de aprovação e data
                $sql_update = "UPDATE DES_DESLIGAMENTO SET 
                              des_status = '$novo_status',
                              des_usuario_aprovacao = '$usuario_id',
                              des_data_aprovacao = NOW()
                              WHERE des_id = '$desligamento_id'";
            } else {
                $response = array('status' => 'error', 'message' => 'Desligamento não pode ser aprovado no status atual');
                echo json_encode($response);
                exit;
            }
            break;
            
        case 'finalizar':
            if ($status_atual == 'em_andamento') {
                $novo_status = 'finalizado';
                $mensagem_acao = 'Desligamento finalizado';
                
                // Atualiza o status do desligamento
                $sql_update = "UPDATE DES_DESLIGAMENTO SET des_status = '$novo_status' WHERE des_id = '$desligamento_id'";
                
                // Também atualiza o status do funcionário para inativo
                $sql_update_funcionario = "UPDATE FUN_FUNCIONARIO SET fun_status = 'inativo' 
                                          WHERE fun_id = (SELECT des_funcionario_id FROM DES_DESLIGAMENTO WHERE des_id = '$desligamento_id')";
            } else {
                $response = array('status' => 'error', 'message' => 'Desligamento não pode ser finalizado no status atual');
                echo json_encode($response);
                exit;
            }
            break;
            
        case 'cancelar':
            if ($status_atual != 'finalizado') {
                $novo_status = 'cancelado';
                $mensagem_acao = 'Desligamento cancelado';
                $sql_update = "UPDATE DES_DESLIGAMENTO SET des_status = '$novo_status' WHERE des_id = '$desligamento_id'";
            } else {
                $response = array('status' => 'error', 'message' => 'Desligamento finalizado não pode ser cancelado');
                echo json_encode($response);
                exit;
            }
            break;
            
        case 'reabrir':
            if ($status_atual == 'cancelado') {
                $novo_status = 'solicitado';
                $mensagem_acao = 'Desligamento reaberto';
                
                // Atualiza o status do desligamento
                $sql_update = "UPDATE DES_DESLIGAMENTO SET des_status = '$novo_status' WHERE des_id = '$desligamento_id'";
                
                // Também reativa o funcionário se ele estiver inativo
                $sql_update_funcionario = "UPDATE FUN_FUNCIONARIO SET fun_status = 'ativo' 
                                          WHERE fun_id = (SELECT des_funcionario_id FROM DES_DESLIGAMENTO WHERE des_id = '$desligamento_id') 
                                          AND fun_status = 'inativo'";
            } else {
                $response = array('status' => 'error', 'message' => 'Apenas desligamentos cancelados podem ser reabertos');
                echo json_encode($response);
                exit;
            }
            break;
            
        default:
            $response = array('status' => 'error', 'message' => 'Ação não reconhecida');
            echo json_encode($response);
            exit;
    }
    
    // Executa a atualização
    if (mysqli_query($conn, $sql_update)) {
        
        // Se for finalização ou reabertura, também atualiza o status do funcionário
        if (($acao == 'finalizar' || $acao == 'reabrir') && isset($sql_update_funcionario)) {
            if (!mysqli_query($conn, $sql_update_funcionario)) {
                $response = array('status' => 'error', 'message' => 'Erro ao atualizar status do funcionário: ' . mysqli_error($conn));
                echo json_encode($response);
                exit;
            }
        }
        
        // Registra no histórico
        $observacoes_historico = !empty($observacoes) ? $observacoes : $mensagem_acao;
        if ($acao == 'finalizar') {
            $observacoes_historico .= ' - Funcionário marcado como inativo';
        } elseif ($acao == 'reabrir') {
            $observacoes_historico .= ' - Funcionário reativado';
        }
        
        $sql_historico = "INSERT INTO DES_HISTORICO (his_desligamento_id, his_acao, his_usuario_id, his_observacoes) 
                         VALUES ('$desligamento_id', '$acao', '$usuario_id', '$observacoes_historico')";
        mysqli_query($conn, $sql_historico);
        
        $mensagem_final = $mensagem_acao . ' com sucesso!';
        if ($acao == 'finalizar') {
            $mensagem_final .= ' Funcionário marcado como inativo.';
        } elseif ($acao == 'reabrir') {
            $mensagem_final .= ' Funcionário reativado.';
        }
        
        $response = array(
            'status' => 'success', 
            'message' => $mensagem_final,
            'novo_status' => $novo_status
        );
    } else {
        $response = array('status' => 'error', 'message' => 'Erro ao atualizar desligamento: ' . mysqli_error($conn));
    }
    
    echo json_encode($response);
    
} elseif ($_SERVER['REQUEST_METHOD'] == 'PUT') {
    
    // Para atualizações de dados do desligamento
    parse_str(file_get_contents("php://input"), $_PUT);
    
    $desligamento_id = intval($_PUT['desligamento_id']);
    $campo = mysqli_real_escape_string($conn, $_PUT['campo']);
    $valor = mysqli_real_escape_string($conn, $_PUT['valor']);
    $usuario_id = $_SESSION['user_id'];
    
    // Campos permitidos para atualização
    $campos_permitidos = array(
        'des_data_desligamento' => 'Data de desligamento',
        'des_motivo' => 'Motivo',
        'des_observacoes' => 'Observações',
        'des_aviso_previo' => 'Aviso prévio',
        'des_dias_aviso_previo' => 'Dias de aviso prévio',
        'des_documentos_entregues' => 'Documentos entregues',
        'des_equipamentos_devolvidos' => 'Equipamentos devolvidos'
    );
    
    if (!isset($campos_permitidos[$campo])) {
        $response = array('status' => 'error', 'message' => 'Campo não permitido para atualização');
        echo json_encode($response);
        exit;
    }
    
    // Verifica se o desligamento existe e pode ser editado
    $sql_check = "SELECT des_id, des_status FROM DES_DESLIGAMENTO WHERE des_id = '$desligamento_id'";
    $result_check = mysqli_query($conn, $sql_check);
    
    if (mysqli_num_rows($result_check) == 0) {
        $response = array('status' => 'error', 'message' => 'Desligamento não encontrado');
        echo json_encode($response);
        exit;
    }
    
    $desligamento = mysqli_fetch_assoc($result_check);
    
    if ($desligamento['des_status'] == 'finalizado') {
        $response = array('status' => 'error', 'message' => 'Desligamento finalizado não pode ser editado');
        echo json_encode($response);
        exit;
    }
    
    // Atualiza o campo
    $sql_update = "UPDATE DES_DESLIGAMENTO SET $campo = '$valor' WHERE des_id = '$desligamento_id'";
    
    if (mysqli_query($conn, $sql_update)) {
        
        // Registra no histórico
        $nome_campo = $campos_permitidos[$campo];
        $observacoes_historico = "Campo '$nome_campo' atualizado";
        $sql_historico = "INSERT INTO DES_HISTORICO (his_desligamento_id, his_acao, his_usuario_id, his_observacoes) 
                         VALUES ('$desligamento_id', 'atualizacao', '$usuario_id', '$observacoes_historico')";
        mysqli_query($conn, $sql_historico);
        
        $response = array(
            'status' => 'success', 
            'message' => 'Campo atualizado com sucesso!'
        );
    } else {
        $response = array('status' => 'error', 'message' => 'Erro ao atualizar campo: ' . mysqli_error($conn));
    }
    
    echo json_encode($response);
    
} else {
    $response = array('status' => 'error', 'message' => 'Método não permitido');
    echo json_encode($response);
}

mysqli_close($conn);
?>