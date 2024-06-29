<?php

namespace Http\Service\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class BoletoProcessTest extends TestCase
{
    use RefreshDatabase;


    public function testUploadBoletos()
    {
        $filePath = base_path('/tests/App/Http/Service/Files/inputTest.csv');

        $file = new UploadedFile(
            $filePath,
            'inputTest.csv',
            'application/pdf',
            null,
            true
        );

        $response = $this->post('api/upload/boletos', [
            'file' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Boletos processados com sucesso',
            ]);
    }
}
