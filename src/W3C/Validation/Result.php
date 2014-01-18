<?php

namespace W3C\Validation;


/**
 * Class, which represents a validation result.
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 * @copyright Copyright (c) 2014 Michel Hunziker <info@michelhunziker.com>
 * @license http://www.opensource.org/licenses/BSD-3-Clause The BSD-3-Clause License
 */
class Result
{
    /**
     * @var bool
     */
    protected $isValid;

    /**
     * @var Violation[]
     */
    protected $errors = array();

    /**
     * @var int
     */
    protected $errorCount = 0;

    /**
     * @var Violation[]
     */
    protected $warnings = array();

    /**
     * @var int
     */
    protected $warningCount = 0;


    /**
     * @param Violation $error
     * @return Result
     */
    public function addError(Violation $error)
    {
        $this->errors[] = $error;
        $this->errorCount++;

        return $this;
    }

    /**
     * @return Violation[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @return int
     */
    public function getErrorCount()
    {
        return $this->errorCount;
    }

    /**
     * @param bool $isValid
     * @return Result
     */
    public function setIsValid($isValid)
    {
        $this->isValid = (bool) $isValid;
        return $this;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * @param Violation $warning
     * @return Result
     */
    public function addWarning(Violation $warning)
    {
        $this->warnings[] = $warning;
        $this->warningCount++;

        return $this;
    }

    /**
     * @return Violation[]
     */
    public function getWarnings()
    {
        return $this->warnings;
    }

    /**
     * @return int
     */
    public function getWarningCount()
    {
        return $this->warningCount;
    }
}
