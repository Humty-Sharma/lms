<?php

/**
 * Get ip info from ipinfo.io
 *
 * There are other services like this, for example
 * http://extreme-ip-lookup.com/json/106.192.146.13
 * http://ip-api.com/json/113.14.168.85
 *
 * source: https://stackoverflow.com/a/54721918/3057377
 */
function getIpInfo($ip = '') {
    $ipinfo = file_get_contents("https://ipinfo.io/" . $ip);
    $ipinfo_json = json_decode($ipinfo, true);
    return $ipinfo_json;
}

function displayIpInfo($ipinfo_json) {
    var_dump($ipinfo_json);
    echo <<<END
<pre>
ip      : {$ipinfo_json['ip']}
city    : {$ipinfo_json['city']}
region  : {$ipinfo_json['region']}
country : {$ipinfo_json['country']}
loc     : {$ipinfo_json['loc']}
postal  : {$ipinfo_json['postal']}
org     : {$ipinfo_json['org']}
</pre>
END;
}

function main() {
    echo("<h1>Server IP information</h1>");
    $ipinfo_json = getIpInfo();
    displayIpInfo($ipinfo_json);

    echo("<h1>Visitor IP information</h1>");
    $visitor_ip = $_SERVER['REMOTE_ADDR'];
    $ipinfo_json = getIpInfo($visitor_ip);
    displayIpInfo($ipinfo_json);
}

main();

?>