<?php

namespace App\Jobs;

use App\Entities\BoletoEntity;
use App\Models\BoletoFile;
use App\Services\Contratcs\GeneratedBoletoMailInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBoletoMailJob implements ShouldQueue
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
        foreach ($this->data as $item) {
            $this->sendMail($item);
        }
    }

    protected function sendMail(array $item): void
    {
        $boletoMail = app(
            GeneratedBoletoMailInterface::class,
            [
                'boletoEntity' => $this->processEntity($item),
                'email' => 'miguel.ii@live.com'
            ]
        );

        $boletoMail->send();
    }

    protected function processEntity(array $item): BoletoEntity
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
