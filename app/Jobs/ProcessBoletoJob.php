<?php

namespace App\Jobs;

use App\Models\Boleto;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class ProcessBoletoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(protected array $data)
    {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $insertChunkSize = 1000;
        $boletos = array_chunk($this->data, $insertChunkSize);

        DB::transaction(function () use ($boletos): void {
            foreach ($boletos as $item) {
                Boleto::insert($item);
            }
        });

        GenereateBoletoPdfJob::dispatch($this->data)->onQueue('boletoPdf');
        SendBoletoMailJob::dispatch($this->data)->onQueue('mail');
    }
}
