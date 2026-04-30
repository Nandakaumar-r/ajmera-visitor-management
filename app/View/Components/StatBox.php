<?php

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class StatBox extends Component
{
    public $title;
    public $value;

    /**
     * Create a new component instance.
     *
     * @param  string  $title
     * @param  string  $value
     */
    public function __construct($title, $value)
    {
        $this->title = $title;
        $this->value = $value;
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return View|string
     */
    public function render(): View|string
    {
        return view('components.stat-box');
    }
}
