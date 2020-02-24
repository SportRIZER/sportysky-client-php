<?php

declare(strict_types=1);

namespace Sportrizer\Sportysky\Utils\Geo;

class Point
{
    public $lat;

    public $lng;

    public function __construct(float $latitude, float $longitude)
    {
        $this->lat = $latitude;
        $this->lng = $longitude;
    }
}
