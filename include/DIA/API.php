<?php

if(!defined('DIR_SEP')) 		define('DIR_SEP', DIRECTORY_SEPARATOR);
if(!defined('DIA_DIR')) 		define('DIA_DIR', dirname(__FILE__) . DIR_SEP);
if(!defined('DIA_API_DIR')) 	define('DIA_API_DIR', DIA_DIR.'API'.DIR_SEP);
if(!defined('DIA_EXTLIB')) 		define('DIA_EXTLIB', DIA_DIR.'extlib'.DIR_SEP);
if(!defined('DIA_EXTLIB_PEAR'))	define('DIA_EXTLIB_PEAR', '');

class DIA_API {

	var $ERROR = "";
	var $WARNING = "";

	function DIA_API() {
		$this->init();
	}

	function init() {
		if( defined('DIA_API_INITIALIZED') ) {
			return;
		}

		if( !defined('DIA_API_DEBUG') ) {
			define('DIA_API_DEBUG', false);
		}

		if( !defined('DIA_MESSAGE_DATA_NOT_FOUND') ) {
			define('DIA_MESSAGE_DATA_NOT_FOUND', 'No data found');
		}

		define('DIA_API_INITIALIZED', true);
	}

	//factory method
    function &create($api = null, $options=null) {
        if (!isset($api)) $api = DIA_API::getDefaultAPI();
        if(include_once(DIA_API_DIR . $api . '.php')) {
            $classname = 'DIA_API_'.$api;
            $item = &new $classname($options);
            return $item;
        } else {
			return DIA_API::error('failed to include API package');
		}

		return DIA_API::error('Could not create new DIA API using '.$api.' as the interface.');
    }

	function getDefaultAPI() {
		return 'HTTP_Request';
	}

	//options are key, column, order, limit, where, desc
	function get( $table, $options = null ) {
		if(defined('DIA_API_CACHE_ON') && DIA_API_CACHE_ON && !(true == $options['DIA_API_CACHE_OFF'])) {
			$this->cacheGet($table, $options);
		}
		$this->error( "get must be overwritten" );
	}

	//options are key, debug
	function process( $table, $data, $options = null ) {
		$this->error( "process must be overwritten" );
	}

	function cacheGet($table, $options) {
		$this->error( "cacheGet must be overwritten" );
	}

	function cacheProcess() {
		$this->error( "cacheProcess must be overwritten" );
	}

	function delete( $table, $criteria ) {
		$this->error( "delete must be overwritten" );
	}
		
	//thank you magpie
	function error ($errormsg, $lvl=E_USER_WARNING) {
        // append PHP's error message if track_errors enabled

        if ( isset( $php_errormsg ) && $php_errormsg ) {
            $errormsg .= " ($php_errormsg)";
        }
        if ( defined('DIA_API_DEBUG') && DIA_API_DEBUG ) {
            trigger_error( $errormsg, $lvl);        
        }
        else {
            error_log( $errormsg, 0);
        }

        $notices = E_USER_NOTICE|E_NOTICE;
        if ( $lvl&$notices ) {
            $this->WARNING = $errormsg;
        } else {
            $this->ERROR = $errormsg;
        }
    }

	//convenience
	function describe( $table ) {
		return $this->get( $table, array('desc'=>true));
	}

    function addSupporter ( $data, $deprecated_info = array() ) {
		if(is_array($deprecated_info) && !empty($deprecated_info)) {
			$this->error('info array in addSupporter is deprecated');
		}

		if(!is_array($data)) {
			$data = array('Email' => $data);
		}
		//not for long
		$data = array_merge($data, $deprecated_info);

		$links = isset( $data['link']) ? $data['link'] : array( );
		unset($data['link']);

        $supporter_id = $this->process( "supporter", $data );

		$this->processLinks($supporter_id, $links);

        return $supporter_id;

    }

	function getSupporter($key) {
		return $this->get('supporter', $key);
	}

	function addGroup( $data ) {
		if(!is_array($data)) {
			$data = array('Group_Name' => $data);
		}
		return $this->process( "groups", $data );
	}

	function getGroup($key) {
		return $this->get('groups', array('key' => $key));
	}

	function addEvent( $data ) {
		return $this->process( "event", $data );
	}

	function getEvent($key) {
		return $this->get('event', $key);
	}

	//returns an array of supporter_groups record keys?  sure...
	function addMembers($supporters, $groups) {
		if(!is_array($groups)) {
			$groups = array($groups);
		}
		if(!is_array($supporters)) {
			$supporters = array($supporters);
		}

		foreach($groups as $group) {
			foreach($supporters as $supporter) {
				$result[] = $this->linkSupporter($group, $supporter);
			}
		}
		return $result;
	}

	function addMembersByEmail($emails, $groups) {
		if(!is_array($emails)) {
			$emails = array($emails);
		}

		foreach($emails as $email) {
			$members[] = $this->addSupporter($email);
		}
		return $this->addMembers($members, $groups);
	}

