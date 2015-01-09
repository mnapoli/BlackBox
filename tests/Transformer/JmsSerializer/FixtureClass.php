<?php

namespace Tests\BlackBox\Transformer\JmsSerializer;

class FixtureClass
{
    /**
     * @var string
     */
    public $property;

    public function __construct($property)
    {
        $this->property = $property;
    }
}
