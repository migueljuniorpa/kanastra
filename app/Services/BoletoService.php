<?php

namespace App\Services;

use App\Jobs\GenereateBoletoPdfJob;
use App\Jobs\ProcessBoletoJob;
use App\Jobs\SendBoletoMailJob;
use App\Models\BoletoFile;
use App\Repositories\BoletoFileRepository;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class BoletoService
{
    /**
     * @param UploadedFile $file
     */
    public function __construct(protected UploadedFile $file)
    {}

    /**
     * @return void
     * @throws Exception
     */
    public function handle(): void
    {
        $this->processFile();
    }

    /**
     * @return bool
     * @throws Exception
     */
    protected function processFile(): bool
    {
        $path = "upload/boletos";
        $fileName = $this->file->getClientOriginalName();
        $boletoFile = $this->storeFile($this->file, $path, $fileName);

        $chunkSize = 100000;
        $handle = fopen(storage_path($boletoFile->path), 'r');
        $chunk = [];

        if ($handle !== false) {
            $currentLine = 0;

            while (!feof($handle)) {
                $line = fgetcsv($handle, '1000');

                $currentLine++;

                if (!$line || $line['0'] === 'name') {
                    continue;
                }

                $chunk[$currentLine] = $this->parseData($line);

                if (count($chunk) === $chunkSize) {
                    ProcessBoletoJob::dispatch($chunk)->onQueue('boletos');
                    $chunk = [];
                }
            }

            if (!empty($chunk)) {
                ProcessBoletoJob::dispatch($chunk)->onQueue('boletos');
            }

            fclose($handle);
        }

        return true;
    }

    /**
     * @param UploadedFile $file
     * @param string $path
     * @param string $name
     * @return BoletoFile
     * @throws Exception
     */
    protected function storeFile(UploadedFile $file, string $path, string $name): BoletoFile
    {
        $fileContent = file_get_contents($file);

        if (empty($fileContent)) {
            throw new Exception('File is empty', 422);
        }

        $fileHash = hash('sha256', $fileContent);

        $filePath = Storage::disk('local')->putFileAs($path, $file, $name);

        if (!$filePath) {
            throw new Exception('Error storing file');
        }

        return BoletoFileRepository::firstOrCreate($name, "app/$path/$name", $fileHash);
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
