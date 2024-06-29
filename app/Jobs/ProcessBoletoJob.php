<?php

namespace App\Jobs;

use App\Services\GenerateBoleto;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBoletoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        protected array $data,
        protected string $checkpointFile
    )
    {}

    /**
     * Execute the job.
     * @throws Exception
     */
    public function handle(): void
    {
        GenerateBoleto::handle($this->data, $this->checkpointFile);
    }
}
