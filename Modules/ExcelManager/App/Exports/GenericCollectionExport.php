<?php

namespace Modules\ExcelManager\App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping; // Optional: for formatting data

class GenericCollectionExport implements FromCollection, WithHeadings, WithMapping
{
    protected $collection;
    protected $headings;

    /**
     * Constructor to accept the data collection.
     *
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
        // Determine headings from the first item's keys if available
        if ($this->collection->isNotEmpty()) {
            $firstItem = $this->collection->first();
            // If it's an Eloquent model, get visible attributes; otherwise, get array keys
            $this->headings = method_exists($firstItem, 'getAttributes') ? array_keys($firstItem->getAttributes()) : array_keys((array) $firstItem);
        } else {
            $this->headings = ['No Data']; // Fallback if collection is empty
        }
    }

    /**
     * Return the data collection.
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection(): Collection
    {
        return $this->collection;
    }

    /**
     * Return the headings determined during instantiation.
     *
     * @return array
     */
    public function headings(): array
    {
        return $this->headings;
    }

    /**
     * Map the data for each row.
     * This ensures the data aligns with the headings.
     *
     * @param mixed $row
     * @return array
     */
    public function map($row): array
    {
        $mappedData = [];
        $rowData = method_exists($row, 'getAttributes') ? $row->getAttributes() : (array) $row;

        foreach ($this->headings as $heading) {
            // Handle potential missing keys gracefully
            $mappedData[] = $rowData[$heading] ?? null;
        }
        return $mappedData;
    }
} 