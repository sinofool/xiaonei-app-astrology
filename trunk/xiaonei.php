<?php
include_once 'simplexml44-0_4_4/class/IsterXmlSimpleXMLImpl.php';

class XiaoNei{
	var $api_key;
	var $secret;
	var $session_key;
	var $api_client;
	var $debug;
	var $params;
	
	function XiaoNei($api_key,$secret,$debug=false){
		$this->api_key = $api_key;
		$this->secret = $secret;
		$this->debug = $debug;
		$this->api_client = new XiaoNeiRestClient($api_key,$secret,$debug);
		
		$this->validate_params();
	}
	function validate_params(){
		$this->params = $this->get_valid_params($_POST, 48*3600, 'xn_sig');
    if (!$this->params) {
      $this->params = $this->get_valid_params($_GET, 48*3600, 'xn_sig');
    }
    
    if ($this->params) {
      // If we got any fb_params passed in at all, then either:
      //  - they included an fb_user / fb_session_key, which we should assume to be correct
      //  - they didn't include an fb_user / fb_session_key, which means the user doesn't have a
      //    valid session and if we want to get one we'll need to use require_login().  (Calling
      //    set_user with null values for user/session_key will work properly.)
      // Note that we should *not* use our cookies in this scenario, since they may be referring to
      // the wrong user.
      $user        = isset($this->params['user'])        ? $this->params['user'] : null;
      $session_key = isset($this->params['session_key']) ? $this->params['session_key'] : null;
      $expires     = isset($this->params['expires'])     ? $this->params['expires'] : null;
      $this->set_user($user, $session_key, $expires);
      
    } 
   
    return !empty($this->params);
    
	}
	function get_valid_params($params,$timeout=null, $namespace='xn_sig'){
		$prefix = $namespace . '_';
    $prefix_len = strlen($prefix);
		$xn_params = array();
		foreach ($params as $name => $val) {
      if (strpos($name, $prefix) === 0) {
        $xn_params[substr($name, $prefix_len)] = $this->no_magic_quotes($val);
      }
    }
    
    if ($timeout && (!isset($xn_params['time']) || time() - $xn_params['time'] > $timeout)) {
      return array();
    }
  
    return $xn_params;
	}
	
	function set_user($user, $session_key, $expires=null, $session_secret=null) {
    $this->user = $user;
    $this->api_client->session_key = $session_key;
    $this->session_expires = $expires;
  }
	
	function no_magic_quotes($val) {
    if (get_magic_quotes_gpc()) {
      return stripslashes($val);
    } else {
      return $val;
    }
  }
  
  
  function in_canvas() {
    return $this->params['in_iframe']==0;
  }
  
  function verify_signature($params, $expected_sig) {
    return $this->generate_sig($params, $this->secret) == $expected_sig;
  }
  
  function generate_sig($params_array, $secret) {
    $str = '';

    ksort($params_array);
    // Note: make sure that the signature parameter is not already included in
    //       $params_array.
    foreach ($params_array as $k=>$v) {
      $str .= "$k=$v";
    }
    $str .= $secret;

    return md5($str);
  }
}

class XiaoNeiRestClient{
	var $api_key;
	var $secret;
	var $session_key;
	//var $version = "1.0";
	var $error_code;
	var $server_addr;
	var $debug;
	
	function XiaoNeiRestClient($api_key,$secret,$debug=false){
		$this->api_key 	= $api_key;
		$this->secret 	=	$secret;
		$this->server_addr = 'http://api.xiaonei.com/restserver.do';
		$this->debug = $debug;
		if($this->debug){
			$this->cur_id = 0;
		}
	}
	
	function user_getLoggedInUser(){
		return $this->call_method("xiaonei.users.getLoggedInUser",array());
	}

	function user_getInfo($ids){
		$p["uids"] = $ids;
		$p["fields"] = "uid,name,sex,birthday";
		return $this->call_method("xiaonei.users.getInfo",$p);
	}
	
	function feed_publishTemplatizedAction($template_id, $title_data, $body_data, $resource_id){
		$p["template_id"]=$template_id;
		$p["title_data"]=$title_data;
		$p["body_data"]=$body_data;
		$p["resource_id"]=$resource_id;
		return $this->call_method("xiaonei.feed.publishTemplatizedAction", $p);
	}
	
