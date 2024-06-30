<?php

namespace App\Services;

use App\Entities\BoletoEntity;
use App\Services\Contratcs\BoletoGeneratorInterface;
use Exception;
use Illuminate\Support\Facades\Log;

class BoletoGeneratePdf implements BoletoGeneratorInterface
{
    public function __construct(private readonly BoletoEntity $boletoEntity)
    {
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function generate(): bool
    {
        $logData = "Boleto gerado: {$this->boletoEntity->getDebtID()}";

        Log::info($logData);

        return true;
    }
}
