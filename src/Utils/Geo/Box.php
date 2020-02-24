<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky\Utils\Geo;

class Box
{
    public $point1;

    public $point2;

    public function __construct(Point $point1, Point $point2)
    {
        $this->point1 = $point1;
        $this->point2 = $point2;
    }
}
