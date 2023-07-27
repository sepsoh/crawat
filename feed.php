<?php
$config = include 'config.php';



function set_data($name,$data){
    if(!is_dir('cache'))
        mkdir('cache');
    file_put_contents('cache/'.$name,'<?php exit(); ?>'.json_encode($data));
}

function get_data($name){
    if(!is_file('cache/'.$name))
        set_data($name,[]);

    return json_decode(str_replace('<?php exit(); ?>','',file_get_contents('cache/'.$name)),true);
}

function mtproto_update(){
    global $config;
    $list = [];
    $config['mtproto']['channels'] = str_replace('@','',$config['mtproto']['channels']); // remove @ if exist in channel username
    foreach($config['mtproto']['channels'] as $channel_username){
        preg_match_all('#(?<=href=")https:\/\/t(?:elegram)?\.me\/proxy\?server[^"]+#i',file_get_contents('https://t.me/s/'.$channel_username),$data);
        $items = array_reverse($data[0]);
        foreach($items as $key => $val){
            $list[] = [
                'type'=>'MTProto',
                'time'=>time(),
                'provider'=>$channel_username,
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
function ss_update(){
    global $config;
    $list = [];
    $config['ss']['channels'] = str_replace('@','',$config['ss']['channels']); // remove @ if exist in channel username
    foreach($config['ss']['channels'] as $channel_username){
        preg_match_all('#(ss:\/\/[^\#\s\n]*)(\#[^\s\n<]+)#i',file_get_contents('https://t.me/s/'.$channel_username),$data);
        $items = array_reverse($data[0]);
        $items_tag = array_reverse($data[2]);
        foreach($items as $key => $val){
            $list[] = [
                'type'=>'ShadowSocks',
                'time'=>time(),
                'provider'=>$channel_username,
                'priority'=>$key,
                'tag'=>urldecode(ltrim($items_tag[$key] ?? 'none','#')),
                'data'=> $val
            ];
        }
    }
    usort($list,function($a,$b){
        return $a['priority']-$b['priority'];
    });
    return $list;
}

function vmess_update(){
    global $config;
    $list = [];
    $config['vmess']['channels'] = str_replace('@','',$config['vmess']['channels']); // remove @ if exist in channel username
    foreach($config['vmess']['channels'] as $channel_username){
        preg_match_all('#vmess:\/\/([a-zA-Z0-9+\/=]+)#i',file_get_contents('https://t.me/s/'.$channel_username),$data);
        $items = array_reverse($data[0]);
        $data = array_reverse($data[1]);
        foreach($items as $key => $val){
            $list[] = [
                'type'=>'vmess',
                'time'=>time(),
                'provider'=>$channel_username,
                'priority'=>$key,
                'tag'=>json_decode(base64_decode($data[$key]))->ps,
                'data'=> $val
            ];
        }
    }
    usort($list,function($a,$b){
        return $a['priority']-$b['priority'];
    });
    return $list;
}
function vless_update(){
    global $config;
    $list = [];
    $config['vless']['channels'] = str_replace('@','',$config['vless']['channels']); // remove @ if exist in channel username
    foreach($config['vless']['channels'] as $channel_username){
        preg_match_all('#(vless:\/\/[^\#\s\n]*)(\#[^\s\n<]+)#i',file_get_contents('https://t.me/s/'.$channel_username),$data);
        $items = array_reverse($data[0]);
        $items_tag = array_reverse($data[2]);
        foreach($items as $key => $val){
            $list[] = [
                'type'=>'vless',
                'time'=>time(),
                'provider'=>$channel_username,
                'priority'=>$key,
                'tag'=>urldecode(ltrim($items_tag[$key] ?? 'none','#')),
                'data'=> $val
            ];
        }
    }
    usort($list,function($a,$b){
        return $a['priority']-$b['priority'];
    });
    return $list;
}



header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

$response = [];
if(@$_GET['type'] == 'mtproto'){
    $response['ok'] = true;
    $data = get_data('mtproto-feed.json.php');

    if(count($data) == 0){
        $data = mtproto_update();
        set_data('mtproto-feed.json.php',$data);
    }

    if((time()-end($data)['time']) > $config['mtproto']['cache_time']){
        $data = mtproto_update();
        set_data('mtproto-feed.json.php',$data);
    }else{
        $data = get_data('mtproto-feed.json.php');
        $response['cache'] = true;
    }

}elseif(@$_GET['type'] == 'ss'){
    $response['ok'] = true;
    $data = get_data('ss-feed.json.php');

    if(count($data) == 0){
        $data = ss_update();
        set_data('ss-feed.json.php',$data);
    }
    if((time()-end($data)['time']) > $config['ss']['cache_time']){
        $data = ss_update();
        set_data('ss-feed.json.php',$data);
    }else{
        $data = get_data('ss-feed.json.php');
        $response['cache'] = true;
    }


}elseif(@$_GET['type'] == 'vmess'){
    $response['ok'] = true;
    $data = get_data('vmess-feed.json.php');

    if(count($data) == 0){
        $data = vmess_update();
        set_data('vmess-feed.json.php',$data);
    }
    if((time()-end($data)['time']) > $config['vmess']['cache_time']){
        $data = vmess_update();
        set_data('vmess-feed.json.php',$data);
    }else{
        $data = get_data('vmess-feed.json.php');
        $response['cache'] = true;
    }


}elseif(@$_GET['type'] == 'vless'){
    $response['ok'] = true;
    $data = get_data('vless-feed.json.php');

    if(count($data) == 0){
        $data = vless_update();
        set_data('vless-feed.json.php',$data);
    }
    if((time()-end($data)['time']) > $config['vless']['cache_time']){
        $data = vless_update();
        set_data('vless-feed.json.php',$data);
    }else{
        $data = get_data('vless-feed.json.php');
        $response['cache'] = true;
    }


}elseif(isset($_GET['list'])){
    $response['ok'] = true;
    foreach($config as $cfgKey => $cfgVal)
        if(isset($cfgVal['channels']) && count($cfgVal['channels'])>0)
            $data[] = $cfgKey;

}



sleep(1);
$response['ok'] = $response['ok'] ?? false;
$response['cache'] = $response['cache'] ?? false;
$response['response_time'] = time();
$response['result'] = $data ?? [];
echo json_encode($response);



?>