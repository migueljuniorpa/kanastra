<?php

namespace App\Services;

use App\Entities\BoletoEntity;
use App\Services\Contratcs\GeneratedBoletoMailInterface;
use Illuminate\Support\Facades\Log;

class GeneratedBoletoMail implements GeneratedBoletoMailInterface
{
    public function __construct(
        private readonly BoletoEntity $boletoEntity,
        private readonly string $email
    )
    {
    }

    /**
     * @return void
     */
    public function send(): void
    {
        Log::info("Email enviado");
    }
}
