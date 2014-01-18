<?php

/**
 * Script to validate the HTML using the W3C validation API. Provide the url
 * or path as an argument. Uses exit code 0 if successful and 1 if an error
 * happend. Optionally add a --report-checkstyle=<save path of xml file> flag
 * to create a checkstyle report at the specified location.
 *
 * @author Michel Hunziker <info@michelhunziker.com>
 * @copyright Copyright (c) 2014 Michel Hunziker <info@michelhunziker.com>
 * @license http://www.opensource.org/licenses/BSD-3-Clause The BSD-3-Clause License
 */

$arguments = array_slice($argv, 1);
$url = '';
$reportPath = null;

foreach ($arguments as $argument) {
    if (strpos($argument, '--report-checkstyle=') === 0) {
        $reportPath = substr($argument, 20);
    } elseif(strpos($argument, '-') !== 0) {
        $url = $argument;
    }
}

if (!$url) {
    echo 'ERROR: No URL specified';
    exit(1);
}


require __DIR__ . '/../vendor/autoload.php';

$validator = new \W3C\HtmlValidator();
$result = $validator->validateInput(file_get_contents($url));

if ($reportPath) {
    $formatter = new \W3C\Formatter\Checkstyle();
    $xml = $formatter->format($result, $url);
    file_put_contents($reportPath, $xml);
}

if ($result->isValid()) {
    echo 'Validation successful';
    exit(0);
} else {
    printf(
        'Validation failed: %d error(s) and %d warning(s)',
        $result->getErrorCount(),
        $result->getWarningCount()
    );
    exit(1);
}
