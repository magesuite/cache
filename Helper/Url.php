<?php

namespace MageSuite\Cache\Helper;

class Url
{
    /**
     * Removes query string, protocol and trailing slash from url
     * @param $url
     * @return mixed|string
     */
    public function normalize($url)
    {
        $url = strtok($url, '?');
        $parsedUrl = parse_url($url);

        if (isset($parsedUrl['host']) and isset($parsedUrl['path'])) {
            $url = $parsedUrl['host'] . $parsedUrl['path'];
        }

        $url = rtrim($url, '/');

        return $url;
    }
}