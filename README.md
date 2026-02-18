# Documentação - API Censo Backend

## Visão Geral

API REST desenvolvida em **Lumen (Laravel)** para gerenciamento de usuários de instituições educacionais com sistema de blacklist de e-mails controlado por tempo.

**Funcionalidades principais:**
- Listagem e consulta de usuários
- Atualização de dados de usuário
- Gerenciamento de blacklist de e-mails com tempo mínimo de permanência

---

## Configuração

### Variáveis de Ambiente (.env)

- Copie o arquivo `.env.example` para `.env`
- Configure as variáveis do banco de dados no `.env` conforme necessário:
- Preferencialmente utilize `DB_USERNAME=censo_user` para não ter problemas com permissões, foi colocado permissões para o usuario com este nome na hora da criação do banco de dados no arquivo "exemplo_base.sql" caso contrário siga as instruções no topico "Como Executar" no final desta documentação (Foi feito isso apenas para facilitar a execução do projeto pelo avaliador).
```env
APP_ENV=local
APP_DEBUG=true
APP_KEY=

DB_CONNECTION=censo
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=censo
DB_USERNAME=<usuario>
DB_PASSWORD=<senha>
DB_ROOT_PASSWORD=root123
# DB_SEMCHAMADA_DATABASE=semchamada

DB_COMPARTILHADOS_DATABASE=compartilhados
DB_CENSO=censo
DB_EMAIL_DATABASE=email

CACHE_DRIVER=file
QUEUE_DRIVER=sync
APP_TIMEZONE=America/Recife

BLACKLIST_MIN_REMOVAL_MINUTES=5
```

### Bancos de Dados

O projeto utiliza **3 bancos distintos**:
- **compartilhados**: Dados dos usuários (`id_usuarios`)
- **censo**: Vínculos instituição-usuário (`instituicao_usuarios`, `usuario_perfil`)
- **email**: Blacklist de e-mails (`em_black_list`)

---

## Endpoints da API

**Base URL:** `/instituicao_usuarios`  
**Header obrigatório:** `inst_codigo: <código_da_instituição>`

### 1. GET /byid
Retorna dados detalhados de um usuário.

**Query Params:**
- `inst_usua_id` (integer, obrigatório)

**Resposta (200):**
```json
{
    "success": true,
    "data": {
        "inst_usua_id": 1,
        "usua_id": "1",
        "usuario_nome": "JoÃ£o da Silva",
        "usuario_email": "joao@exemplo.com",
        "usuario_codigo": "20230001",
        "usuario_cpf": "12345678901",
        "usuario_funcao": "Diretor",
        "usuario_sexo": null,
        "usuario_idioma": "P",
        "email_blacklist": 1,
        "can_remove_from_blacklist": 1,
        "usua_foto": null,
        "usua_foto_miniatura": null,
        "data_nascimento": "",
        "usuario_telefones": [],
        "usuario_perfil": [
            {
                "perf_id": 1,
                "perf_descricao": "Diretor",
                "usua_tipo_id": 1
            }
        ]
    },
    "message": ""
}
```

---

### 2. GET /listar
Lista usuários da instituição com paginação.

**Query Params:**
- `offset` (integer, opcional) - Para paginação

**Header Obrigatório:**
- `inst_codigo: <id_da_instituicao>`

**Resposta (200):**
```json
{
    "success": true,
    "data": [
        {
            "inst_usua_id": 1,
            "inst_usua_codigo": "20230001",
            "usua_nome": "JoÃ£o da Silva",
            "usua_email": "joao@exemplo.com",
            "usuario_perfil": "Diretor",
            "email_blacklist": 0,
            "can_remove_from_blacklist": 0
        },
        {
            "inst_usua_id": 2,
            "inst_usua_codigo": "20230002",
            "usua_nome": "Maria Souza",
            "usua_email": "maria@exemplo.com.br",
            "usuario_perfil": "Professor",
            "email_blacklist": 1,
            "can_remove_from_blacklist": 1
        }
    ],
    "message": ""
}
```

**Paginação:**
- 1ª página: 100 registros
- Demais páginas: 50 registros

---

### 3. PUT /editar
Atualiza dados do usuário. Se o e-mail mudar, atualiza também na blacklist.

**Header Obrigatório:**
- `inst_codigo: <id_da_instituicao>`

**Body:**
```json
{
  "inst_usua_id": 123,
  "usua_nome": "João da Silva Junior",
  "usua_email": "joao.junior@example.com"
}
```

**Validações:**
- `inst_usua_id`: obrigatório, numérico
- `usua_nome`: obrigatório, string, máx 255 caracteres
- `usua_email`: obrigatório, e-mail válido, máx 255 caracteres

