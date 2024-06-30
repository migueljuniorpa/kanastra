<?php

namespace App\Jobs;

use App\Entities\BoletoEntity;
use App\Models\Boleto;
use App\Services\Contratcs\BoletoGeneratorInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class GenereateBoletoPdfJob implements ShouldQueue
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
            $this->generateBoleto($item);
        }
    }

    protected function generateBoleto(array $item): bool
    {
        $generateBoleto = app(
            BoletoGeneratorInterface::class,
            ['boletoEntity' => $this->processEntity($item)]
        );

        return $generateBoleto->generate();
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
