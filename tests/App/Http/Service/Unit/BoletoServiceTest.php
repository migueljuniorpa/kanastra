<?php

namespace Http\Service\Unit;

use App\Services\BoletoService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use ReflectionClass;
use ReflectionException;
use Tests\TestCase;


class BoletoServiceTest extends TestCase
{
    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testConstruct()
    {
        $file = Mockery::mock(UploadedFile::class);
        $service = new BoletoService($file);

        $this->assertInstanceOf(BoletoService::class, $service);
    }

    /**
     * @return void
     * @throws Exception
     */
    public function testHandle()
    {
        $file = Mockery::mock(UploadedFile::class);

        $file->shouldReceive('getClientOriginalName')
            ->andReturn('fileTest.csv');
        $file->shouldReceive('getRealPath')
            ->andReturn(base_path('/tests/App/Http/Service/Files/fileTest.csv'));

        $service = Mockery::mock(BoletoService::class, [$file])
            ->shouldAllowMockingProtectedMethods()
            ->makePartial();

        $service->shouldReceive('storeFile')->once();
        $service->shouldReceive('processBoletos')->once();

        $service->handle();
    }

    /**
     * @return void
     * @throws ReflectionException
     * @throws Exception
     */
    public function testStoreFile()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('test.csv', 100);
        $service = new BoletoService($file);

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('storeFile');

        $method->invoke($service);

        Storage::disk('local')->assertExists("upload/boletos/{$file->getClientOriginalName()}");
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testStoreFileThrowsException()
    {
        $this->expectException(Exception::class);
        $file = Mockery::mock(UploadedFile::class);
        $service = Mockery::mock(BoletoService::class, [$file])->makePartial();

        $service->shouldReceive('storageFile')->andReturn(false);

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('storeFile');

        $method->invoke($service);
    }

    /**
     * @return void
     * @throws ReflectionException
     * @throws Exception
     */
    public function testProcessBoletos()
    {
        $filePath = base_path('/tests/App/Http/Service/Files/inputTest.csv');

        $file = new UploadedFile(
            $filePath,
            'inputTest.csv',
            'application/pdf',
            null,
            true
        );

        $service = new BoletoService($file);

        file_put_contents(storage_path('app/upload/boletos/fileTest-checkpoint.txt'), 0);

        $reflection = new ReflectionClass($service);
        $methodStoreFile = $reflection->getMethod('storeFile');
        $methodStoreFile->invoke($service);

        $method = $reflection->getMethod('processBoletos');

        $this->assertTrue($method->invoke($service));
    }

    /**
     * @return void
     * @throws ReflectionException
     * @throws Exception
     */
    public function testParseData()
    {
        $file = UploadedFile::fake()->create('fileTest.csv', 100);
        $service = new BoletoService($file);

        $data = [
            'Timothy Peters',
            '2908',
            'morrisjennifer@example.org',
            '7753',
            '2023-10-27',
            'de9181b2-749e-4b1b-92c3-5a4dc1962fb1'
        ];

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('parseData');

        $result = $method->invokeArgs($service, [$data]);

        $this->assertIsArray($result);
    }
}
