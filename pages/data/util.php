<?php

/**
 * Contains random utility methods
 */

use PrettyDateTime\PrettyDateTime;

function random_text( $type = 'alnum', $length = 8 )
{
    switch ( $type ) {
        case 'alnum':
            $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'alpha':
            $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            break;
        case 'hexdec':
            $pool = '0123456789abcdef';
            break;
        case 'numeric':
            $pool = '0123456789';
            break;
        case 'nozero':
            $pool = '123456789';
            break;
        case 'distinct':
            $pool = '2345679ACDEFHJKLMNPRSTUVWXYZ';
            break;
        default:
            $pool = (string) $type;
            break;
    }


    $crypto_rand_secure = function ( $min, $max ) {
        $range = $max - $min;
        if ( $range < 0 ) return $min; // not so random...
        $log    = log( $range, 2 );
        $bytes  = (int) ( $log / 8 ) + 1; // length in bytes
        $bits   = (int) $log + 1; // length in bits
        $filter = (int) ( 1 << $bits ) - 1; // set all lower bits to 1
        do {
            $rnd = hexdec( bin2hex( openssl_random_pseudo_bytes( $bytes ) ) );
            $rnd = $rnd & $filter; // discard irrelevant bits
        } while ( $rnd >= $range );
        return $min + $rnd;
    };

    $token = "";
    $max   = strlen( $pool );
    for ( $i = 0; $i < $length; $i++ ) {
        $token .= $pool[$crypto_rand_secure( 0, $max )];
    }
    return $token;
}

function truncate($text, $length, $truncationChars) {
    return strlen($text) > $length ? substr($text, 0, $length - strlen($truncationChars)) . $truncationChars : $text;
}

function prettyTime($time) {
    global $dt;
    return PrettyDateTime::parse(date_create_from_format('Y-m-d H:i:s', $time), new DateTime('now'));
}

function redirect($target = '/') {
    header('Location: '.SITE_PREFIX.$target);
}

function deleteImageFile($imgId, $fileExt) {
    //delete the files
    foreach (IMAGE_FILE_SIZES as $NAME => $SIZE) {
        try {
            @unlink(IMAGE_LOCATION . DIRECTORY_SEPARATOR . $NAME . DIRECTORY_SEPARATOR . $imgId . '.' . $fileExt);
        } catch (Exception $e) {

        }
    }
}

function constructFlickrUrl($photo, $size = 'o', $format = 'jpg') {
    return 'https://farm'.$photo['farm'].'.staticflickr.com/'.$photo['server'].'/'.$photo['id'].'_'.$photo['secret'].'_'.$size.'.'.$format;
}

?>