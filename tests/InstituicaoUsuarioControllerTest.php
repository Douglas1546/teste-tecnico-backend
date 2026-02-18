<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;
use Carbon\Carbon;

class InstituicaoUsuarioControllerTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();

        Config::set('database.censo_schema', 'censo');
        Config::set('database.compartilhados_schema', 'compartilhados');
        Config::set('database.email_schema', 'email');
        Config::set('blacklist.min_removal_minutes', 5);

        // Limpar tabelas (desativando FKs para evitar erros)
        DB::connection('censo')->statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::connection('compartilhados')->statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::connection('email')->statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::connection('censo')->table('instituicao_usuarios')->truncate();
        DB::connection('censo')->table('usuario_perfil')->truncate();
        DB::connection('censo')->table('instituicao_perfil')->truncate();
        DB::connection('compartilhados')->table('id_usuarios')->truncate();
        DB::connection('email')->table('em_black_list')->truncate();

        DB::connection('censo')->statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::connection('compartilhados')->statement('SET FOREIGN_KEY_CHECKS=1;');
        DB::connection('email')->statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->seedData();
    }

    private function seedData()
    {
        // 1. Inserir Usuário (Compartilhados)
        DB::connection('compartilhados')->table('id_usuarios')->insert([
            'usua_id' => 1,
            'usua_nome' => 'Teste User',
            'usua_email' => 'teste@exemplo.com',
            'usua_login' => 'testeuser',
            'usua_cpf' => '12345678901',
            'usua_gerar_senha_token' => 'token',
            'usua_requisicao_token' => 1
        ]);

        // 2. Inserir Perfil (Censo)
        DB::connection('censo')->table('instituicao_perfil')->insert([
            'perf_id' => 1,
            'perf_descricao' => 'Admin',
            'perf_codigo' => 'ADM',
            'inst_codigo' => 1,
            'usua_tipo_id' => 1
        ]);

        // 3. Inserir Vínculo Instituição (Censo)
        DB::connection('censo')->table('instituicao_usuarios')->insert([
            'inst_usua_id' => 1,
            'inst_codigo' => 1,
            'usua_id' => 1,
            'inst_usua_codigo' => '1001',
            'inst_usua_funcao' => 'Gestor'
        ]);

        // 4. Inserir Vínculo Perfil (Censo)
        DB::connection('censo')->table('usuario_perfil')->insert([
            'usua_perf_id' => 1,
            'inst_usua_id' => 1,
            'perf_id' => 1,
            'ano_leti_id' => 2023
        ]);
    }

    /** @test */
    public function test_listar_usuarios()
    {
        // Header inst_codigo é obrigatório pelo controller
        $this->json('GET', '/instituicao_usuarios/listar', [], ['HTTP_inst_codigo' => 1]);

        $this->seeStatusCode(200);
        $this->seeJsonStructure([
            'success',
            'data' => [
                '*' => ['inst_usua_id', 'usua_nome', 'usua_email']
            ]
        ]);
        $this->seeJson(['usua_email' => 'teste@exemplo.com']);
    }

    /** @test */
    public function test_obter_usuario_por_id()
    {
        $this->json('GET', '/instituicao_usuarios/byid', ['inst_usua_id' => 1]);

        $this->seeStatusCode(200);
        $this->seeJson(['usua_id' => '1', 'usuario_email' => 'teste@exemplo.com']);
    }

    /** @test */
    public function test_editar_usuario()
    {
        // Alterar nome e email
        $payload = [
            'inst_usua_id' => 1,
            'usua_nome' => 'Teste Alterado',
            'usua_email' => 'novo@exemplo.com'
        ];

        $this->json('PUT', '/instituicao_usuarios/editar', $payload, ['HTTP_inst_codigo' => 1]);

        $this->seeStatusCode(200);

        // Verificar no banco
        $usuario = DB::connection('compartilhados')->table('id_usuarios')->where('usua_id', 1)->first();
        $this->assertEquals('Teste Alterado', $usuario->usua_nome);
        $this->assertEquals('novo@exemplo.com', $usuario->usua_email);
    }

    /** @test */
    public function test_remover_da_blacklist_via_endpoint()
    {
        // Inserir na blacklist (antigo o suficiente para remover)
        DB::connection('email')->table('em_black_list')->insert([
            'black_list_mail' => 'teste@exemplo.com',
            'created_at' => Carbon::now()->subMinutes(10),
            'black_list_contador' => 1
        ]);

        $this->json('DELETE', '/instituicao_usuarios/blacklist/remover', ['email' => 'teste@exemplo.com']);

        $this->seeStatusCode(200);
        $this->seeJson(['success' => true]);

        // Verificar soft delete
        $inBlacklist = DB::connection('email')->table('em_black_list')
            ->where('black_list_mail', 'teste@exemplo.com')
            ->whereNull('deleted_at')
            ->exists();

        $this->assertFalse($inBlacklist);
    }
}
