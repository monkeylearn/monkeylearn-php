<?php
namespace MonkeyLearn;

use MonkeyLearn\Config;
use MonkeyLearn\MonkeyLearnResponse;
use MonkeyLearn\HandleErrors;

class Classification extends SleepRequests {
    function __construct($token, $base_endpoint) {
        $this->token = $token;
        $this->endpoint = $base_endpoint.'classifiers/';
        $this->categories = new Categories($token, $base_endpoint);
    }

    function classify($module_id, $text_list, $sandbox=false,
                      $batch_size=Config::DEFAULT_BATCH_SIZE, $sleep_if_throttled=true) {
        HandleErrors::check_batch_limits($text_list, $batch_size);
        $url = $this->endpoint.$module_id.'/classify/';
        if ($sandbox) {
            $url .= '?sandbox=1';
        }

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

    function list($sleep_if_throttled=true) {
        $url = $this->endpoint;
        list($response, $header) = $this->make_request($url, 'GET', null, $sleep_if_throttled);
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function detail($module_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$module_id;
        list($response, $header) = $this->make_request($url, 'GET', null, $sleep_if_throttled);
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function upload_samples($module_id, $samples_with_categories, $sleep_if_throttled=true) {
        $url = $this->endpoint.$module_id.'/samples/';
        $data_samples = array();
        foreach($samples_with_categories as $sc) {
            $data_samples[] = array('text' => $sc[0], 'category_id' => $sc[1]);
        }
        $data = array('samples' => $data_samples);
        list($response, $header) = $this->make_request($url, 'POST', $data, $sleep_if_throttled);
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function train($module_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$module_id.'/train/';
        list($response, $header) = $this->make_request($url, 'POST', null, $sleep_if_throttled);
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function deploy($module_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$module_id.'/deploy/';
        list($response, $header) = $this->make_request($url, 'POST', null, $sleep_if_throttled);
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function delete($module_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$module_id;
        list($response, $header) = $this->make_request($url, 'DELETE', null, $sleep_if_throttled);
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function create($name, $description=null, $train_state=null, $language=null, $ngram_range=null,
               $use_stemmer=null, $stop_words=null, $max_features=null, $strip_stopwords=null,
               $is_multilabel=null, $is_twitter_data=null, $normalize_weights=null,
               $classifier=null, $industry=null, $classifier_type=null,
               $text_type=null, $permissions=null, $sleep_if_throttled=true) {

        $data = array(
            'name' => $name,
            'description' => $description,
            'train_state' => $train_state,
            'language' => $language,
            'ngram_range' => $ngram_range,
            'use_stemmer' => $use_stemmer,
            'stop_words' => $stop_words,
            'max_features' => $max_features,
            'strip_stopwords' => $strip_stopwords,
            'is_multilabel' => $is_multilabel,
            'is_twitter_data' => $is_twitter_data,
            'normalize_weights' => $normalize_weights,
            'classifier' => $classifier,
            'industry' => $industry,
            'classifier_type' => $classifier_type,
            'text_type' => $text_type,
            'permissions' => $permissions,
        );

        foreach($data as $key => $val) {
            if (!$val) {
                unset($data[$key]);
            }
        }

        $url = $this->endpoint;
        list($response, $header) = $this->make_request($url, 'POST', $data, $sleep_if_throttled);
        return new MonkeyLearnResponse($response['result'], array($header));
    }
}

class Categories extends SleepRequests {
    function __construct($token, $base_endpoint) {
        $this->token = $token;
        $this->endpoint = $base_endpoint.'classifiers/';
    }

    function create($module_id, $name, $parent_id, $sleep_if_throttled=true) {

        $data = array(
            'name' => $name,
            'parent_id' => $parent_id,
        );

        $url = $this->endpoint.$module_id.'/categories/';
        list($response, $header) = $this->make_request($url, 'POST', $data, $sleep_if_throttled);
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function edit($module_id, $category_id, $name=null, $parent_id=null,
                  $sleep_if_throttled=true) {

        $data = array(
            'name' => $name,
            'parent_id' => $parent_id,
        );

        foreach($data as $key => $val) {
            if (!$val) {
                unset($data[$key]);
            }
        }

        $url = $this->endpoint.$module_id.'/categories/'.$category_id.'/';
        list($response, $header) = $this->make_request($url, 'PATCH', $data, $sleep_if_throttled);
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function delete($module_id, $category_id, $samples_strategy=null,
                    $samples_category_id=null, $sleep_if_throttled=true) {

        $data = array(
            'samples-strategy' => $samples_strategy,
            'samples-category-id' => $samples_category_id,
        );

        foreach($data as $key => $val) {
            if (!$val) {
                unset($data[$key]);
            }
        }

        $url = $this->endpoint.$module_id.'/categories/'.$category_id.'/';
        list($response, $header) = $this->make_request($url, 'DELETE', $data, $sleep_if_throttled);
        return new MonkeyLearnResponse($response['result'], array($header));
    }
}
?>
