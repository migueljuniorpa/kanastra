<?php

namespace Http\Service\Unit;

use App\Services\BoletoService;
use Dotenv\Dotenv;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use ReflectionClass;
use ReflectionException;
use Faker\Factory as Faker;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Artisan;

class BoletoServiceTest extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    protected function setUp(): void
    {
        parent::setUp();

        if (file_exists(__DIR__.'/../.env.testing')) {
            (new Dotenv(__DIR__.'/../', '.env.testing'))->overload();
        }

        Artisan::call('config:clear');

        Artisan::call('migrate', ['--env' => 'testing']);
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

        $service->shouldReceive('processFile')->once();

        $service->handle();
    }


    /**
     * @return void
     * @throws ReflectionException
     */
    public function testProcessFile()
    {
        $filePath = base_path('/tests/App/Http/Service/Files/random_data.csv');
        $data = $this->generateRandomData(10);
        $this->saveToCsv($filePath, $data);

        $file = new UploadedFile(
            $filePath,
            'random_data.csv',
            'application/pdf',
            null,
            true
        );

        $service = new BoletoService($file);

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('processFile');

        $method->invoke($service);

        Storage::disk('local')->assertExists("upload/boletos/{$file->getClientOriginalName()}");

        unlink($filePath);
    }

    /**
     * @return void
     * @throws ReflectionException
     */
    public function testProcessFileThrowsException()
    {
        $this->expectException(Exception::class);
        $file = Mockery::mock(UploadedFile::class);
        $service = Mockery::mock(BoletoService::class, [$file])->makePartial();

        $service->shouldReceive('processFile')->andReturn(false);

        $reflection = new ReflectionClass($service);
        $method = $reflection->getMethod('processFile');

        $method->invoke($service);
    }

    /**
     * @return void
     * @throws ReflectionException
     * @throws Exception
     */
    public function testProcessBoletos()
    {
        $filePath = base_path('/tests/App/Http/Service/Files/random_data.csv');
        $data = $this->generateRandomData(10);
        $this->saveToCsv($filePath, $data);

        $file = new UploadedFile(
            $filePath,
            'random_data.csv',
            'application/pdf',
            null,
            true
        );

        $service = new BoletoService($file);

        $reflection = new ReflectionClass($service);

        $methodprocessFile = $reflection->getMethod('processFile');

        $this->assertTrue($methodprocessFile->invoke($service));

        unlink($filePath);
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

    function generateRandomData($count = 10)
    {
        $faker = Faker::create();
        $data = [];

        // Cabeçalho do CSV
        $data[] = ['name', 'governmentId', 'email', 'debtAmount', 'debtDueDate', 'debtId'];

        for ($i = 0; $i < $count; $i++) {
            $data[] = [
                $faker->name,
                $faker->randomNumber(4),
                $faker->email,
                $faker->numberBetween(1000, 10000),
                $faker->date($format = 'Y-m-d', $max = 'now'),
                $faker->uuid,
            ];
        }

        return $data;
    }

    function saveToCsv($filename, $data)
    {
        $file = fopen($filename, 'w');

        foreach ($data as $row) {
            fputcsv($file, $row);
        }

        fclose($file);
    }
}
