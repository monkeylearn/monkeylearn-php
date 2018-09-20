<?php
namespace MonkeyLearn;

use MonkeyLearn\Config;
use MonkeyLearn\MonkeyLearnResponse;
use MonkeyLearn\HandleErrors;
use MonkeyLearn\MonkeyLearnException;

class Extraction extends SleepRequests {
    function __construct($token, $base_endpoint) {
        $this->token = $token;
        $this->endpoint = $base_endpoint.'extractors/';
    }

    function detail($model_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$model_id.'/';
        try {
            list($response, $header) = $this->make_request($url, 'GET', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }

    function list($page=null, $per_page=null, $order_by=null, $sleep_if_throttled=true) {
        $query_params = http_build_query(
            array('page' => $page, 'per_page' => $per_page, 'order_by' => $order_by)
        );
        $url = $this->endpoint.'?'.$query_params;
        try {
            list($response, $header) = $this->make_request($url, 'GET', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }

    function extract($model_id, $data, $production_model=false, $extra_args=null,
                      $batch_size=Config::DEFAULT_BATCH_SIZE, $sleep_if_throttled=true) {
        HandleErrors::check_batch_limits($data, $batch_size);
        $url = $this->endpoint.$model_id.'/extract/';

        if (!$extra_args) {
            $extra_args = array();
        }

        $res = array();
        $headers = array();
        $batches = array_chunk($data, $batch_size);
        foreach($batches as $batch) {
            $data_dict = array('data' => $batch);
            $data_dict['production_model'] = $production_model;
            foreach($extra_args as $key => $val) {
                $data_dict[$key] = $val;
            }
            try {
                list($response, $header) = $this->make_request($url, 'POST', $data_dict, $sleep_if_throttled);
            } catch (\MonkeyLearnException $mle){
                throw $mle;
            }
            $headers[] = $header;
            $res = array_merge($res, $response);
        }

        return new MonkeyLearnResponse($res, $headers);
    }
}

?>
