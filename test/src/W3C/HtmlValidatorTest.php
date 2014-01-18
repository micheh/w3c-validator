<?php

namespace W3C;

use ReflectionMethod;
use SimpleXMLElement;
use W3C\Validation\Result;
use W3C\Validation\Violation;

/**
 * TestCase for the HtmlValidator.
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 * @copyright Copyright (c) 2014 Michel Hunziker <info@michelhunziker.com>
 * @license http://www.opensource.org/licenses/BSD-3-Clause The BSD-3-Clause License
 */
class HtmlValidatorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var HtmlValidator
     */
    protected $validator;


    protected function setUp()
    {
        $this->validator = new HtmlValidator();
    }

    /**
     * @covers W3C\HtmlValidator::validateInput
     */
    public function testValidateInput()
    {
        $validator = $this->getMock('W3C\HtmlValidator', array('validate'));
        $validator->expects($this->once())
            ->method('validate')
            ->with(array('fragment' => 'My HTML'))
            ->will($this->returnValue('Validated'));

        $this->assertEquals('Validated', $validator->validateInput('My HTML'));
    }

    /**
     * @covers W3C\HtmlValidator::validate
     */
    public function testValidate()
    {
        $method = new ReflectionMethod('W3C\HtmlValidator', 'validate');
        $method->setAccessible(true);

        $html = file_get_contents(__DIR__ . '/../../files/simple-valid.html');
        $response = file_get_contents(__DIR__ . '/../../files/simple-valid.xml');

        $validator = $this->getMock('W3C\HtmlValidator', array('parseResponse'));
        $validator->expects($this->once())
            ->method('parseResponse')
            ->with($response)
            ->will($this->returnValue('Parsed'));

        $this->assertEquals(
            'Parsed',
            $method->invoke($validator, array('fragment' => $html))
        );
    }

    /**
     * @covers W3C\HtmlValidator::parseResponse
     */
    public function testParseResponseWithValidHtml()
    {
        $result = $this->parseFile('simple-valid.xml');
        $this->assertTrue($result->isValid());
    }

    /**
     * @covers W3C\HtmlValidator::parseResponse
     */
    public function testParseResponseWithValidHtmlHasNoErrors()
    {
        $result = $this->parseFile('simple-valid.xml');
        $this->assertAttributeEmpty('errors', $result);
    }

    /**
     * @covers W3C\HtmlValidator::parseResponse
     */
    public function testParseResponseWithValidHtmlHasNoWarnings()
    {
        $result = $this->parseFile('simple-valid.xml');
        $this->assertAttributeEmpty('warnings', $result);
    }

    /**
     * @covers W3C\HtmlValidator::parseResponse
     */
    public function testParseResponseWithInvalidHtml()
    {
        $result = $this->parseFile('simple.xml');
        $this->assertFalse($result->isValid());
    }

    /**
     * @covers W3C\HtmlValidator::parseResponse
     */
    public function testParseResponseWithInvalidHtmlSetsErrors()
    {
        $result = $this->parseFile('simple.xml');
        $errors = $result->getErrors();

        $this->assertCount(3, $errors);
        $this->assertContainsOnlyInstancesOf('W3C\Validation\Violation', $errors);
    }

    /**
     * @covers W3C\HtmlValidator::parseResponse
     */
    public function testParseResponseWithInvalidHtmlSetsWarnings()
    {
        $result = $this->parseFile('warning.xml');
        $warnings = $result->getWarnings();

        $this->assertCount(1, $warnings);
        $this->assertContainsOnlyInstancesOf('W3C\Validation\Violation', $warnings);
    }

    /**
     * @covers W3C\HtmlValidator::parseResponse
     * @depends testParseResponseWithInvalidHtmlSetsErrors
     */
    public function testParseResponseWithInvalidHtmlSetsViolations()
    {
        $result = $this->parseFile('simple.xml');
        $errors = $result->getErrors();

        $explanation = $errors[1]->getExplanation();
        $expectedViolation = new Violation();
        $expectedViolation->setLine(3)
            ->setColumn(6)
            ->setMessage('document type does not allow element "body" here')
            ->setExplanation($explanation)
            ->setSource('&#60;body<strong title="Position where error was detected.">&#62;</strong>');

        $this->assertEquals($expectedViolation, $errors[1]);
    }

    /**
     * @covers W3C\HtmlValidator::getEntry
     */
    public function testGetEntry()
    {
        $method = new ReflectionMethod('W3C\HtmlValidator', 'getEntry');
        $method->setAccessible(true);

        $xmlString = <<<'XML'
<warning>
    <line>3</line>
    <col>9</col>
    <message>My Message</message>
    <messageid>my-id</messageid>
    <explanation>Some info</explanation>
    <source>Unknown</source>
</warning>
XML;
        $xml = new SimpleXMLElement($xmlString);
        $violation = new Violation();
        $violation->setLine(3)
            ->setColumn(9)
            ->setMessage('My Message')
            ->setExplanation('Some info')
            ->setSource('Unknown');

        $this->assertEquals(
            $violation,
            $method->invoke($this->validator, $xml)
        );
    }

    /**
     * Helper method to test the parseResponse method.
     *
     * @param string $filename Filename in the files directory
     * @return Result Parsed result
     */
    protected function parseFile($filename)
    {
        $method = new ReflectionMethod('W3C\HtmlValidator', 'parseResponse');
        $method->setAccessible(true);

        return $method->invoke(
            $this->validator,
            file_get_contents(__DIR__ . '/../../files/' . $filename)
        );
    }
}
