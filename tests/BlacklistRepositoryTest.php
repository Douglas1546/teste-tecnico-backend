<?php

use App\Repository\BlacklistRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Config;

class BlacklistRepositoryTest extends TestCase
{
    public function setUp()
    {
        parent::setUp();
        // Configurar schema do banco de dados para testes
        Config::set('database.email_schema', 'email');
        Config::set('blacklist.min_removal_minutes', 5);

        // Limpar tabela antes de cada teste
        DB::connection('email')->table('em_black_list')->truncate();
    }

    /** @test */
    public function deve_retornar_erro_se_email_nao_existir_na_blacklist()
    {
        $resultado = BlacklistRepository::remover('naoexiste@teste.com');

        $this->assertFalse($resultado['sucesso']);
        $this->assertEquals('E-mail não encontrado na blacklist.', $resultado['mensagem']);
    }

    /** @test */
    public function deve_impedir_remocao_antes_do_tempo_minimo()
    {
        // Inserir registro criado agora (0 minutos atrás)
        DB::connection('email')->table('em_black_list')->insert([
            'black_list_mail' => 'recente@teste.com',
            'created_at' => Carbon::now(),
            'black_list_contador' => 1
        ]);

        $resultado = BlacklistRepository::remover('recente@teste.com');

        $this->assertFalse($resultado['sucesso']);
        $this->assertContains('Aguarde', $resultado['mensagem']);
    }

    /** @test */
    public function deve_remover_email_se_tempo_minimo_passou()
    {
        // Inserir registro criado há 10 minutos (limite é 5)
        DB::connection('email')->table('em_black_list')->insert([
            'black_list_mail' => 'antigo@teste.com',
            'created_at' => Carbon::now()->subMinutes(10),
            'black_list_contador' => 1
        ]);

        $resultado = BlacklistRepository::remover('antigo@teste.com');

        $this->assertTrue($resultado['sucesso']);

        // Verificar soft delete
        $registro = DB::connection('email')->table('em_black_list')
            ->where('black_list_mail', 'antigo@teste.com')
            ->first();

        $this->assertNotNull($registro->deleted_at);
    }
}
