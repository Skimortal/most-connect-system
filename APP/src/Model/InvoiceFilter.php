<?php
namespace App\Model;

use App\Enum\InvoiceStatus;

final class InvoiceFilter
{
    public ?\DateTimeImmutable $dateFrom = null;
    public ?\DateTimeImmutable $dateTo   = null;
    public ?float $totalMin = null;
    public ?float $totalMax = null;
    public ?InvoiceStatus $status = null;

}
