<?php

namespace App\Services;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class RecordsService
{
    public function processRecords(UploadedFile $file): array
    {
        $chunkSize = 100;
        $chunk = [];

        if (($handle = fopen($file, 'r')) !== false) {
            while (!feof($handle)) {
                try {
                    $line = fgetcsv($handle, '1000');

                    if (!$line || $line[0] === 'name') {
                        continue;
                    }

                    $chunk[] = self::parseData($line);

                    if (count($chunk) == $chunkSize) {
//                        Log::debug('Inserted ' . count($chunk) . ' rows');
                        #self::insertData('sicor_operacao_basica_estado', $chunk);
//                        $chunk = [];

                        return $chunk;
                    }
                } catch (\Throwable $throwable) {
                    file_put_contents(
                        storage_path('logs/microdados.log'),
                        $throwable->getMessage() . PHP_EOL,
                        FILE_APPEND
                    );
                    continue;
                }
            }

//            if (count($chunk) > 0) {
//                Log::debug('Inserted ' . count($chunk) . ' rows');
                #self::insertData('sicor_operacao_basica_estado', $chunk);
//            }

            fclose($handle);
        }

        return $chunk;
    }

    private function parseData(array $line): array
    {
        return [
            'name' => $line[0],
            'governmentId' => $line[1],
            'email' => $line[2],
            'debtAmount' => $line[3],
            'debtDueDate' => $line[4],
            'debtID' => $line[5]
        ];
    }
}
