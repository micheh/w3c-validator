<?php

namespace W3C\Validation;

/**
 * TestCase for the Violation class.
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 * @copyright Copyright (c) 2014 Michel Hunziker <info@michelhunziker.com>
 * @license http://www.opensource.org/licenses/BSD-3-Clause The BSD-3-Clause License
 */
class ViolationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Violation
     */
    protected $violation;


    protected function setUp()
    {
        $this->violation = new Violation();
    }

    /**
     * @covers W3C\Validation\Violation::setColumn
     * @covers W3C\Validation\Violation::getColumn
     */
    public function testSetColumn()
    {
        $this->violation->setColumn('35');
        $this->assertSame(35, $this->violation->getColumn());
    }

    /**
     * @covers W3C\Validation\Violation::setExplanation
     * @covers W3C\Validation\Violation::getExplanation
     */
    public function testSetExplanation()
    {
        $this->violation->setExplanation('Additional info');
        $this->assertSame('Additional info', $this->violation->getExplanation());
    }

    /**
     * @covers W3C\Validation\Violation::setLine
     * @covers W3C\Validation\Violation::getLine
     */
    public function testSetLine()
    {
        $this->violation->setLine('12');
        $this->assertSame(12, $this->violation->getLine());
    }

    /**
     * @covers W3C\Validation\Violation::setMessage
     * @covers W3C\Validation\Violation::getMessage
     */
    public function testSetMessage()
    {
        $this->violation->setMessage('Violation Message');
        $this->assertSame('Violation Message', $this->violation->getMessage());
    }

    /**
     * @covers W3C\Validation\Violation::setSource
     * @covers W3C\Validation\Violation::getSource
     */
    public function testSetSource()
    {
        $this->violation->setSource('My Source');
        $this->assertSame('My Source', $this->violation->getSource());
    }
}
