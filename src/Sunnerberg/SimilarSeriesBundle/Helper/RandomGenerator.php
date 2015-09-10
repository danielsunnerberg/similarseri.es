<?php

namespace Sunnerberg\SimilarSeriesBundle\Helper;

class RandomGenerator {

    public static function generateRandomUsername()
    {
        return 'anonymous_user-' . self::generateRandomString();
    }

    public static function generateRandomString($length = 32)
    {
        return 'anonymous_user-' . bin2hex(openssl_random_pseudo_bytes(32));
    }

}
