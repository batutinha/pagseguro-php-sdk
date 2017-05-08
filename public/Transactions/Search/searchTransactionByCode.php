<?php

use PagSeguro\Library;
use PagSeguro\Services\Transactions;
use PagSeguro\Configuration\Configure;

require_once "../../../vendor/autoload.php";

Library::initialize();
Configure::fromXmlFile('./path/to/file/conf.xml');


$code = '6AE2DCA63476443ABFC3EDD703243CFB';

try {
    /** @var $response \PagSeguro\Parsers\Transaction\Response */
    $response = Transactions\Search\Code::search(
        Configure::getAccountCredentials(),
        $code
    );

    var_dump($response);
} catch (Exception $e) {
    die($e->getMessage());
}