	function requests_sendRequest($uids){
		$p["uids"]=$uids;
		return $this->call_method("xiaonei.requests.sendRequest", $p);
	}
	function profile_setXNML($uid,$profile, $profileAction){
		$p["profile"]=$profile;
		$p["profile_action"]=$profileAction;
		$p["uid"]=$uid;
		return $this->call_method("xiaonei.profile.setXNML", $p);
	}

	function call_method($method, $params) {
		$xml = $this->post_request($method, $params);
		
		$impl = new IsterXmlSimpleXMLImpl();
    $sxml = $impl->load_string($xml);
    $result = array();
    $children = $sxml->children();
    $result = $this->convert_simplexml_to_array($children[0]);
    
    
    if ($this->debug){
      // output the raw xml and its corresponding php object, for debugging:
      //echo "test";
      print '<div style="margin: 10px 30px; padding: 5px; border: 2px solid black; background: gray; color: white; font-size: 12px; font-weight: bold;">';
      $this->cur_id++;
      print $this->cur_id . ': Called ' . $method . ', show ' .
            '<a>Params</a> | '.
            '<a>XML</a> | '.
            '<a>PHP</a>';
      print '<pre id="params'.$this->cur_id.'">'.print_r($params, true).'</pre>';
      print '<pre id="xml'.$this->cur_id.'">'.htmlspecialchars($xml).'</pre>';
      print '<pre id="php'.$this->cur_id.'">'.print_r($result, true).'</pre>';
      print '</div>';
    
  	}
    if (is_array($result) && isset($result['error_code'])) {
      $this->error_code = $result['error_code'];
      return null;
    }
    return $result;
	}
	function post_request($method, $params) {
    $params['method'] = $method;
    $params['session_key'] = $this->session_key;
    $params['api_key'] = $this->api_key;
    $params['call_id'] = fb_microtime_float(true);
    if ($params['call_id'] <= $this->last_call_id) {
      $params['call_id'] = $this->last_call_id + 0.001;
    }
    $this->last_call_id = $params['call_id'];
    if (!isset($params['v'])) {
      $params['v'] = '1.0';
    }
    $post_params = array();
    foreach ($params as $key => $val) {
      if (is_array($val)) $params[$key] = implode(',', $val);
      $post_params[] = $key.'='.urlencode($params[$key]);
    }
    $secret = $this->secret;
    $post_params[] = 'sig='.generate_sig($params, $secret);
    $post_string = implode('&', $post_params);

    if (function_exists('curl_init')) {
      // Use CURL if installed...
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $this->server_addr);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Facebook API PHP4 Client 1.1 (curl) ' . phpversion());
      $result = curl_exec($ch);
      curl_close($ch);
    } else {
      // Non-CURL based version...
      //echo("no curl");
      
      $context =
        array('http' =>
              array('method' => 'POST',
                    'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
                                'User-Agent: Facebook API PHP4 Client 1.1 (non-curl) '.phpversion()."\r\n".
                                'Content-length: ' . strlen($post_string),
                    'content' => $post_string));
      $contextid=stream_context_create($context);
      $sock=fopen($this->server_addr, 'r', false, $contextid);
      if ($sock) {
        $result='';
        while (!feof($sock))
          $result.=fgets($sock, 4096);

        fclose($sock);
      }
    }
    return $result;
  }
  function convert_simplexml_to_array($sxml) {
    if ($sxml) {
      $arr = array();
      $attrs = $sxml->attributes();
      foreach ($sxml->children() as $child) {
        if (!empty($attrs['list'])) {
          $arr[] = $this->convert_simplexml_to_array($child);
        } else {
          $arr[$child->___n] = $this->convert_simplexml_to_array($child);
        }
      }
      if (sizeof($arr) > 0) {
        return $arr;
      } else {
        return (string)$sxml->CDATA();
      }
    } else {
      return '';
    }
  }
  
}

function fb_microtime_float() {
  list($usec, $sec) = explode(' ', microtime());
  return ((float)$usec + (float)$sec);
}

function generate_sig($params_array, $secret) {
    $str = '';

    ksort($params_array);
    // Note: make sure that the signature parameter is not already included in
    //       $params_array.
    foreach ($params_array as $k=>$v) {
      $str .= "$k=$v";
    }
    $str .= $secret;

    return md5($str);
  }
?>
