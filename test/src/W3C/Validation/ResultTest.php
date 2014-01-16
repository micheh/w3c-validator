<?php

namespace W3C\Validation;

/**
 * TestCase for the Result class.
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 */
class ResultTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Result
     */
    protected $result;


    protected function setUp()
    {
        $this->result = new Result();
    }

    /**
     * @covers W3C\Validation\Result::setIsValid
     * @covers W3C\Validation\Result::isValid
     */
    public function testSetIsValid()
    {
        $this->result->setIsValid(false);
        $this->assertFalse($this->result->isValid());

        $this->result->setIsValid(true);
        $this->assertTrue($this->result->isValid());
    }

    /**
     * @covers W3C\Validation\Result::addError
     * @covers W3C\Validation\Result::getErrors
     */
    public function testAddErrors()
    {
        $violation1 = new Violation();
        $violation2 = new Violation();

        $this->result->addError($violation1);
        $this->result->addError($violation2);

        $this->assertSame(
            array($violation1, $violation2),
            $this->result->getErrors()
        );
    }

    /**
     * @depends testAddErrors
     * @covers W3C\Validation\Result::getErrorCount
     */
    public function testAddErrorsSetsErrorCount()
    {
        $this->assertEquals(0, $this->result->getErrorCount());
        $this->result->addError(new Violation());
        $this->assertEquals(1, $this->result->getErrorCount());
        $this->result->addError(new Violation());
        $this->assertEquals(2, $this->result->getErrorCount());
    }

    /**
     * @covers W3C\Validation\Result::addWarning
     * @covers W3C\Validation\Result::getWarnings
     */
    public function testAddWarnings()
    {
        $violation1 = new Violation();
        $violation2 = new Violation();

        $this->result->addWarning($violation1);
        $this->result->addWarning($violation2);

        $this->assertSame(
            array($violation1, $violation2),
            $this->result->getWarnings()
        );
    }

    /**
     * @depends testAddWarnings
     * @covers W3C\Validation\Result::getWarningCount
     */
    public function testAddErrorsSetsWarningCount()
    {
        $this->assertEquals(0, $this->result->getWarningCount());
        $this->result->addWarning(new Violation());
        $this->assertEquals(1, $this->result->getWarningCount());
        $this->result->addWarning(new Violation());
        $this->assertEquals(2, $this->result->getWarningCount());
    }
}
