<?php

class DemocracyInAction_Client {
    var $cookiefile;

    function DemocracyInAction_Client($cookiefile=null) {
        return $this->__construct($cookiefile);
    }

    function __construct($cookiefile=null) {
        if(is_null($cookiefile)) {
            $cookiefile = tempnam(null, 'DemocracyInAction_Client');
        }
        $this->cookiefile = $cookiefile;
    }

    function send($url, $fields, $method=null) {
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_handle, CURLOPT_HEADER, 0);
        curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl_handle, CURLOPT_COOKIEFILE, $this->cookiefile);
        curl_setopt($curl_handle, CURLOPT_COOKIEJAR, $this->cookiefile);

        if(is_null($method)) {
            curl_setopt($curl_handle, CURLOPT_HTTPGET, 1);
        } elseif('post' == $method) {
            curl_setopt($curl_handle, CURLOPT_POST, 1); 
        } else {
            trigger_error('method not supported');
        }
        curl_setopt($curl_handle, CURLOPT_URL, $url);

        if(is_array($fields)) {
            $ret = array();
            foreach($fields as $key => $value) {
                $ret[] = "$key=".urlencode($value);
            }
            $fields = implode('&', $ret);
        }
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, $fields);

        $data = curl_exec($curl_handle);
        $status = curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
        curl_close($curl_handle);

        return array($status, $data);
    }
}

?>
