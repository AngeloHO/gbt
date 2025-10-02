-- Script simplificado para criar tabelas do sistema de EPI
-- Execute este script no phpMyAdmin ou MySQL

USE gebert;

-- Tabela de EPIs (versão simplificada)
CREATE TABLE IF NOT EXISTS EPI_EQUIPAMENTOS (
    epi_id INT AUTO_INCREMENT PRIMARY KEY,
    epi_nome VARCHAR(255) NOT NULL COMMENT 'Nome do EPI',
    epi_descricao TEXT COMMENT 'Descrição detalhada do EPI',
    epi_categoria VARCHAR(100) NOT NULL COMMENT 'Categoria do EPI (ex: Capacete, Luva, etc)',
    epi_fabricante VARCHAR(255) COMMENT 'Nome do fabricante',
    epi_tamanho VARCHAR(50) COMMENT 'Tamanho disponível (P, M, G, XG, etc)',
    epi_observacoes TEXT COMMENT 'Observações sobre o EPI',
    epi_status ENUM('ativo', 'inativo') DEFAULT 'ativo' COMMENT 'Status do EPI',
    epi_usuario_cadastro INT COMMENT 'ID do usuário que cadastrou',
    epi_data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data do cadastro',
    epi_usuario_atualizacao INT COMMENT 'ID do usuário que fez a última atualização',
    epi_data_atualizacao DATETIME DEFAULT NULL COMMENT 'Data da última atualização',
    -- Mantemos estes campos para compatibilidade com código existente
    epi_estoque_atual INT DEFAULT 1,
    epi_estoque_minimo INT DEFAULT 1
);

-- Tabela de entregas de EPI para funcionários
CREATE TABLE IF NOT EXISTS EPI_ENTREGAS (
    entrega_id INT AUTO_INCREMENT PRIMARY KEY,
    entrega_funcionario_id INT NOT NULL COMMENT 'ID do funcionário que recebeu',
    entrega_epi_id INT NOT NULL COMMENT 'ID do EPI entregue',
    entrega_quantidade INT NOT NULL DEFAULT 1 COMMENT 'Quantidade entregue',
    entrega_data_entrega DATE NOT NULL COMMENT 'Data da entrega',
    entrega_data_prevista_devolucao DATE COMMENT 'Data prevista para devolução/troca',
    entrega_data_devolucao DATE COMMENT 'Data real da devolução (quando devolvido)',
    entrega_motivo_entrega TEXT COMMENT 'Motivo da entrega',
    entrega_observacoes TEXT COMMENT 'Observações sobre a entrega',
    entrega_assinatura_funcionario BOOLEAN DEFAULT FALSE COMMENT 'Se o funcionário assinou o recebimento',
    entrega_status ENUM('entregue', 'devolvido', 'perdido', 'danificado') DEFAULT 'entregue' COMMENT 'Status da entrega',
    entrega_usuario_entrega INT NOT NULL COMMENT 'ID do usuário que fez a entrega',
    entrega_data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data do registro',
    entrega_usuario_atualizacao INT COMMENT 'ID do usuário que fez a última atualização',
    entrega_data_atualizacao DATETIME DEFAULT NULL COMMENT 'Data da última atualização',
    
    -- Chaves estrangeiras
    FOREIGN KEY (entrega_funcionario_id) REFERENCES FUN_FUNCIONARIO(fun_id) ON DELETE CASCADE,
    FOREIGN KEY (entrega_epi_id) REFERENCES EPI_EQUIPAMENTOS(epi_id) ON DELETE CASCADE
);

-- Tabela de categorias de EPI (para organização)
CREATE TABLE IF NOT EXISTS EPI_CATEGORIAS (
    categoria_id INT AUTO_INCREMENT PRIMARY KEY,
    categoria_nome VARCHAR(100) NOT NULL UNIQUE COMMENT 'Nome da categoria',
    categoria_descricao TEXT COMMENT 'Descrição da categoria',
    categoria_status ENUM('ativo', 'inativo') DEFAULT 'ativo' COMMENT 'Status da categoria',
    categoria_usuario_cadastro INT COMMENT 'ID do usuário que cadastrou',
    categoria_data_cadastro TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Data do cadastro'
);

-- Inserir categorias padrão de EPI
INSERT INTO EPI_CATEGORIAS (categoria_nome, categoria_descricao, categoria_usuario_cadastro) VALUES
('Proteção para Cabeça', 'Capacetes, bonés, toucas, etc.', 1),
('Proteção para Olhos e Face', 'Óculos de segurança, viseiras, máscaras de solda, etc.', 1),
('Proteção Auditiva', 'Protetores auriculares, abafadores de ruído, etc.', 1),
('Proteção Respiratória', 'Máscaras, respiradores, filtros, etc.', 1),
('Proteção para Mãos e Braços', 'Luvas de segurança, mangotes, cremes protetores, etc.', 1),
('Proteção para Pés e Pernas', 'Calçados de segurança, perneiras, meias, etc.', 1),
('Proteção do Corpo Inteiro', 'Macacões, aventais, coletes, cintos de segurança, etc.', 1),
('Proteção contra Quedas', 'Cinturões, talabartes, mosquetões, etc.', 1);

-- Inserir alguns EPIs de exemplo (versão simplificada)
INSERT INTO EPI_EQUIPAMENTOS (epi_nome, epi_descricao, epi_categoria, epi_fabricante, epi_tamanho, epi_usuario_cadastro) VALUES
('Capacete de Segurança', 'Capacete de segurança para proteção contra impactos', 'Proteção para Cabeça', 'Plastcor', 'Único', 1),
('Luva de Segurança', 'Luva de proteção para as mãos', 'Proteção para Mãos e Braços', 'Danny', 'G', 1),
('Botina de Segurança', 'Botina de couro com bico de aço', 'Proteção para Pés e Pernas', 'Marluvas', '42', 1),
('Óculos de Segurança', 'Óculos de proteção com lente incolor', 'Proteção para Olhos e Face', '3M', 'Único', 1),
('Protetor Auricular', 'Protetor auricular de inserção', 'Proteção Auditiva', 'Honeywell', 'Único', 1);

-- Verificar as tabelas criadas
SHOW TABLES LIKE 'EPI_%';