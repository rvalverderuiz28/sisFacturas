<?php

namespace App\Abstracts;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Translation\HasLocalePreference;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;

abstract class Export implements FromCollection, HasLocalePreference, ShouldAutoSize, ShouldQueue, WithHeadings, WithMapping, WithTitle
{
    use Exportable;

    public $ids;

    public $fields;
    public $fieldsHeaders;

    public $user;


    public function __construct($ids = null)
    {
        $this->ids = $ids;
        $fields = $this->fields();

        $this->fields = array_keys($fields);
        $this->fieldsHeaders = array_values($fields);

        $this->user = auth()->user();
    }

    public function title(): string
    {
        return Str::snake((new \ReflectionClass($this))->getShortName());
    }

    public function fields(): array
    {
        return [];
    }

    public function map($model): array
    {
        $map = [];

        $date_fields = ['paid_at', 'invoiced_at', 'billed_at', 'due_at', 'issued_at', 'created_at', 'transferred_at'];

        $evil_chars = ['=', '+', '-', '@'];

        foreach ($this->fields as $field) {
            $value = $model->$field;

            if (in_array($field, $date_fields)) {
                $value = ExcelDate::PHPToExcel(\Date::parse($value)->format('Y-m-d'));
            }

            // Prevent CSV injection https://security.stackexchange.com/a/190848
            if (Str::startsWith($value, $evil_chars)) {
                $value = "'" . $value;
            }

            $map[] = $value;
        }

        return $map;
    }

    public function headings(): array
    {
        return $this->fieldsHeaders;
    }

    public function prepareRows($rows)
    {
        return $rows;
    }

    public function preferredLocale()
    {
        return $this->user->locale;
    }

    public function failed(\Throwable $exception): void
    {
        report($exception);
    }
}
