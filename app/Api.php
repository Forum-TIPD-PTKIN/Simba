<?php

namespace App;

use GuzzleHttp\Client;

class Api
{
    public static function post($url, $data = [], $header = [])
    {
        try {
            $urlParts = parse_url($url);
            if (isset($urlParts['query'])) {
                $queryArray = [];
                parse_str($urlParts['query'], $queryArray);
                $data = array_merge($queryArray, $data);
                $url = (isset($urlParts['scheme']) ? $urlParts['scheme'] . '://' : '') .
                    (isset($urlParts['host']) ? $urlParts['host'] : '') .
                    (isset($urlParts['port']) ? ':' . $urlParts['port'] : '') . // Tambahkan port agar tidak error di localhost
                    (isset($urlParts['path']) ? $urlParts['path'] : '');
            }
            $client = new Client();
            $response = $client->postAsync($url, [
                "form_params" => $data,
                "headers" => $header,
                "verify" => false,
            ])->wait();
            $data = $response->getBody()->getContents();
            $n = new \stdClass();
            $n->status = true;
            $n->data = json_decode($data);
            return $n;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $result = json_decode($response->getBody()->getContents(), true);
            $n = new \stdClass();
            $n->status = false;
            $n->data = $result;
            return $n;
        }
    }

    public static function get($url, $data = [], $header = [])
    {
        try {
            $urlParts = parse_url($url);
            if (isset($urlParts['query'])) {
                $queryArray = [];
                parse_str($urlParts['query'], $queryArray);
                $data = array_merge($queryArray, $data);
                $url = (isset($urlParts['scheme']) ? $urlParts['scheme'] . '://' : '') .
                    (isset($urlParts['host']) ? $urlParts['host'] : '') .
                    (isset($urlParts['port']) ? ':' . $urlParts['port'] : '') . // Tambahkan port agar tidak error di localhost
                    (isset($urlParts['path']) ? $urlParts['path'] : '');
            }
            $client = new Client();
            $response = $client->getAsync($url, [
                "query" => $data,
                "headers" => $header,
                "verify" => false,
            ])->wait();
            $data = $response->getBody()->getContents();
            $n = new \stdClass();
            $n->status = true;
            $n->data = json_decode($data);
            return $n;
        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();
            $result = json_decode($response->getBody()->getContents(), true);
            $n = new \stdClass();
            $n->status = false;
            $n->data = $result;
            return $n;
        }
    }
}
