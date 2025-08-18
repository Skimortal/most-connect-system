<?php
namespace App\Model;

final class InvoiceFilter
{
    public ?\DateTimeImmutable $dateFrom = null;
    public ?\DateTimeImmutable $dateTo   = null;
    public ?float $totalMin = null;
    public ?float $totalMax = null;
}
