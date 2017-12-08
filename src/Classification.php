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
        $this->categories = new Categories($token, $base_endpoint);
    }

    function classify($module_id, $sample_list, $sandbox=false,
                      $batch_size=Config::DEFAULT_BATCH_SIZE, $sleep_if_throttled=true) {
        HandleErrors::check_batch_limits($sample_list, $batch_size);
        $url = $this->endpoint.$module_id.'/classify/';
        if ($sandbox) {
            $url .= '?sandbox=1';
        }

        $res = array();
        $headers = array();
        $batches = array_chunk($sample_list, $batch_size);
        foreach($batches as $batch) {
            if (is_array($batch[0])) {
                $data = array('sample_list' => $batch);
            } else {
                $data = array('text_list' => $batch);
            }
         try{
            list($response, $header) = $this->make_request($url, 'POST', $data, $sleep_if_throttled);
         } catch (\MonkeyLearnException $mle){ throw $mle;}
            $headers[] = $header;
            $res = array_merge($res, $response['result']);
        }

        return new MonkeyLearnResponse($res, $headers);
    }

    function list_classifiers($sleep_if_throttled=true) {
        $url = $this->endpoint;
        try{
            list($response, $header) = $this->make_request($url, 'GET', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){ throw $mle;}
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function detail($module_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$module_id;
        try{
            list($response, $header) = $this->make_request($url, 'GET', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){ throw $mle;}
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function upload_samples($module_id, $samples_with_categories, $sleep_if_throttled=true,
                            $features_schema=null) {
        $url = $this->endpoint.$module_id.'/samples/';
        $data_samples = array();
        foreach($samples_with_categories as $i => $sc) {
            if (is_array($sc[0])) {
                $sample = array("features" => $sc[0]);
            } else {
                $sample = array("text" => $sc[0]);
            }

            if (is_int($sc[1]) || (is_array($sc[1]) && $sc[1] == array_filter($sc[1], 'is_int'))) {
                $sample['category_id'] = $sc[1];
            } else if (is_string($sc[1]) || (is_array($sc[1]) && $sc[1] == array_filter($sc[1], 'is_string'))) {
                $sample['category_path'] = $sc[1];
            } else if (!is_null($sc[1])){
                throw new MonkeyLearnException(
                    "Invalid category value in sample $i"
                );
            }

            if (count($sc) > 2 && $sc[2] && (is_string($sc[2]) || (is_array($sc[2]) && $sc[2] == array_filter($sc[2], 'is_string')))) {
                $sample['tag'] = $sc[2];
            }
            $data_samples[] = $sample;
        }
        $data = array('samples' => $data_samples);
        if ($features_schema) {
            $data['features_schema'] = $features_schema;
        }
        try {
            list($response, $header) = $this->make_request($url, 'POST', $data, $sleep_if_throttled);
            } catch (\MonkeyLearnException $mle){ throw $mle;}
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function train($module_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$module_id.'/train/';
        try{
            list($response, $header) = $this->make_request($url, 'POST', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){ throw $mle;}
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function deploy($module_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$module_id.'/deploy/';
        try{
            list($response, $header) = $this->make_request($url, 'POST', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){ throw $mle;}
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function delete($module_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$module_id;
        try{
            list($response, $header) = $this->make_request($url, 'DELETE', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){ throw $mle;}
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
        try{
            list($response, $header) = $this->make_request($url, 'POST', $data, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){ throw $mle;}
        return new MonkeyLearnResponse($response['result'], array($header));
    }
}

class Categories extends SleepRequests {
    function __construct($token, $base_endpoint) {
        $this->token = $token;
        $this->endpoint = $base_endpoint.'classifiers/';
    }

    function detail($module_id, $category_id, $sleep_if_throttled=true) {
        $url = $this->endpoint.$module_id.'/categories/'.$category_id.'/';
        try{
            list($response, $header) = $this->make_request($url, 'GET', null, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){ throw $mle;}
        return new MonkeyLearnResponse($response['result'], array($header));
    }

    function create($module_id, $name, $parent_id, $sleep_if_throttled=true) {

        $data = array(
            'name' => $name,
            'parent_id' => $parent_id,
        );

        $url = $this->endpoint.$module_id.'/categories/';
        try{
            list($response, $header) = $this->make_request($url, 'POST', $data, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){ throw $mle;}
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
        try{
            list($response, $header) = $this->make_request($url, 'PATCH', $data, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){ throw $mle;}
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
        try{
            list($response, $header) = $this->make_request($url, 'DELETE', $data, $sleep_if_throttled);
        } catch (\MonkeyLearnException $mle){ throw $mle;}            
        return new MonkeyLearnResponse($response['result'], array($header));
    }
}
?>
