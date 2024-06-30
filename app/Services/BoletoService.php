<?php

namespace App\Services;

use App\Entities\BoletoEntity;
use App\Services\Contratcs\BoletoGeneratorInterface;
use App\Services\Contratcs\GeneratedBoletoMailInterface;
use Exception;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Throwable;

class BoletoService
{
    protected string $filePath;

    /**
     * @param UploadedFile $file
     */
    public function __construct(protected UploadedFile $file)
    {}

    /**
     * @return void
     * @throws BindingResolutionException
     */
    public function handle(): void
    {
        $this->storeFile();
        $this->processFile();
    }

    /**
     * @return void
     * @throws Exception
     */
    protected function storeFile(): void
    {
        $path = "upload/boletos";
        $fileName = $this->file->getClientOriginalName();
        $this->filePath = storage_path("app/$path/$fileName");

        if (!$this->storageFile($this->file, $path, $fileName)) {
            throw new Exception('Error storing file');
        }
    }

    /**
     * @return bool
     */
    protected function processFile(): bool
    {
        if (!file_exists("{$this->filePath}-checkpoint.txt")) {
            file_put_contents("{$this->filePath}-checkpoint.txt", 0);
        }

        $chunkSize = 100000;
        $chunk = [];
        $checkpointFile = "{$this->filePath}-checkpoint.txt";
        $lastProcessedLine = (int)file_get_contents($checkpointFile) ?? 0;
        $processedLine = 0;
        $handle = fopen($this->filePath, 'r');

        if ($handle !== false) {
            $currentLine = 0;

            while (!feof($handle)) {
                $line = fgetcsv($handle, '1000');

                $currentLine++;

                if ($currentLine <= $lastProcessedLine || !$line || $line['0'] === 'name') {
                    continue;
                }

                $chunk[$currentLine] = $this->parseData($line);

                if (count($chunk) == $chunkSize) {
                    foreach ($chunk as $item) {
                        $generatedBoleto = $this->generateBoleto($item);

                        if ($generatedBoleto) {
                            $processedLine++;
                            $this->sendMail($item);
                        }
                    }
                }
            }

            if (!empty($chunk)) {
                foreach ($chunk as $item) {
                    $generatedBoleto = $this->generateBoleto($item);

                    if ($generatedBoleto) {
                        $processedLine++;
                        $this->sendMail($item);
                    }
                }
            }

            fclose($handle);
        }

        file_put_contents($checkpointFile, $processedLine);

        return true;
    }

    /**
     * @param array $item
     * @return bool
     */
    protected function generateBoleto(array $item): bool
    {
        $generateBoleto = app(
            BoletoGeneratorInterface::class,
            ['boletoEntity' => $this->generateBoletoEntity($item)]
        );

        return $generateBoleto->generate();
    }

    protected function sendMail(array $item): void
    {
        $boletoMail = app(
            GeneratedBoletoMailInterface::class,
            [
                'boletoEntity' => $this->generateBoletoEntity($item),
                'email' => 'miguel.ii@live.com'
            ]
        );

        $boletoMail->send();
    }

    /**
     * @param UploadedFile $file
     * @param string $path
     * @param string $name
     * @return bool
     */
    protected function storageFile(UploadedFile $file, string $path, string $name): bool
    {
        return Storage::disk('local')->putFileAs($path, $file, $name);
    }

    protected function parseData(array $line): array
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

    protected function generateBoletoEntity(array $item): BoletoEntity
    {
        $boletoEntity = new BoletoEntity();

        $boletoEntity->setName($item['name']);
        $boletoEntity->setGovernmentId($item['governmentId']);
        $boletoEntity->setEmail($item['email']);
        $boletoEntity->setDebtAmount($item['debtAmount']);
        $boletoEntity->setDebtDueDate($item['debtDueDate']);
        $boletoEntity->setDebtID($item['debtID']);

        return $boletoEntity;
    }
}
