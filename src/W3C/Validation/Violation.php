<?php

namespace W3C\Validation;


/**
 * Class, which represents a violation (error or warning).
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 */
class Violation
{
    /**
     * @var int
     */
    protected $line;

    /**
     * @var int
     */
    protected $column;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $explanation;

    /**
     * @var string
     */
    protected $source;


    /**
     * @param int $col
     * @return Violation
     */
    public function setColumn($col)
    {
        $this->column = (int) $col;
        return $this;
    }

    /**
     * @return int
     */
    public function getColumn()
    {
        return $this->column;
    }

    /**
     * @param string $explanation
     * @return Violation
     */
    public function setExplanation($explanation)
    {
        $this->explanation = (string) $explanation;
        return $this;
    }

    /**
     * @return string
     */
    public function getExplanation()
    {
        return $this->explanation;
    }

    /**
     * @param int $line
     * @return Violation
     */
    public function setLine($line)
    {
        $this->line = (int) $line;
        return $this;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param string $message
     * @return Violation
     */
    public function setMessage($message)
    {
        $this->message = (string) $message;
        return $this;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $source
     * @return Violation
     */
    public function setSource($source)
    {
        $this->source = (string) $source;
        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }
}
