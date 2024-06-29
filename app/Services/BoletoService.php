<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BoletoService
{
    protected string $filePath;

    /**
     * @throws Exception
     */
    public function __construct(protected UploadedFile $file)
    {}

    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->storeFile();
        $this->processBoletos();
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
     * @throws Exception
     */
    protected function processBoletos(): bool
    {
        if (!file_exists("{$this->filePath}-checkpoint.txt")) {
            file_put_contents("{$this->filePath}-checkpoint.txt", 0);
        }

        $chunkSize = 100000;
        $chunk = [];
        $checkpointFile = "{$this->filePath}-checkpoint.txt";
        $lastProcessedLine = (int)file_get_contents($checkpointFile) ?? 0;

        if (($handle = fopen($this->filePath, 'r')) !== false) {
            $currentLine = 0;

            while (!feof($handle)) {
                $line = fgetcsv($handle, '1000');

                $currentLine++;

                if ($currentLine <= $lastProcessedLine || !$line || $line['0'] === 'name') {
                    continue;
                }

                $chunk[$currentLine] = $this->parseData($line);

                if (count($chunk) == $chunkSize) {
                    file_put_contents($checkpointFile, $currentLine);

                    GenerateBoleto::handle($chunk, $checkpointFile);
                }
            }

            if (!empty($chunk)) {
                file_put_contents($checkpointFile, $currentLine);

                GenerateBoleto::handle($chunk, $checkpointFile);
            }

            fclose($handle);
        }

        return true;
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
}
