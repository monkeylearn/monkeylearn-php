<?php
namespace MonkeyLearn;

class MonkeyLearnResponse {
    function __construct($result, $headers) {
        $this->result = $result;
        $this->query_limit_remaining = end($headers)['X-Query-Limit-Remaining'];
        $this->headers = $headers;
    }
}
?>
