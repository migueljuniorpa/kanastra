<?php

namespace App\Services\Contratcs;

interface BoletoGeneratorInterface
{
    /**
     * @return bool
     */
    public function generate(): bool;
}
