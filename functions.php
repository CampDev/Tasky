<?php
//All the functions used in Tasky
//Functions to parse the header to an array
if (!function_exists('http_parse_headers')) {
    function http_parse_headers($headers){
        if($headers === false){
            return false;
            }
        $headers = str_replace("\r","",$headers);
        $headers = explode("\n",$headers);
        foreach($headers as $value){
            $header = explode(": ",$value);
            if($header[0] && !$header[1]){
                $headerdata['status'] = $header[0];
            }
            elseif($header[0] && $header[1]){
                $headerdata[$header[0]] = $header[1];
            }
            }
        return $headerdata;
    }
}

//Function to discovery an entity's meta post
function discover_link($entity_uri, $debug){
        $entity_sub = substr($entity_uri, 0, strlen($entity_uri)-1);
        $header_result = get_headers($entity_uri);
        foreach ($header_result as $header_value) {
            if (preg_match("/Link:.*/", $header_value)) {
                $link = $header_value;
            }
        }
        // This needs a more flexible solution because the place where the link is located may vary
        $discovery_link = str_replace("<", "", $link);
		$discovery_link = str_replace(">", "", $discovery_link);
        $discovery_link = str_replace("Link: ", "", $discovery_link);
		$discovery_link = str_replace('; rel="https://tent.io/rels/meta-post"', "", $discovery_link);
       	$ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $entity_sub.$discovery_link);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    $meta = json_decode(curl_exec($ch), true);
	    curl_close($ch);
	    if ($debug == true) {
            echo "<p><b>Entity-Sub: </b>".$entity_sub.$discovery_link."</p>";
            echo "<hr /><p><b>Header: </b></p>";
            var_dump($header_result);
            echo "<p><b>Status: ".$header_result[0]."</b></p>";
            echo "<p><b>Length: ".$header_result[1]."</b></p>";
            echo "<hr /><p><b>Discovered Link: </b></p>";
            echo "<p>".$discovery_link."</p>";
            echo "<hr /> <p><b>Meta Post: </b></p>";	
	     	var_export($meta);
	        }
	    return $meta;
}

//Function to generate the mac for requests
function generate_mac($header_type, $ts, $nonce, $method, $request_uri, $host, $port, $app, $hawk_key, $debug) {
    //TODO: Implement that everywhere
    $mac_data = $header_type."\n".$ts."\n".$nonce."\n".$method."\n".$request_uri."\n".$host."\n".$port."\n\n\n".$app."\n\n";
    $mac_n = $header_type.'\n'.$ts.'\n'.$nonce.'\n'.$method.'\n'.$request_uri.'\n'.$host.'\n'.$port.'\n\n\n'.$app.'\n\n';
    $mac_sha256 = hash_hmac('sha256', $mac_data, $hawk_key, true);
    $mac = base64_encode($mac_sha256);
    if ($debug == true) {
        echo "<p><b>Mac Data:</b> ".$mac_data."</p>";
        echo "<p><b>Mac N:</b> ".$mac_n."</p>";
        echo "<p><b>Mac-SHA256:</b> ".$mac_sha256."</p>";
        echo "<p><b>Mac:</b> ".$mac."</p>";
    }
    return $mac;
}

function generate_auth_header($hawk_id, $mac, $ts, $nonce, $app_id) {
    $auth_header = 'Authorization: Hawk id="'.$hawk_id.'", mac="'.$mac.'", ts="'.$ts.'", nonce="'.$nonce.'", app="'.$app_id.'"';
    return $auth_header;
}

function created_date($timestamp){
    $ts = $timestamp/1000;
    $time = date('d.m.Y - G:i', $ts);
    return $time;
}
?>