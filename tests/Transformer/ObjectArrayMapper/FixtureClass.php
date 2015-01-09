<?php

namespace Tests\BlackBox\Transformer\ObjectArrayMapper;

class FixtureClass
{
    public $public;
    protected $protected;
    private $private;

    public static $static = 'hello';

    public function __construct($public, $protected, $private)
    {
        $this->public = $public;
        $this->protected = $protected;
        $this->private = $private;
    }
}
