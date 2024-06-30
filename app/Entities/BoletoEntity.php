<?php

namespace App\Entities;

readonly class BoletoEntity
{
    private string $name;
    private string $governmentId;
    private string $email;
    private float $debtAmount;
    private string $debtDueDate;
    private string $debtID;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName($name): void
    {
        $this->name = $name;
    }

    public function getGovernmentId(): string
    {
        return $this->governmentId;
    }

    public function setGovernmentId($governmentId): void
    {
        $this->governmentId = $governmentId;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail($email): void
    {
        $this->email = $email;
    }

    public function getDebtAmount(): float
    {
        return $this->debtAmount;
    }

    public function setDebtAmount($debtAmount): void
    {
        $this->debtAmount = $debtAmount;
    }

    public function getDebtDueDate(): string
    {
        return $this->debtDueDate;
    }

    public function setDebtDueDate($debtDueDate): void
    {
        $this->debtDueDate = $debtDueDate;
    }

    public function getDebtID(): string
    {
        return $this->debtID;
    }

    public function setDebtID($debtID): void
    {
        $this->debtID = $debtID;
    }
}
