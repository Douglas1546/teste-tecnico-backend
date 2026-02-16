<?php

namespace App\Repository;

use Carbon\Carbon;

class BlacklistRepository
{
    /**
     * Remove um email da blacklist se o tempo mínimo já passou
     * 
     * @param string $email
     * @return array ['sucesso' => bool, 'mensagem' => string]
     */
    public static function remover($email)
    {
        // Buscar registro ativo na blacklist
        $registro = \DB::table(config('database.email_schema') . '.em_black_list')
            ->where('black_list_mail', $email)
            ->whereNull('deleted_at')
            ->first();

        if (!$registro) {
            return [
                'sucesso' => false,
                'mensagem' => 'E-mail não encontrado na blacklist.'
            ];
        }

        // Verificar tempo mínimo
        $tempoMinimo = config('blacklist.min_removal_minutes', 5);
        $criadoEm = Carbon::parse($registro->created_at);
        $agora = Carbon::now();
        $minutosPassados = $criadoEm->diffInMinutes($agora);

        if ($minutosPassados < $tempoMinimo) {
            $minutosRestantes = $tempoMinimo - $minutosPassados;
            return [
                'sucesso' => false,
                'mensagem' => "Aguarde {$minutosRestantes} minuto(s) para remover este e-mail novamente."
            ];
        }

        // Fazer soft delete
        \DB::table(config('database.email_schema') . '.em_black_list')
            ->where('black_list_id', $registro->black_list_id)
            ->update(['deleted_at' => Carbon::now()]);

        return [
            'sucesso' => true
        ];
    }
}
