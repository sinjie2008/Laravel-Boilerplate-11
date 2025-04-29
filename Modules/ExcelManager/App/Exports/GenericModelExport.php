<?php

namespace Modules\ExcelManager\App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class GenericModelExport implements FromCollection, WithHeadings
{
    protected $headings;

    /**
     * Constructor to accept headings.
     *
     * @param array $headings
     */
    public function __construct(array $headings)
    {
        $this->headings = $headings;
    }

    /**
     * Return an empty collection for the template.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection
    {
        return collect([]);
    }

    /**
     * Return the headings provided during instantiation.
     *
     * @return array
     */
    public function headings(): array
    {
        return $this->headings;
    }
}
