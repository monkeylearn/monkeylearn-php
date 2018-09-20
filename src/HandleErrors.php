<?php
namespace MonkeyLearn;

use MonkeyLearn\Config;
use MonkeyLearn\MonkeyLearnException;

class HandleErrors {
    static function check_batch_limits($data, $batch_size) {
        if ($batch_size > Config::MAX_BATCH_SIZE || $batch_size < Config::MIN_BATCH_SIZE) {
            throw new MonkeyLearnException(
                "batch_size has to be between {Config::MIN_BATCH_SIZE} and {Config::MAX_BATCH_SIZE}"
            );
        }
        if (!$data) {
            throw new MonkeyLearnException(
                "The data can't be empty."
            );
        }
    }
}
?>
