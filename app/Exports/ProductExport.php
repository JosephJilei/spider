<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class ProductExport implements FromArray
{
    protected $invoices;
 
    public function __construct(array $invoices)
    {
        $this->invoices = $invoices;
    }
 
    public function array(): array
    {
        // TODO: Implement array() method.
        return $this->invoices;
    }
}
