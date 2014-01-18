<?php

namespace W3C\Formatter;

use ReflectionMethod;
use SimpleXMLElement;
use W3C\Validation\Result;
use W3C\Validation\Violation;

/**
 * TestCase for the Checkstyle formatter.
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 * @copyright Copyright (c) 2014 Michel Hunziker <info@michelhunziker.com>
 * @license http://www.opensource.org/licenses/BSD-3-Clause The BSD-3-Clause License
 */
class CheckstyleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Checkstyle
     */
    protected $formatter;


    protected function setUp()
    {
        $this->formatter = new Checkstyle();
    }

    /**
     * @covers W3C\Formatter\Checkstyle::format
     */
    public function testFormatValid()
    {
        $this->assertXmlStringEqualsXmlString(
            "<checkstyle><file/></checkstyle>",
            $this->formatter->format(new Result())
        );
    }

    /**
     * @covers W3C\Formatter\Checkstyle::format
     */
    public function testFormatValidWithUrl()
    {
        $this->assertXmlStringEqualsXmlString(
            '<checkstyle><file name="http://me.com"/></checkstyle>',
            $this->formatter->format(new Result(), 'http://me.com')
        );
    }

    /**
     * @covers W3C\Formatter\Checkstyle::format
     */
    public function testFormatAddsErrorViolations()
    {
        $formatter = $this->getMock('W3C\Formatter\Checkstyle', array('addViolation'));
        $result = new Result();
        $error1 = new Violation();
        $error2 = new Violation();

        $result->addError($error1)->addError($error2);

        $formatter->expects($this->at(0))
            ->method('addViolation')
            ->with($this->anything(), $this->identicalTo($error1), 'error');
        $formatter->expects($this->at(1))
            ->method('addViolation')
            ->with($this->anything(), $this->identicalTo($error2), 'error');

        $formatter->format($result);
    }

    /**
     * @covers W3C\Formatter\Checkstyle::format
     */
    public function testFormatAddsWarningViolations()
    {
        $formatter = $this->getMock('W3C\Formatter\Checkstyle', array('addViolation'));
        $result = new Result();
        $warning1 = new Violation();
        $warning2 = new Violation();

        $result->addWarning($warning1)->addWarning($warning2);

        $formatter->expects($this->at(0))
            ->method('addViolation')
            ->with($this->anything(), $this->identicalTo($warning1), 'warning');
        $formatter->expects($this->at(1))
            ->method('addViolation')
            ->with($this->anything(), $this->identicalTo($warning2), 'warning');

        $formatter->format($result);
    }

    /**
     * @covers W3C\Formatter\Checkstyle::addViolation
     */
    public function testAddsViolation()
    {
        $xml = new SimpleXMLElement('<root />');
        $violation = new Violation();
        $violation->setLine(5)
            ->setColumn(9)
            ->setMessage('My message')
            ->setSource('My Source')
            ->setExplanation('My Explanation');

        $method = new ReflectionMethod('W3C\Formatter\Checkstyle', 'addViolation');
        $method->setAccessible(true);

        $method->invoke($this->formatter, $xml, $violation, 'my-severity');
        $this->assertXmlStringEqualsXmlString(
            '<root><error severity="my-severity" line="5" column="9" message="My message" source="HTMLValidation"/></root>',
            $xml->asXML()
        );
    }
}
