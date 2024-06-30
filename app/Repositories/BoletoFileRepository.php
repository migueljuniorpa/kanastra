<?php

namespace App\Repositories;

use App\Models\BoletoFile;

class BoletoFileRepository
{
    static public function firstOrCreate(string $fileName, string $filePath, string $hash): BoletoFile
    {
        return BoletoFile::firstOrCreate([
            'name' => $fileName,
            'path' => $filePath,
            'file_hash' => $hash,
        ]);
    }
}
