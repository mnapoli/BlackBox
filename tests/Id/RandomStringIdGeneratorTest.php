<?php

namespace Tests\BlackBox\Id;

use BlackBox\Id\RandomStringIdGenerator;

/**
 * @covers \BlackBox\Id\RandomStringIdGenerator
 */
class RandomStringIdGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     */
    public function it_should_return_a_string()
    {
        $generator = new RandomStringIdGenerator();

        $this->assertInternalType('string', $generator->getId());
    }

    /**
     * @test
     */
    public function it_should_return_unique_ids()
    {
        $generator = new RandomStringIdGenerator();

        $this->assertNotEquals($generator->getId(), $generator->getId());
    }
}
