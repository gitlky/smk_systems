<?php

namespace App\Http\Controllers\smk_systems\Tool;

use anlutro\cURL\cURL;

class Smk_Http
{
    private $http_domain;

    public function __construct($domain = null)
    {
        $this->http_domain = null == $domain ? "https://qyapi.weixin.qq.com/cgi-bin/" : $domain;
    }


    public function get($url, Array $data=[], $token = null)
    {
        $url = $this->http_domain . $url;
        if (null != $token) {
            $data['access_token'] = $token;
        }
        $curl = new cURL();
        $url_all = $curl->buildUrl($url, $data);
        $response = $curl->get($url_all);
        if ($response->statusCode == 200) {
            return $response->body;
        }
    }


    public function post($url, Array $data=[], $token = null)
    {
        $url = $this->http_domain . $url;
        $url = null == $token ? $url : $url . '?access_token=' . $token;
        $curl = new cURL();
        $response = $curl->jsonPost($url, $data);
        if ($response->statusCode == 200) {
            return $response->body;
        }
    }

    public function get_json_post($url, Array $data=[], $token = null)
    {
        $data = $this->post($url, $data, $token);
        return json_decode($data);
    }

    public function get_json_get($url, Array $data=[], $token = null)
    {
        return json_decode($this->get($url, $data, $token));
    }
}
