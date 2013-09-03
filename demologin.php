<?php

        try {
                global $settings;
                $token = login($settings['user'], $settings['pass']);
                login($settings['user'], $settings['pass'], $token);
                echo ("SUCCESS");
        } catch (Exception $e) {
                die("FAILED: " . $e->getMessage());
        }

$settings['wikiroot'] = "http://smorz.cs.yale.edu/cryptowiki/wiki";
$settings['user'] = "Bryan";
$settings['pass'] = "test";
$settings['cookiefile'] = "cookies.tmp";

function httpRequest($url, $post="") {
        global $settings;

        $ch = curl_init();
        //Change the user agent below suitably
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.9) Gecko/20071025 Firefox/2.0.0.9');
        curl_setopt($ch, CURLOPT_URL, ($url));
        curl_setopt( $ch, CURLOPT_ENCODING, "UTF-8" );
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt ($ch, CURLOPT_COOKIEFILE, $settings['cookiefile']);
        curl_setopt ($ch, CURLOPT_COOKIEJAR, $settings['cookiefile']);
        if (!empty($post)) curl_setopt($ch,CURLOPT_POSTFIELDS,$post);
        //UNCOMMENT TO DEBUG TO output.tmp
        //curl_setopt($ch, CURLOPT_VERBOSE, true); // Display communication with server
        //$fp = fopen("output.tmp", "w");
        //curl_setopt($ch, CURLOPT_STDERR, $fp); // Display communication with server
        
        $xml = curl_exec($ch);
        
        if (!$xml) {
                throw new Exception("Error getting data from server ($url): " . curl_error($ch));
        }

        curl_close($ch);
        
        return $xml;
}


function login ($user, $pass, $token='') {
        global $settings;

        $url = $settings['wikiroot'] . "/api.php?action=login&format=xml";

        $params = "action=login&lgname=$user&lgpassword=$pass";
        if (!empty($token)) {
                $params .= "&lgtoken=$token";
        }

        $data = httpRequest($url, $params);
        
        if (empty($data)) {
                throw new Exception("No data received from server. Check that API is enabled.");
        }

        $xml = simplexml_load_string($data);
        
        if (!empty($token)) {
                //Check for successful login
                $expr = "/api/login[@result='Success']";
                $result = $xml->xpath($expr);

                if(!count($result)) {
                        throw new Exception("Login failed");
                }
        } else {
                $expr = "/api/login[@token]";
                $result = $xml->xpath($expr);

                if(!count($result)) {
                        throw new Exception("Login token not found in XML");
                }
        }
        
        return $result[0]->attributes()->token;
}

?>