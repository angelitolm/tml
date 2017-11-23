<?php

namespace AppBundle\Services;

class Utiles
{
    function truncate($value, $length = 30, $preserve = false, $separator = '...')
    {
        if (strlen($value) > $length) {
            if ($preserve) {
                if (false !== ($breakpoint = strpos($value, ' ', $length))) {
                    $length = $breakpoint;
                }
            }

            return rtrim(substr($value, 0, $length)) . $separator;
        }

        return $value;
    }

    function get_slug( $cadena, $separador = '-' )
    {
        // Código copiado de http://cubiq.org/the-perfect-php-clean-url-generator
        $slug = iconv( 'UTF-8', 'ASCII//TRANSLIT', $cadena );
        $slug = preg_replace( "/[^a-zA-Z0-9\/_|+ -]/", '', $slug );
        $slug = strtolower( trim( $slug, $separador ) );
        $slug = preg_replace( "/[\/_|+ -]+/", $separador, $slug );

        return $slug;
    }

    public function in_str( $haystack, $needle )
    {
        return strpos( $haystack, $needle ) !== false;
    }
}