<?php
namespace MonkeyLearn;

use MonkeyLearn\Config;
use MonkeyLearn\Classification;
use MonkeyLearn\Extraction;

class Client {

    function __construct($token, $base_endpoint=Config::DEFAULT_BASE_ENDPOINT) {
        $this->token = $token;
        $this->classifiers = new Classification($token, $base_endpoint);
        $this->extractors = new Extraction($token, $base_endpoint);
    }

}
?>
