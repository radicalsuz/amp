<?php

//
// smtp_client class -------------------------------------------------------
// from: http://zend.com/codex.php?id=347&single=1
// author: http://zend.com/search_code_author.php?author=gollino
// modified: elijah
// use:
//   $smtp = new smtp_client();
//   $smtp->email($from, $to[0], $to_name[0], $header[0], $subject[0], $body[0]);
//   $smtp->email($from, $to[1], $to_name[1], $header[1], $subject[1], $body[1]);
//   $smtp->send();
//

class smtp_client {
    var $connection;
    var $server;
    var $elog_fp;
    var $log_file='./smtp_client.log';
    var $do_log=true;    
	var $ok = true;
	var $msg = '';
	
    // default constructor
    function smtp_client($server='') {
        if (!$server) $this->server="localhost";
        else $this->server=$server;
        
        $this->connection = fsockopen($this->server, 25);
        if ($this->connection <= 0) {
        	$this->ok = false;
        	return;
        }

        $this->elog(fgets($this->connection, 1024));
        $this->elog("HELO xyz\r\n", 1);
        fputs($this->connection,"HELO xyz\r\n");
        $this->elog(fgets($this->connection, 1024));
    }

	function fgets() {
		$result = fgets($this->connection, 1024);
		if ($result === FALSE) {
			$this->elog("reading SMTP socket returned false");
			$this->ok = false;
		}
		elseif ($result === EOF) {
			$this->elog("reading SMTP socket hit EOF");
			$this->ok = false;
		}
		else {
			$this->elog($result);
		}
	}

    function email($from_mail, $to_mail, $to_name, $header, $subject, $body) {
        if ($this->connection <= 0) {
        	$this->ok = false;
			$this->msg = "smtp: no connection";
        	return false;
        }
    
        $this->elog("MAIL FROM:$from_mail", 1);
        fputs($this->connection,"MAIL FROM:$from_mail\r\n");
        $this->fgets();
		if (!$this->ok) return false;
		
        $this->elog("RCPT TO:$to_mail", 1);
        fputs($this->connection, "RCPT TO:$to_mail\r\n");
		$this->fgets();
		if (!$this->ok) return false;
		
        $this->elog("DATA", 1);
        fputs($this->connection, "DATA\r\n");
        $this->fgets();
		if (!$this->ok) return false;
    
        $this->elog("Subject: $subject", 1);
        fputs($this->connection,"Subject: $subject\r\n");
		if ($to_name != '') {
            $this->elog("To: $to_name", 1);
	        fputs($this->connection,"To: $to_name\r\n");
        }
        else {
            $this->elog("To: $to_mail", 1);
	        fputs($this->connection,"To: $to_mail\r\n");
        }

        if ($header) {
            $this->elog($header, 1);
            fputs($this->connection, "$header\r\n");
        }

        $this->elog("", 1);
        $this->elog($body, 1);
        $this->elog(".", 1);
        fputs($this->connection,"\r\n");
        fputs($this->connection,"$body \r\n");
        fputs($this->connection,".\r\n");
		$this->fgets();
		if (!$this->ok) return false;		
        return true;
    }

    function send() {
        if ($this->connection) {
            fputs($this->connection, "QUIT\r\n");
            fclose($this->connection);
            $this->connection=0;
        }
    }

    function close() { $this->send(); }

    function elog($text, $mode=0) {
        if (!$this->do_log) return;

        // open file
        if (!$this->elog_fp) {
            if (!($this->elog_fp=fopen($this->log_file, 'a'))) return;
            fwrite($this->elog_fp, "\n-------------------------------------------\n");
            fwrite($this->elog_fp, " Sent " . date("Y-m-d H:i:s") . "\n");
            fwrite($this->elog_fp, "-------------------------------------------\n");
        }

        // write to log
        if (!$mode) fwrite($this->elog_fp, "    $text\n");
        else fwrite($this->elog_fp, "$text\n");
    }
} // end class smtp_client

?>