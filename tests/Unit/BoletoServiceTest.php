<?php

namespace Tests\Unit;

use App\Services\BoletoService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\TestCase;

class BoletoServiceTest extends TestCase
{
    private BoletoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new BoletoService();
    }

    public function testHandleMethodThrowsExceptionOnStorageFailure()
    {
        // Mock a UploadedFile instance
        $file = UploadedFile::fake()->create('boletos.csv');

        // Mock the storage method to return false
        Storage::shouldReceive('disk->putFileAs')->andReturn(false);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Error storing file');

        $this->service->handle($file);
    }

    public function testProcessBoletosMethodCreatesCheckpointFile()
    {
        $filePath = 'app/upload/boletos/test.csv';

        // Create a mock CSV file for testing
        $csvContent = "name,governmentId,email,debtAmount,debtDueDate,debtID\n";
        $csvContent .= "John Doe,12345,johndoe@example.com,100,2024-07-01,987654\n";
        file_put_contents($filePath, $csvContent);

        // Call the method directly
        $this->service->processBoletos($filePath);

        // Assert that the checkpoint file was created
        $this->assertFileExists("{$filePath}-checkpoint.txt");

        // Clean up created files after testing
//        unlink($filePath);
//        unlink("{$filePath}-checkpoint.txt");
    }
}
