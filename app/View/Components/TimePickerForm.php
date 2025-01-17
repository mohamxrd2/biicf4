<?php

namespace App\View\Components;

use Illuminate\View\Component;

class TimePickerForm extends Component
{
    public $title;
    public $dateId;
    public $timeId;
    public $periodId;
    public $dateModel;
    public $timeModel;
    public $periodModel;
    public $dateLabel;

    public function __construct($title, $dateId, $timeId, $periodId, $dateModel, $timeModel, $periodModel, $dateLabel)
    {
        $this->title = $title;
        $this->dateId = $dateId;
        $this->timeId = $timeId;
        $this->periodId = $periodId;
        $this->dateModel = $dateModel;
        $this->timeModel = $timeModel;
        $this->periodModel = $periodModel;
        $this->dateLabel = $dateLabel;
    }

    public function render()
    {
        return view('components.time-picker-form');
    }
}
