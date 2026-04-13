<?php

class RateLimiter {

    public static function check($ip, $form) {

        $key = 'leadflow_' . md5($ip . $form);
        $attempts = (int) get_transient($key);

        if ($attempts >= 5) {
            return false;
        }

        set_transient($key, $attempts + 1, 15 * MINUTE_IN_SECONDS);

        return true;
    }
}