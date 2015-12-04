<?php
namespace MonkeyLearn;

use MonkeyLearn\Config;
use MonkeyLearn\MonkeyLearnResponse;
use MonkeyLearn\HandleErrors;

class Extraction extends SleepRequests {
    function __construct($token, $base_endpoint) {
        $this->token = $token;
        $this->endpoint = $base_endpoint.'extractors/';
    }

    function extract($module_id, $text_list,
                      $batch_size=Config::DEFAULT_BATCH_SIZE, $sleep_if_throttled=true) {
        HandleErrors::check_batch_limits($text_list, $batch_size);
        $url = $this->endpoint.$module_id.'/extract/';

        $res = array();
        $headers = array();
        $batches = array_chunk($text_list, $batch_size);
        foreach($batches as $batch) {
            $data = array('text_list' => $batch);
            list($response, $header) = $this->make_request($url, 'POST', $data, $sleep_if_throttled);
            $headers[] = $header;
            $res = array_merge($res, $response['result']);
        }

        return new MonkeyLearnResponse($res, $headers);
    }
}

?>
