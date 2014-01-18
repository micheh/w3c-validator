<?php

namespace W3C\Formatter;

use SimpleXMLElement;
use W3C\Validation\Result;
use W3C\Validation\Violation;

/**
 * Formatter, which formats a validation result in the checkstyle xml format.
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 * @copyright Copyright (c) 2014 Michel Hunziker <info@michelhunziker.com>
 * @license http://www.opensource.org/licenses/BSD-3-Clause The BSD-3-Clause License
 */
class Checkstyle
{
    /**
     * Formats a result object as a checkstyle report xml and returns the xml content.
     *
     * @param Result $result The result object for which the xml report should be created
     * @param string|null $url (optional) URL of the page, will be added to the <file> tag
     * @return string XML in checkstyle format
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
     * Add a violation to the file.
     *
     * @param SimpleXMLElement $file The file where the error should be added
     * @param Violation $violation The violation to add
     * @param string $severity Should be either error or warning
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
