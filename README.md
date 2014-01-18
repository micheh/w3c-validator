W3C HTML Validator
==================

This is a PHP library to validate HTML using the W3C HTML Validator API (non-official). It includes
a checkstyle.xml formatter, which can be used in continuous integration systems.


Installation
------------
Install with [composer](https://getcomposer.org/):

```sh
php composer.phar require-dev micheh/w3c-validator:0.*
```


Usage
-----
Run the `w3c-validator.php` file in the `bin` directory and provide a url or path to the HTML file,
which should be validated with the W3C Validator. If a url is provided, it will grab the html locally
and submit it to the validator, which enables you to validate a local page as well. The script will
exit with error code 0 if no errors or warnings are found, and exit code 1 if there is at least one
error and/or warning.

To create a checkstyle.xml file with the violations, provide the `--report-checkstyle=<path>` flag
and set the path where the xml file should be saved.

```sh
php bin/w3c-validator.php --report-checkstyle=artifacts/checkstyle.xml http://localhost/project1
```


License
-------
The files in this archive are licensed under the BSD-3-Clause license.
You can find a copy of this license in [LICENSE.txt](LICENSE.txt).
