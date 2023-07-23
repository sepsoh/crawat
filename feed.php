<?php
$config = include 'config.php';
$feed_file = 'feed.json.php';



function set_data($name,$data){
    file_put_contents($name,'<?php exit(); ?>'.json_encode($data));
}

function get_data($name){
    if(is_file($name))
        return json_decode(str_replace('<?php exit(); ?>','',file_get_contents($name)),true);
    else{
        file_put_contents($name,'<?php exit(); ?>{}');
        return [];
    }
    
}

function mtproto_update(){
    global $config;
    $list = [];
    $config['mtproto']['channels'] = str_replace('@','',$config['mtproto']['channels']); // remove @ if exist in channel username
    foreach($config['mtproto']['channels'] as $channelUsername){
        preg_match_all('#(?<=href=")https:\/\/t(?:elegram)?\.me\/proxy\?server[^"]+#i',file_get_contents('https://t.me/s/'.$channelUsername),$data);
        $proxies = array_unique(array_reverse($data[0]));
        foreach($proxies as $key => $val){
            $parts = parse_url($val);
            parse_str($parts['query'], $query);
            $list[] = [
            'type'=>'MTProto',
            'time'=>time(),
            'provider'=>$channelUsername,
            'priority'=>$key,
            'data'=> str_replace('&amp;','&',str_replace('https://t.me/','tg://',$val))
            ];
        }
    }
    usort($list,function($a,$b){
        return $a['priority']-$b['priority'];
    });
    return $list;
}

$response = [];
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if(!isset($_GET['type'])){
    $response['ok'] = false;
    $response['message'] = "Invalid parameters.";
    
}elseif($_GET['type'] == 'mtproto'){
    
    $data = get_data($feed_file);
    
    if(count($data) == 0){
        $data = mtproto_update();
        set_data($feed_file,$data);
    }
    
    if((time()-end($data)['time']) > $config['mtproto']['cache_time']){
        $data = mtproto_update();
        set_data($feed_file,$data);
        $cache = false;
    }else{
        $data = get_data($feed_file);
        $cache = true;
    }

    mtproto_update();
    $response['ok'] = true;
    $response['cache'] = $cache;
    $response['response_time'] = time();
    $response['result'] = $data;
}
echo json_encode($response);



?>