**Resposta (200):**
```json
{
  "success": true,
  "message": "Usuário atualizado com sucesso."
}
```

**Erros:**
- `400`: Validação falhou
- `404`: Usuário não encontrado

---

### 4. DELETE /blacklist/remover
Remove e-mail da blacklist (soft delete) respeitando tempo mínimo.

- É necessário inserir dados na tabela `em_black_list` na coluna `created_at`, preencha utilizando esse comando no phpMyAdmin:

```sql
UPDATE em_black_list SET created_at = '2026-02-17 14:30:00' WHERE created_at IS NULL;
```

**Body:**
```json
{
  "email": "usuario@example.com"
}
```

**Resposta (200):**
```json
{
  "success": true,
  "message": "E-mail removido da blacklist com sucesso."
}
```

**Erros:**
- `400`: "E-mail não encontrado na blacklist"
- `400`: "Aguarde X minuto(s) para remover este e-mail novamente"

---

## Histórico de Desenvolvimento

**Correções SQL**
- Fix: Compatibilidade GROUP BY com MAX()
- Fix: Conexão padrão para 'censo'

**Sistema de Blacklist**
- Configuração de migrações
- Configuração de tempo mínimo (5 min)
- Migration: Adiciona `created_at` e `deleted_at` à blacklist

**Endpoint de Remoção**
- Implementa DELETE /blacklist/remover
- Adiciona `can_remove_from_blacklist` na listagem
- Filtro de soft delete no JOIN

**Models Eloquent**
- Model BlackList
- Relacionamento `emBlackList` em IdUsuarios

**Endpoint de Edição**
- Implementa PUT /editar com sync de blacklist
- Adiciona `can_remove_from_blacklist` no byId

---


## Regras de Negócio

### Sistema de Blacklist

1. **Tempo Mínimo**: E-mail deve permanecer na blacklist por no mínimo 5 minutos (configurável)
2. **Soft Delete**: Remoções são lógicas (`deleted_at`), preservando histórico
3. **Sincronização**: Alteração de e-mail atualiza automaticamente a blacklist
4. **Validação Temporal**: Campo `can_remove_from_blacklist` indica se tempo mínimo foi atingido

### Cálculo do Tempo
```php
$minutosPassados = Carbon::parse($created_at)->diffInMinutes(Carbon::now());
if ($minutosPassados >= config('blacklist.min_removal_minutes', 5)) {
}
```

---

## Tratamento de Erros

### Padrão de Resposta de Erro
```json
{
  "success": false,
  "data": "",
  "message": "<descrição do erro>"
}
```

### Códigos HTTP
- `200`: Sucesso
- `400`: Erro de validação ou regra de negócio
- `404`: Recurso não encontrado

---

## Como Executar


```bash
# Subir os containers
docker-compose up --build -d

# 3) Instale dependências dentro do container
docker exec -u root censo-backend composer install --no-interaction --prefer-dist --optimize-autoloader


# Executar as migrations
# Na primeira execução, é necessário criar a tabela de controle de migrations:
docker exec censo-backend php artisan migrate:install

# Executar migration
docker exec censo-backend php artisan migrate

# API disponível em:
http://localhost:8000

# Observação Importante
# Caso seja necessário resetar o ambiente:
docker-compose down -v
docker-compose up --build -d
```

## Banco de Dados
```bash
# O MySQL é executado em container Docker.
# Para facilitar a visualização e administração do banco, foi configurado o phpMyAdmin.
# Acesso ao phpMyAdmin disponível em:
http://localhost:8080
```

### Se você não tiver colocado o `DB_USERNAME=censo_user` no arquivo .env, você terá que criar as permissões manualmente. Para isso, basta acessar o phpMyAdmin e executar os comandos abaixo (substitua "seu-usuario" pelo seu usuário):

```sql
GRANT ALL PRIVILEGES ON censo.* TO 'seu-usuario'@'%';
GRANT ALL PRIVILEGES ON compartilhados.* TO 'seu-usuario'@'%';
GRANT ALL PRIVILEGES ON email.* TO 'seu-usuario'@'%';
```

# Dados para Teste da Blacklist (IMPORTANTE!)

### Para testar o endpoint de remoção da blacklist, é necessário inserir dados na tabela `em_black_list` na coluna `created_at`, preencha utilizando esse comando no phpMyAdmin:

```sql
UPDATE em_black_list SET created_at = '2026-02-17 14:30:00' WHERE created_at IS NULL;
```



# Testes
### observação importante
- Caso você rode os testes, reinicie o banco de dados para poder usar o sistema normalmente.


```bash
# Executar todos os testes
docker exec censo-backend vendor/bin/phpunit

# Executar teste específico
docker exec censo-backend vendor/bin/phpunit BlacklistRepositoryTest.php
```


