<?php

namespace W3C\Formatter;

use SimpleXMLElement;
use W3C\Validation\Result;
use W3C\Validation\Violation;

/**
 * Formatter, which formats a validation result in the checkstyle xml format.
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 */
class Checkstyle
{
    /**
     * @param Result $result
     * @param string|null $url
     * @return string
     */
    public function format(Result $result, $url = null)
    {
        $xml = new SimpleXMLElement('<checkstyle />');
        $file = $xml->addChild('file');
        if ($url) {
            $file->addAttribute('name', $url);
        }

        $errors = $result->getErrors();
        $warnings = $result->getWarnings();

        foreach ($errors as $error) {
            $this->addViolation($file, $error, 'error');
        }

        foreach ($warnings as $warning) {
            $this->addViolation($file, $warning, 'warning');
        }

        return $xml->asXML();
    }

    /**
     * @param SimpleXMLElement $file
     * @param Violation $violation
     * @param string $severity
     */
    protected function addViolation(SimpleXMLElement $file, Violation $violation, $severity)
    {
        $xmlViolation = $file->addChild('error');
        $xmlViolation->addAttribute('severity', $severity);
        $xmlViolation->addAttribute('line', $violation->getLine());
        $xmlViolation->addAttribute('column', $violation->getColumn());
        $xmlViolation->addAttribute('message', $violation->getMessage());
        $xmlViolation->addAttribute('source', 'HTMLValidation');
    }
}
