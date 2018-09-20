<?php
namespace MonkeyLearn;

use MonkeyLearn\Config;
use MonkeyLearn\MonkeyLearnResponse;
use MonkeyLearn\HandleErrors;
use MonkeyLearn\MonkeyLearnException;

class Classification extends SleepRequests {
    function __construct($token, $base_endpoint) {
        $this->token = $token;
        $this->endpoint = $base_endpoint.'classifiers/';
        $this->tags = new Tags($token, $base_endpoint);
    }

    function classify($model_id, $data, $production_model=false,
                      $batch_size=Config::DEFAULT_BATCH_SIZE, $sleep_if_throttled=true) {
        HandleErrors::check_batch_limits($data, $batch_size);
        $url = $this->endpoint.$model_id.'/classify/';

        $res = array();
        $headers = array();
        $batches = array_chunk($data, $batch_size);
        foreach($batches as $batch) {
            $data_dict = array('data' => $batch);
            $data_dict['production_model'] = $production_model;
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

    function detail($model_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$model_id.'/';
        try {
            list($response, $header) = $this->make_request($url, 'GET', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }

    function upload_data($model_id, $data, $sleep_if_throttled=true) {
        $url = $this->endpoint.$model_id.'/data/';
        $data = array('data' => $data);
        try {
            list($response, $header) = $this->make_request($url, 'POST', $data, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }

    function train($model_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$model_id.'/train/';
        try {
            list($response, $header) = $this->make_request($url, 'POST', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }

    function deploy($model_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$model_id.'/deploy/';
        try {
            list($response, $header) = $this->make_request($url, 'POST', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }

    function delete($model_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$model_id.'/';
        try {
            list($response, $header) = $this->make_request($url, 'DELETE', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }

    function create($name, $description=null, $algorithm=null, $language=null, $max_features=null,
               $ngram_range=null, $use_stemming=null, $preprocess_numbers=null,
               $preprocess_social_media=null, $normalize_weights=null, $stopwords=null,
               $whitelist=null, $sleep_if_throttled=true) {

        $data = array(
            'name' => $name,
            'description' => $description,
            'algorithm' => $algorithm,
            'language' => $language,
            'max_features' => $max_features,
            'ngram_range' => $ngram_range,
            'use_stemming' => $use_stemming,
            'preprocess_numbers' => $preprocess_numbers,
            'preprocess_social_media' => $preprocess_social_media,
            'normalize_weights' => $normalize_weights,
            'stopwords' => $stopwords,
            'whitelist' => $whitelist,
        );

        // remove null values
        foreach($data as $key => $val) {
            if (!$val) {
                unset($data[$key]);
            }
        }

        $url = $this->endpoint;
        try {
            list($response, $header) = $this->make_request($url, 'POST', $data, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }

    function edit($model_id, $name=null, $description=null, $algorithm=null, $language=null,
               $max_features=null, $ngram_range=null, $use_stemming=null, $preprocess_numbers=null,
               $preprocess_social_media=null, $normalize_weights=null, $stopwords=null,
               $whitelist=null, $sleep_if_throttled=true) {

        $data = array(
            'name' => $name,
            'description' => $description,
            'algorithm' => $algorithm,
            'language' => $language,
            'max_features' => $max_features,
            'ngram_range' => $ngram_range,
            'use_stemming' => $use_stemming,
            'preprocess_numbers' => $preprocess_numbers,
            'preprocess_social_media' => $preprocess_social_media,
            'normalize_weights' => $normalize_weights,
            'stopwords' => $stopwords,
            'whitelist' => $whitelist,
        );

        // remove null values
        foreach($data as $key => $val) {
            if (!$val) {
                unset($data[$key]);
            }
        }

        $url = $this->endpoint.$model_id.'/';
        try {
            list($response, $header) = $this->make_request($url, 'PATCH', $data, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }
}

class Tags extends SleepRequests {
    function __construct($token, $base_endpoint) {
        $this->token = $token;
        $this->endpoint = $base_endpoint.'classifiers/';
    }

    function detail($model_id, $tag_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$model_id.'/tags/'.$tag_id.'/';
        try {
            list($response, $header) = $this->make_request($url, 'GET', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }

    function create($model_id, $name, $parent_id=null, $sleep_if_throttled=true) {

        $data = array(
            'name' => $name,
            'parent_id' => $parent_id,
        );

        // remove null values
        foreach($data as $key => $val) {
            if (!$val) {
                unset($data[$key]);
            }
        }

        $url = $this->endpoint.$model_id.'/tags/';
        try {
            list($response, $header) = $this->make_request($url, 'POST', $data, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }

    function edit($model_id, $tag_id, $name=null, $parent_id=null, $sleep_if_throttled=true) {

        $data = array(
            'name' => $name,
            'parent_id' => $parent_id,
        );

        // remove null values
        foreach($data as $key => $val) {
            if (!$val) {
                unset($data[$key]);
            }
        }

        $url = $this->endpoint.$model_id.'/tags/'.$tag_id.'/';
        try {
            list($response, $header) = $this->make_request($url, 'PATCH', $data, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }

    function delete($model_id, $tag_id, $move_data_to=null, $sleep_if_throttled=true) {

        $data = array(
            'move_data_to' => $move_data_to
        );

        foreach($data as $key => $val) {
            if (!$val) {
                unset($data[$key]);
            }
        }

        $url = $this->endpoint.$model_id.'/tags/'.$tag_id.'/';
        try {
            list($response, $header) = $this->make_request($url, 'DELETE', $data, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){
            throw $mle;
        }
        return new MonkeyLearnResponse($response, array($header));
    }
}
?>
