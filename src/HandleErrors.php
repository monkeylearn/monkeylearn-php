<?php
namespace MonkeyLearn;

use MonkeyLearn\Config;
use MonkeyLearn\MonkeyLearnException;

class HandleErrors {
    static function check_batch_limits($text_list, $batch_size) {
        if ($batch_size > Config::MAX_BATCH_SIZE || $batch_size < Config::MIN_BATCH_SIZE) {
            throw new MonkeyLearnException(
                "batch_size has to be between {Config::MIN_BATCH_SIZE} and {Config::MAX_BATCH_SIZE}"
            );
        }
        if (!$text_list) {
            throw new MonkeyLearnException(
                "The text_list can't be empty."
            );
        }
        if (in_array('', $text_list)) {
            throw new MonkeyLearnException(
                "You have an empty text in position ".array_search('', $text_list)." in text_list."
            );
        }
    }
}
?>
