<?php
declare (strict_types=1);

namespace Tardigrades\SectionField\ValueObject;

use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass Tardigrades\SectionField\ValueObject\SectionNamespace
 * @covers ::<private>
 * @covers ::__construct
 */
class SectionNamespaceTest extends TestCase
{
    /**
     * @test
     * @covers ::fromString
     */
    public function it_creates_from_string()
    {
        $this->assertInstanceOf(SectionNamespace::class, SectionNamespace::fromString('sexy string'));
    }
    /**
     * @test
     * @covers ::__toString
     */
    public function it_should_be_treatable_as_a_string()
    {
        $string = 'sexy string!';
        $thing = (string)SectionNamespace::fromString($string);

        $this->assertSame($string, $thing);
    }
}
