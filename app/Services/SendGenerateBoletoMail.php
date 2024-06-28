<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class SendGenerateBoletoMail
{
    /**
     * @throws Exception
     */
    static public function handle(array $data): void
    {
        Log::info("Email enviado");
    }
}
