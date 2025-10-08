-- Criação da tabela para controle de desligamentos
-- Autor: Sistema Gebert
-- Data: 2025-10-08

CREATE TABLE IF NOT EXISTS DES_DESLIGAMENTO (
    des_id INT PRIMARY KEY AUTO_INCREMENT,
    des_funcionario_id INT NOT NULL,
    des_data_solicitacao DATE NOT NULL,
    des_data_desligamento DATE NOT NULL,
    des_tipo_desligamento ENUM(
        'demissao_sem_justa_causa',
        'demissao_com_justa_causa', 
        'pedido_demissao',
        'termino_contrato',
        'aposentadoria',
        'morte',
        'acordo_mutuo'
    ) NOT NULL,
    des_motivo TEXT,
    des_observacoes TEXT,
    des_aviso_previo ENUM('trabalhado', 'indenizado', 'nao_aplicavel') DEFAULT 'nao_aplicavel',
    des_dias_aviso_previo INT DEFAULT 0,
    des_ferias_vencidas DECIMAL(10,2) DEFAULT 0.00,
    des_ferias_proporcionais DECIMAL(10,2) DEFAULT 0.00,
    des_decimo_terceiro DECIMAL(10,2) DEFAULT 0.00,
    des_multa_fgts DECIMAL(10,2) DEFAULT 0.00,
    des_saldo_salario DECIMAL(10,2) DEFAULT 0.00,
    des_outros_valores DECIMAL(10,2) DEFAULT 0.00,
    des_valor_total DECIMAL(10,2) DEFAULT 0.00,
    des_status ENUM('solicitado', 'em_andamento', 'finalizado', 'cancelado') DEFAULT 'solicitado',
    des_usuario_solicitante INT NOT NULL,
    des_usuario_aprovacao INT NULL,
    des_data_aprovacao DATETIME NULL,
    des_documentos_entregues TEXT NULL, -- JSON com lista de documentos
    des_equipamentos_devolvidos TEXT NULL, -- JSON com lista de equipamentos/EPIs
    des_data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP,
    des_data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Chaves estrangeiras
    FOREIGN KEY (des_funcionario_id) REFERENCES FUN_FUNCIONARIO(fun_id) ON DELETE CASCADE,
    
    -- Índices para melhor performance
    INDEX idx_funcionario (des_funcionario_id),
    INDEX idx_data_desligamento (des_data_desligamento),
    INDEX idx_tipo (des_tipo_desligamento),
    INDEX idx_status (des_status),
    INDEX idx_data_criacao (des_data_criacao)
);

-- Tabela para histórico de alterações no desligamento
CREATE TABLE IF NOT EXISTS DES_HISTORICO (
    his_id INT PRIMARY KEY AUTO_INCREMENT,
    his_desligamento_id INT NOT NULL,
    his_acao ENUM('criacao', 'atualizacao', 'aprovacao', 'cancelamento', 'finalizacao') NOT NULL,
    his_usuario_id INT NOT NULL,
    his_observacoes TEXT,
    his_data_acao DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (his_desligamento_id) REFERENCES DES_DESLIGAMENTO(des_id) ON DELETE CASCADE,
    
    INDEX idx_desligamento (his_desligamento_id),
    INDEX idx_data_acao (his_data_acao)
);

-- Inserir alguns tipos padrão de documentos e equipamentos (opcional)
CREATE TABLE IF NOT EXISTS DES_TIPOS_DOCUMENTOS (
    td_id INT PRIMARY KEY AUTO_INCREMENT,
    td_nome VARCHAR(255) NOT NULL,
    td_descricao TEXT,
    td_obrigatorio BOOLEAN DEFAULT FALSE,
    td_ativo BOOLEAN DEFAULT TRUE,
    td_data_criacao DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Inserir documentos padrão
INSERT INTO DES_TIPOS_DOCUMENTOS (td_nome, td_descricao, td_obrigatorio) VALUES
('Termo de Rescisão', 'Documento oficial de rescisão do contrato', TRUE),
('CTPS', 'Carteira de Trabalho e Previdência Social', TRUE),
('CD de TRCT', 'Comunicação de Dispensa e Termo de Rescisão do Contrato de Trabalho', TRUE),
('Exame Demissional', 'Atestado de Saúde Ocupacional demissional', TRUE),
('Chaves e Cartões', 'Devolução de chaves e cartões de acesso', TRUE),
('Uniformes', 'Devolução de uniformes da empresa', FALSE),
('Crachá', 'Devolução do crachá de identificação', TRUE),
('Equipamentos de Trabalho', 'Devolução de equipamentos, ferramentas, etc.', FALSE);

-- Trigger para atualizar status do funcionário automaticamente
DELIMITER $$

CREATE TRIGGER trg_desligamento_status_funcionario
AFTER UPDATE ON DES_DESLIGAMENTO
FOR EACH ROW
BEGIN
    -- Se o desligamento foi finalizado, marca funcionário como inativo
    IF NEW.des_status = 'finalizado' AND OLD.des_status != 'finalizado' THEN
        UPDATE FUN_FUNCIONARIO 
        SET fun_status = 'inativo' 
        WHERE fun_id = NEW.des_funcionario_id;
    END IF;
    
    -- Se o desligamento foi cancelado e o funcionário estava inativo, reativa
    IF NEW.des_status = 'cancelado' AND OLD.des_status = 'finalizado' THEN
        UPDATE FUN_FUNCIONARIO 
        SET fun_status = 'ativo' 
        WHERE fun_id = NEW.des_funcionario_id AND fun_status = 'inativo';
    END IF;
END$$

DELIMITER ;