	function isMember($supporter_key, $group_key) {
		return $this->getRecordKey('supporter_groups', array('supporter_KEY' => $supporter_key,
															 'groups_KEY' => $group_key));
	}

	function isMemberByEmail($email, $group_key) {
		$supporter_key = $this->getSupporterKeyByEmail($email);
		return $this->isMember($supporter_key, $group_key);
	}

	function getSupporterKeyByEmail($email) {
		return $this->getSupporterKey(array('Email' => $email));
	}

	function getSupporterKey($criteria) {
		return $this->getRecordKey('supporter', $criteria);
	}

	function getRecordKey($table, $criteria) {
		foreach($criteria as $key => $value) {
			$where[] = $key.'="'.$value.'"';
		}
		$record = $this->get($table, array('where' => join(' AND ', $where),
										   'column' => $table.'_KEY'));
		return $record['key'];
	}

	/***
	returns an array of groups data
	*/
	function getGroups($options=null) {
		return $this->get("groups", $options);
	}

	function getGroupNames() {
		return $this->getGroups(array('column' => 'Group_Name'));
	}

	function getGroupNamesAssoc() {
		$names = $this->getGroupNames();
        if ( !$names ) return array( );

		foreach($names as $name) {
			$names_assoc[$name['groups_KEY']] = $name['Group_Name'];
		}
		return $names_assoc;
	}
		
	function processLinks($supporter_key, $links) {
        $results = array( );
		foreach($links as $link => $keys) {
			if(!is_array($keys)) {
				$keys = array($keys);
			}
			foreach($keys as $key) {
				$results[] = $this->linkSupporter($key, $supporter_key, $link);
			}
		}
		return $results;
	}

    function linkSupporter ( $key, $supporter, $link = 'groups' ) {
		$link_table = 'supporter_'.$link;
		$link_key = $link.'_KEY';

		return $this->process($link_table,
							  array('supporter_KEY' => $supporter,
									$link_key => $key)
							 );
/*
        $data = array();
        
        $data[ 'link' ] = 'groups';
        $data[ 'linkKey' ] = $list;
        $data[ 'key' ] = $supporter;
        $data[ 'updateRowValues' ] = 1;

        return $this->process( "supporter", $data );
*/

    }

	function getShortName() {
		$organization = $this->get('organization');
		return $organization['READONLY_Short_Name'];
	}

	function getBaseURL() {
		$organization = $this->get('organization');
		return $organization['Base_URL'];
	}

	function getEventIndex() {
		if(defined('DIA_EVENT_INDEX_URL')) {
			return DIA_EVENT_INDEX_URL;
		}

		$organization = null;

		if(defined('DIA_ORGANIZATION_BASE_URL')) {
			$url = DIA_ORGANIZATION_BASE_URL;
		} else {
			if(!isset($this)) {
				$api =& DIA_API::create();
			} else {
				$api =& $this;
			}
			$organization = $api->get('organization');
			if(isset($organization['Base_URL'])) {
				$url = $organization['Base_URL'];
			} else {
				$url = 'http://www.democracyinaction.org/';
			}
		}
		$url .= 'dia/organizations/';

		if(defined('DIA_ORGANIZATION_SHORT_NAME')) {
			$url .= DIA_ORGANIZATION_SHORT_NAME. '/';
		} else {
			if(!isset($organization)) {
				if(!isset($this)) {
					$api =& DIA_API::create();
				} else {
					$api =& $this;
				}
				$organization = $api->get('organization');
			}
			if(isset($organization['READONLY_Short_Name'])) {
				$url .= $organization['READONLY_Short_Name']. '/';
			} else {
				//boo
				$this->error('getEventIndex couldn\'t get short name');
				return false;
			}
		}

		if(defined('DIA_EVENT_INDEX_CONTROLLER')) {
			$url .= DIA_EVENT_INDEX_CONTROLLER;
		} else {
			$url .= 'event/index.jsp';
		}

		return $url;
	}

	//formats a unix timestamp such that DIA understands it (basically, strftime)
	function formatDate($timestamp=null) {
		return strftime('%Y-%m-%d %T', $timestamp);
	}

	//translates a DIA datetime to a unix timestamp (basically, strtotime)
	function dateToTime($date) {
		if(preg_match('/(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})/', $date, $matches)) {
			list($datetime, $year, $month, $day, $hour, $minute, $second) = $matches;
			return mktime($hour, $minute, $second, $month, $day, $year);
		}
	}

}

function dia_get($table, $options) {
	$api =& DIA_API::create();
	return $api->get($table, $options);
}

function dia_process($table, $data, $options=null) {
	$api =& DIA_API::create();
	return $api->process($table, $data, $options);
}

function dia_formatdate($timestamp=null) {
	return DIA_API::formatDate($timestamp);
}

function dia_datetotime($date) {
	return DIA_API::dateToTime($date);
}

?>
