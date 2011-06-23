<?php

$mod = array(

);
$queryString = $_SERVER['QUERY_STRING'];
$new = "http://localhost:8080/solr/select/?" . modifyUrl($queryString, $mod);
#var_dump(modifyUrl($queryString, $mod));
#exit();
#die($_SERVER['QUERY_STRING']);
readfile($new);
exit();



function modifyUrl($url, $mod) {
    $query = explode("&", $url);
    foreach ($query as $q) {
        list($key, $value) = explode("=", $q);
        if (array_key_exists($key, $mod)) {
            if ($mod[$key]) {
                $url = preg_replace('/'.$key.'='.$value.'/', $key.'='.$mod[$key], $url);
            } else {
                $url = preg_replace('/&?'.$key.'='.$value.'/', '', $url);
            }
        }
    }
    // add new data
    foreach ($mod as $key => $value) {
        if ($value && !preg_match('/'.$key.'=/', $url)) {
            $url .= $key.'='.$value;
        }
    }
    return $url;
}

?>
