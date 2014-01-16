<?php

namespace W3C;

use SimpleXMLElement;
use W3C\Validation\Result;
use W3C\Validation\Violation;

/**
 * @todo add phpDoc
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 */
class HtmlValidator
{
    /**
     * @var string
     */
    protected $url = 'http://validator.w3.org/check';


    /**
     * @param string $html
     * @return Result
     */
    public function validateInput($html)
    {
        $data = array('fragment' => $html);
        return $this->validate($data);
    }

    /**
     * @param array $data
     * @return Result
     */
    protected function validate(array $data)
    {
        $data['output'] = 'soap12';

        $resource = curl_init($this->url);
        curl_setopt($resource, CURLOPT_USERAGENT, 'curl');
        curl_setopt($resource, CURLOPT_POST, true);
        curl_setopt($resource, CURLOPT_POSTFIELDS, $data);
        curl_setopt($resource, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($resource, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($resource);
        return $this->parseResponse($response);
    }

    /**
     * @param string $response
     * @return Result
     */
    protected function parseResponse($response)
    {
        $xml = new SimpleXMLElement($response);
        $ns = $xml->getNamespaces(true);
        $data = $xml->children($ns['env'])->children($ns['m'])->markupvalidationresponse;

        $result = new Result();
        $result->setIsValid($data->validity == 'true');

        foreach ($data->errors->errorlist->error as $error) {
            $entry = $this->getEntry($error);
            $result->addError($entry);
        }

        foreach ($data->warnings->warninglist->warning as $warning) {
            if (strpos($warning->messageid, 'W') === false) {
                $entry = $this->getEntry($warning);
                $result->addWarning($entry);
            }
        }

        return $result;
    }

    /**
     * @param SimpleXMLElement $xml
     * @return Violation
     */
    protected function getEntry(SimpleXMLElement $xml)
    {
        $entry = new Violation();
        $entry->setLine($xml->line)
            ->setColumn($xml->col)
            ->setMessage($xml->message)
            ->setExplanation($xml->explanation)
            ->setSource($xml->source);

        return $entry;
    }
}
