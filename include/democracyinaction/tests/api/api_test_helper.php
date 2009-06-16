<?php

class ApiTestCase extends UnitTestCase {
    function assertSameXMLStructure($target, $expected) {
        $target_xml = new SimpleXMLElement($target); 
        $expected_xml = new SimpleXMLElement($expected);

        $this->assertSubtreeExists($target, $expected);
        $this->assertSubtreeExists($expected, $target);
    }

    # recursively assert that the xpath of all 
    # nodes of the subtree exists in the doc
    function assertSubtreeExists($subtree, $doc, $xpath_prefix='') {
        if(is_string($doc)) {
            $doc = new SimpleXMLElement($doc);
        }
        if(is_string($subtree)) {
            $subtree = new SimpleXMLElement($subtree);
        }

        $xpath = $xpath_prefix.'/'.$subtree->getName();

        $matches = $doc->xpath($xpath);
        $this->assertFalse(empty($matches), "$xpath does not exist in ".$doc->asXML());
        foreach($subtree->children() as $key => $child) {
            $this->assertSubtreeExists($child, $doc, $xpath);
        }
    }
}

function test_user() {
    return '1';
    return $_ENV['USER'];
}

function test_pass() {
    return '1';
    return $_ENV['PASS'];
}

function test_node() {
    return 'sandbox.salsalabs.com';
    return $_ENV['NODE'];
}

function authentication_url() {
    return 'https://'.test_node().'/api/authenticate.sjs';
}

function authenticated_curl_handle() {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_COOKIESESSION, TRUE);
    $temp_file = tempnam(sys_get_temp_dir(), 'DIA');
    curl_setopt($ch, CURLOPT_COOKIEFILE, $temp_file);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $temp_file);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    curl_setopt($ch, CURLOPT_URL, authentication_url());
    curl_setopt($ch, CURLOPT_POSTFIELDS, "email=".test_user()."&password=".test_pass());
    $data = curl_exec($ch);
    return $ch;
}

function authenticated_get($path) {
    $ch = authenticated_curl_handle();
    $url = 'https://'.test_node().$path;
    curl_setopt($ch, CURLOPT_URL, $url);
    $data = curl_exec($ch);

    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array($status, $data);
}

function post($url, $fields) {
    return send($url, CURLOPT_POST, $fields);
}

function authenticated_send($url, $method, $fields) {
    $ch = authenticated_curl_handle();
    return send($url, $method, $fields, $ch);
}

function authenticated_post($url, $fields) {
    return authenticated_send($url, CURLOPT_POST, $fields);
}

function send($url, $method, $fields, $ch=null) {
    if(is_array($fields)) {
        $fields = http_build_query($fields);
    }
    if(is_null($ch)) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    }

#    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, $method, 1); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);

    $data = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return array($status, $data);
}

