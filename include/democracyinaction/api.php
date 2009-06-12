<?php

require(dirname(__FILE__).'/client.php');
require(dirname(__FILE__).'/parser.php');

class DemocracyInAction_API {
    var $client;
    var $node;
    var $email;
    var $pass;

    function DemocracyInAction_API($node=null, $email=null, $pass=null) {
        $this->__construct($node, $email, $pass);
    }

    function __construct($node=null, $email=null, $pass=null) {
        $this->node = $node;
        $this->email = $email;
        $this->pass = $pass;
        $this->client = new DemocracyInAction_Client();
    }

    function authentication_url() {
        return 'https://'.$this->node.'/api/authenticate.sjs';
    }

    function authenticate() {
        return $this->client->send($this->authentication_url(), 
        array('email' => $this->email, 'password' => $this->pass),
        'post');
    }

    function save_url() {
        return 'https://'.$this->node.'/save';
    }

    function save($object, $data) {
        $this->authenticate();
        $data['xml'] = true;
        $data['object'] = $object;
        list($status, $data) = $this->client->send($this->save_url(), $data, 'post');

        if(false != strpos($data, '<success')) {
            preg_match('/key="(\d+)/', $data, $matches);
            return $matches[1];
        }
    }

    function get($object, $options) {
        $this->authenticate();
        if(is_string($options) || is_integer($options)) {
            $key = $options;
            return $this->get_object($object, $key);
        } else {
            return $this->get_objects($object, $options);
        }
    }

    function get_object($object, $key) {
        $url = 'https://'.$this->node.'/api/getObject.sjs';
        $options['key'] = $key;
        $options['object'] = $object;
        list($status, $data) = $this->client->send($url, $options);

        return DemocracyInAction_Parser::parse_object($data);
    }

    function get_objects($object, $options) {
        $url = 'https://'.$this->node.'/api/getObjects.sjs';
        $options['object'] = $object;
        list($status, $data) = $this->client->send($url, $options);

        return DemocracyInAction_Parser::parse_objects($data);
    }

    function report($key) {
        $this->authenticate();
        $url = 'https://'.$this->node."/api/getReport.sjs?key=$key";
        list($status, $data) = $this->client->send($url, null);

        return DemocracyInAction_Parser::parse_report($data);
    }
}

?>
