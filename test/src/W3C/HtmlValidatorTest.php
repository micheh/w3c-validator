<?php

namespace W3C;

use ReflectionMethod;
use W3C\Validation\Result;
use W3C\Validation\Violation;

/**
 * TestCase for the HTMLValidator.
 *
 * @author Michel Hunziker <info@michelhunziker.com>
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
     * @covers W3C\HTMLValidator::parseResponse
     */
    public function testParseResponseWithValidHtml()
    {
        $result = $this->parseFile('simple-valid.xml');
        $this->assertTrue($result->isValid());
    }

    /**
     * @covers W3C\HTMLValidator::parseResponse
     */
    public function testParseResponseWithValidHtmlHasNoErrors()
    {
        $result = $this->parseFile('simple-valid.xml');
        $this->assertAttributeEmpty('errors', $result);
    }

    /**
     * @covers W3C\HTMLValidator::parseResponse
     */
    public function testParseResponseWithValidHtmlHasNoWarnings()
    {
        $result = $this->parseFile('simple-valid.xml');
        $this->assertAttributeEmpty('warnings', $result);
    }

    /**
     * @covers W3C\HTMLValidator::parseResponse
     */
    public function testParseResponseWithInvalidHtml()
    {
        $result = $this->parseFile('simple.xml');
        $this->assertFalse($result->isValid());
    }

    /**
     * @covers W3C\HTMLValidator::parseResponse
     */
    public function testParseResponseWithInvalidHtmlSetsErrors()
    {
        $result = $this->parseFile('simple.xml');
        $errors = $result->getErrors();

        $this->assertCount(3, $errors);
        $this->assertContainsOnlyInstancesOf('W3C\Validation\Violation', $errors);
    }

    /**
     * @covers W3C\HTMLValidator::parseResponse
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
