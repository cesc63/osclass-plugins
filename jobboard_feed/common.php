<?php

function ef_tag($tag, $value, $echo = true) {
    $string = '        <' . $tag . '><![CDATA[' . trim($value) . ']]></' . $tag . '>' . PHP_EOL;

    if( !$echo ) {
        return $string;
    }

    echo $string;
}

function ef_tag_if_exists($tag, $var, $key, $echo = true) {
    if( !is_array($var) ) {
        return false;
    }
    if( !array_key_exists($key, $var) ) {
        return false;
    }

    if( $var[$key] === '' ) {
        return false;
    }

    if( $var[$key] === 0 ) {
        return false;
    }

    if( $var[$key] === '0' ) {
        return false;
    }

    ef_tag( $tag, $var[$key], $echo );
}