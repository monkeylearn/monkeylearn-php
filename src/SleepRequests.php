<?php
namespace MonkeyLearn;

use MonkeyLearn\MonkeyLearnException;

class SleepRequests {

    function parseHeaders($headers) {
        $head = array();
        foreach( $headers as $k=>$v )
        {
            $t = explode( ':', $v, 2 );
            if( isset( $t[1] ) )
                $head[ trim($t[0]) ] = trim( $t[1] );
            else
            {
                $head[] = $v;
                if( preg_match( "#HTTP/[0-9\.]+\s+([0-9]+)#",$v, $out ) )
                    $head['response_code'] = intval($out[1]);
            }
        }
        return $head;
    }

    function make_request($url, $method, $data=null, $sleep_if_throttled=true) {
        $options = array(
            'http' => array(
                'header'  => "Content-type: application/json\r\n".
                    "Authorization:token $this->token\r\n".
                    "User-Agent: php-sdk\r\n",
                'method'  => $method,
                'content' => json_encode($data),
                'ignore_errors' => true, // don't fail file_get_contents with status code 429
            ),
        );
        $context  = stream_context_create($options);
        while (true) {
            $result = @file_get_contents($url, false, $context);
            $headers = $this->parseHeaders($http_response_header);
            $response_json = json_decode($result, true);
            if ($sleep_if_throttled && $headers['response_code'] == 429
                    && strpos($response_json['detail'], 'seconds')) {
                $seconds = preg_match('/available in (\d+) seconds/', $response_json['detail'], $matches);
                sleep($matches[1]);
                continue;
            } else if ($sleep_if_throttled && $headers['response_code'] == 429
                    && strpos($response_json['detail'], 'Too many concurrent requests')) {
                sleep(2);
                continue;
            } else if ($headers['response_code'] != 200) {
                throw new MonkeyLearnException($response_json['detail']);
            }
            return array($response_json, $headers);
        }
    }
}
?>
