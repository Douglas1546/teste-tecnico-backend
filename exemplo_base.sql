-- Criação dos bancos
CREATE DATABASE IF NOT EXISTS compartilhados CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci;
CREATE DATABASE IF NOT EXISTS censo CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci;
CREATE DATABASE IF NOT EXISTS email CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci;
-- CREATE DATABASE IF NOT EXISTS semchamada CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci;

-- Conceder permissões ao usuário censo_user apenas para os bancos usados pela aplicação
GRANT ALL PRIVILEGES ON censo.* TO 'censo_user'@'%';
GRANT ALL PRIVILEGES ON compartilhados.* TO 'censo_user'@'%';
GRANT ALL PRIVILEGES ON email.* TO 'censo_user'@'%';

USE compartilhados;

-- Tabela id_usuarios
CREATE TABLE IF NOT EXISTS id_usuarios (
  usua_id int NOT NULL AUTO_INCREMENT,
  usua_pluri_id_chave varchar(40) DEFAULT NULL,
  usua_nome varchar(255) NOT NULL,
  usua_login varchar(150) NOT NULL,
  usua_email varchar(150) DEFAULT NULL,
  usua_cpf varchar(11) DEFAULT NULL,
  usua_sexo varchar(1) DEFAULT NULL,
  usua_foto varchar(255) DEFAULT NULL,
  usua_foto_miniatura varchar(255) DEFAULT NULL,
  usua_data_nascimento date DEFAULT NULL,
  usua_senha varchar(64) DEFAULT NULL,
  usua_senha_antiga varchar(40) DEFAULT NULL,
  usua_salt_senha varchar(60) DEFAULT NULL,
  usua_gerar_senha tinyint(1) NOT NULL DEFAULT '0',
  usua_gerar_senha_token varchar(40) NOT NULL,
  usua_gerar_senha_sist_id int DEFAULT NULL,
  usua_ativo tinyint(1) NOT NULL DEFAULT '1',
  usua_facebook_token varchar(255) DEFAULT NULL,
  usua_googleplus_token varchar(255) DEFAULT NULL,
  usua_tipo_id int DEFAULT NULL,
  usua_requisicao_token int NOT NULL,
  usua_datalimite_semsenha datetime DEFAULT NULL,
  usua_facebook_id varchar(255) DEFAULT NULL,
  usua_google_id varchar(255) DEFAULT NULL,
  updated_at datetime DEFAULT NULL,
  usua_email_verificado tinyint DEFAULT '0',
  usua_apple_user varchar(255) DEFAULT NULL,
  leitor_parc_id int DEFAULT NULL,
  usua_interno tinyint DEFAULT '0',
  PRIMARY KEY (usua_id),
  UNIQUE KEY usua_pluri_id_chave_UNIQUE (usua_pluri_id_chave),
  KEY IDX_PluriID_Email (usua_email),
  KEY id_usuarios_usua_login_IDX (usua_login),
  KEY id_usuarios_usua_cpf_IDX (usua_cpf)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dados para id_usuarios
INSERT INTO id_usuarios (usua_nome, usua_login, usua_email, usua_cpf, usua_gerar_senha_token, usua_requisicao_token)
VALUES
('João da Silva', 'joaosilva', 'joao@exemplo.com', '12345678901', 'token1', 111),
('Maria Souza', 'mariasouza', 'maria@exemplo.com', '10987654321', 'token2', 222);

USE censo;

-- Tabela instituicao_perfil
CREATE TABLE IF NOT EXISTS instituicao_perfil (
  perf_id int NOT NULL AUTO_INCREMENT,
  perf_descricao varchar(60) DEFAULT NULL,
  perf_codigo varchar(10) DEFAULT NULL,
  inst_codigo int NOT NULL,
  usua_tipo_id int NOT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  deleted_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (perf_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dados para instituicao_perfil
INSERT INTO instituicao_perfil (perf_descricao, perf_codigo, inst_codigo, usua_tipo_id)
VALUES
('Diretor', 'DIR', 1, 1),
('Professor', 'PROF', 1, 2);

-- Tabela instituicao_usuarios
CREATE TABLE IF NOT EXISTS instituicao_usuarios (
  inst_usua_id int NOT NULL AUTO_INCREMENT,
  inst_codigo int NOT NULL,
  usua_id int NOT NULL,
  inst_usua_codigo varchar(150) DEFAULT NULL,
  inst_usua_idioma char(1) DEFAULT NULL,
  inst_usua_indicador_email int DEFAULT '1',
  inst_usua_indicador_push int DEFAULT '1',
  inst_usua_indicador_push_mens int NOT NULL DEFAULT '1',
  inst_usua_indicador_push_solicitacoes int NOT NULL DEFAULT '1',
  inst_usua_indicador_agenda_google int DEFAULT NULL,
  inst_usua_cadastro_automatico int NOT NULL DEFAULT '0',
  inst_usua_funcao varchar(150) DEFAULT NULL,
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  deleted_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (inst_usua_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dados para instituicao_usuarios
INSERT INTO instituicao_usuarios (inst_codigo, usua_id, inst_usua_codigo, inst_usua_idioma, inst_usua_funcao)
VALUES
(1, 1, '20230001', 'P', 'Diretor'),
(1, 2, '20230002', 'P', 'Professor');

-- Tabela usuario_perfil
CREATE TABLE IF NOT EXISTS usuario_perfil (
  usua_perf_id int NOT NULL AUTO_INCREMENT,
  inst_usua_id int NOT NULL,
  perf_id int NOT NULL,
  ano_leti_id int NOT NULL,
  usua_perf_cadastro_automatico tinyint NOT NULL DEFAULT '0',
  created_at timestamp NULL DEFAULT NULL,
  updated_at timestamp NULL DEFAULT NULL,
  deleted_at timestamp NULL DEFAULT NULL,
  PRIMARY KEY (usua_perf_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dados para usuario_perfil
INSERT INTO usuario_perfil (inst_usua_id, perf_id, ano_leti_id, usua_perf_cadastro_automatico)
VALUES
(1, 1, 2023, 0),
(2, 2, 2023, 0);

USE email;

-- Tabela em_black_list
CREATE TABLE IF NOT EXISTS em_black_list (
  black_list_id int NOT NULL AUTO_INCREMENT,
  black_list_mail varchar(50) NOT NULL,
  black_list_data_inclusao datetime NOT NULL,
  black_list_contador int NOT NULL DEFAULT '1',
  black_list_aws_mesage text,
  black_list_motivo varchar(50) DEFAULT NULL,
  PRIMARY KEY (black_list_id),
  KEY IDX_MA_Email (black_list_mail)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3;

-- Dados para em_black_list
INSERT INTO em_black_list (black_list_mail, black_list_data_inclusao, black_list_contador, black_list_aws_mesage, black_list_motivo)
VALUES
('joao@exemplo.com', NOW(), 1, 'Mensagem AWS', 'Motivo'),
('maria@exemplo.com', NOW(), 2, 'Mensagem AWS 2', 'Motivo 2');