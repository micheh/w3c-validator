<?php

namespace W3C;

use SimpleXMLElement;
use W3C\Validation\Result;
use W3C\Validation\Violation;

/**
 * Class which validates HTML with the W3C Validator API.
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 * @copyright Copyright (c) 2014 Michel Hunziker <info@michelhunziker.com>
 * @license http://www.opensource.org/licenses/BSD-3-Clause The BSD-3-Clause License
 */
class HtmlValidator
{
    /**
     * @var string
     */
    protected $url = 'http://validator.w3.org/check';
    protected $urlAtom = 'http://validator.w3.org/feed/check.cgi';


    /**
     * Validates the provided HTML string and returns a result.
     *
     * @param string $html HTML string to validate
     * @return Result
     */
    public function validateInput($html, $type = null)
    {
        $data = array('fragment' => $html);
        return ($type == "atom") ? $this->validateFeed($data) : $this->validate($data);
    }

    /**
     * External call to the W3C Validation API, using curl.
     *
     * @param array $data The data to post to the API.
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
     * External call to the W3C Validation API, using curl.
     *
     * @param array $data The data to post to the API.
     * @return Result
     */
    protected function validateFeed(array $data)
    {
        $get = [
            'output' => 'soap12',
            'rawdata' => $data['fragment']
        ];
        $options = [];
        $defaults = array(
            CURLOPT_URL => $this->urlAtom. (strpos($this->urlAtom, '?') === FALSE ? '?' : ''). http_build_query($get),
            CURLOPT_HEADER => 0,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_TIMEOUT => 4
        );
        error_log($defaults[CURLOPT_URL]);
        $ch = curl_init();
        curl_setopt_array($ch, ($options + $defaults));
        if( ! $result = curl_exec($ch))
        {
            trigger_error(curl_error($ch));
        }
        curl_close($ch);
        
        return $this->parseResponse($result, 'feedvalidationresponse');
    }

    /**
     * Parses the SOAP response of the API and returns a new Result object.
     *
     * @param string $response SOAP response of the API
     * @return Result
     */
    protected function parseResponse($response, $parentNode = 'markupvalidationresponse')
    {
        $xml = new SimpleXMLElement($response);
        $ns = $xml->getNamespaces(true);
        $data = $xml->children($ns['env'])->children($ns['m'])->$parentNode;

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
     * Create a violation object from the provided xml.
     *
     * @param SimpleXMLElement $xml XML element which contains the violation details
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
