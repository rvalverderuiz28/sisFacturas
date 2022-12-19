<?php

namespace App\Abstracts;

use Carbon\Carbon;
use Illuminate\View\Component;

abstract class Widgets extends Component
{

    /**
     * @var \Illuminate\Support\Carbon
     */
    public $startDate;
    /**
     * @var \Illuminate\Support\Carbon
     */
    public $endDate;


    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->startDate = now()->startOfMonth();
        $this->endDate = now()->endOfMonth();

        if (request()->has("start_date") && request()->has("end_date")) {
            try {
                $this->startDate = Carbon::parse(request('start_date'));
                $this->endDate = Carbon::parse(request('end_date'));
            } catch (\Exception $ex) {
            }
        }
    }

    public function applyFilter($query, $column = 'created_at')
    {
        return $query->whereBetween($column, [
            $this->startDate->clone(),
            $this->endDate->clone()
        ]);
    }
}