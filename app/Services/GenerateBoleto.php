<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;

class GenerateBoleto
{
    /**
     * @throws Exception
     */
    static public function handle(array $data, string $checkpointFile): void
    {
        foreach ($data as $key => $item) {
            try {
                Log::info("Boleto gerado: {$item['debtID']}");

                SendGenerateBoletoMail::handle($item);
            } catch (\Throwable $throwable) {
                file_put_contents($checkpointFile, $key);

                throw new Exception($throwable->getMessage());
            }
        }
    }
}
