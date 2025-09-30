<?php
require_once 'config/conexao.php'; // Incluir o arquivo de conexão

// Inicializar variável de resposta
$response = ['success' => false, 'message' => ''];

// Iniciar captura do buffer de saída para evitar impressão direta
ob_start();

// Ativar exibição de erros para depuração mas redirecionar para log
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'c:/xampp/htdocs/gbt/php_error.log');
error_reporting(E_ALL);

// Função para depuração - agora salva no log ao invés de imprimir
function debug($var, $exit = false) {
    $output = print_r($var, true);
    error_log($output);
    // Não imprimimos nada diretamente
}

// Obter os dados enviados via POST
$data = json_decode(file_get_contents('php://input'), true);
debug($data, false); // Visualizar os dados recebidos

if ($data !== null) {
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // Logar os dados recebidos para debug
    error_log("Dados de login recebidos - Email: " . $email . " | Senha: " . $password);

    // Conectar ao banco de dados já especificando o nome
    $conexao = connect_local_mysqli('gebert');

    if ($conexao) {
        // Preparar a query de forma segura usando prepared statements
        $stmt = $conexao->prepare("SELECT * FROM usu_usuario WHERE usu_email = ?");

        if ($stmt) {
            // Vincular parâmetros (s = string)
            $stmt->bind_param("s", $email);

            // Executar a query
            $execResult = $stmt->execute();
            debug("Execução da query: " . ($execResult ? "Sucesso" : "Falha"), false);
            
            // Obter o resultado
            $result = $stmt->get_result();
            debug("Número de linhas encontradas: " . $result->num_rows, false);

            if ($result->num_rows > 0) {
                $usuario = $result->fetch_assoc();
                
                // DEBUG: Mostrar todas as colunas disponíveis
                debug("Colunas disponíveis no resultado:");
                debug($usuario);
                
                // Descobrir o nome da coluna da senha
                $coluna_senha = '';
                foreach ($usuario as $coluna => $valor) {
                    // Procura por colunas que possam conter a senha
                    if (strpos(strtolower($coluna), 'senha') !== false || 
                        strpos(strtolower($coluna), 'password') !== false) {
                        $coluna_senha = $coluna;
                        break;
                    }
                }
                
                // Se encontrou uma coluna de senha
                if ($coluna_senha) {
                    debug("Coluna de senha encontrada: " . $coluna_senha);
                    
                    // Verificar a senha em texto puro
                    if ($password === $usuario[$coluna_senha]) {
                    // Identificar os nomes das colunas corretas
                    $coluna_id = '';
                    $coluna_nome = '';
                    $coluna_email = '';
                    
                    foreach ($usuario as $coluna => $valor) {
                        if (strpos(strtolower($coluna), 'id') !== false) $coluna_id = $coluna;
                        if (strpos(strtolower($coluna), 'nome') !== false || 
                            strpos(strtolower($coluna), 'name') !== false) $coluna_nome = $coluna;
                        if (strpos(strtolower($coluna), 'email') !== false || 
                            strpos(strtolower($coluna), 'mail') !== false) $coluna_email = $coluna;
                    }
                    
                    // Criar dados do usuário
                    $user_data = [
                        'id' => $usuario[$coluna_id] ?? '',
                        'nome' => $usuario[$coluna_nome] ?? '',
                        'email' => $usuario[$coluna_email] ?? ''
                    ];
                    
                    // Iniciar a sessão e salvar os dados
                    session_start();
                    $_SESSION['user_id'] = $user_data['id'];
                    $_SESSION['user'] = $user_data;
                    
                    $response = [
                        'success' => true,
                        'message' => 'Login realizado com sucesso',
                        'user' => $user_data
                    ];
                } else {
                    $response['message'] = 'Senha incorreta: a senha fornecida não corresponde à senha armazenada';
                }
                } else {
                    $response['message'] = 'Estrutura da tabela não reconhecida. Coluna de senha não encontrada.';
                }
            } else {
                $response['message'] = 'Usuário não encontrado'; // Removido o operador incorreto
                // Se quiser debugar, você pode usar: error_log("Resultado da consulta: " . print_r($result, true));
            }

            // Fechar o statement
            $stmt->close();
        } else {
            $response['message'] = 'Erro ao preparar consulta: ' . $conexao->error;
        }

        // Fechar a conexão
        $conexao->close();
    } else {
        $response['message'] = 'Erro ao conectar ao banco de dados';
    }
} else {
    $response['message'] = 'Dados inválidos';
}

// Limpar qualquer saída anterior
ob_end_clean();

// Enviar resposta como JSON
header('Content-Type: application/json');
echo json_encode($response);
exit;
