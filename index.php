<?php
if (isset($_GET["id"])) {

    $data["id"] = $_GET["id"];

    $referer = "https://space.bilibili.com/".$data["id"]."/";

    $opts = array(
        "https"=>array(
            "header"=>"User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.125 Safari/537.36\r\n"
                ."Referer:".$referer."\r\n"
        )
    );

    $context = stream_context_create($opts);

    $info = json_decode(file_get_contents("https://api.bilibili.com/x/space/acc/info?mid=".$data["id"]."&jsonp=jsonp", false, $context));

    $SubmitVideos = json_decode(file_get_contents("https://api.bilibili.com/x/space/arc/search?mid=".$data["id"]."&pn=1&ps=25&jsonp=jsonp", false, $context));
    if ($SubmitVideos->data->page->count<5) {
        $count = $SubmitVideos->data->page->count;
    } elseif ($SubmitVideos->data->page->count == 0) {
        echo json_encode(array("status"=>"400","msg"=>"No Submit Videos"));
        header("Content-Type:application/json; charset=utf-8");
        header("HTTP/1.1 400 Bad request");
        exit();
    } else {
        $count = 5;
    }
    $videos = $SubmitVideos->data->list->vlist;
    //print_r($videos);

    $data["name"] = $info->data->name;
    $data["description"] = $info->data->sign;
    //echo $data["name"];
    
    date_default_timezone_set('Asia/Shanghai');
    require_once("./rss.php");
} else {
    echo json_encode(array("status"=>"400","msg"=>"Need id"));
    header("Content-Type:application/json; charset=utf-8");
    header("HTTP/1.1 400 Bad request");
    exit();
}

function curl_request($url){
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.36');
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_AUTOREFERER, 1);
        curl_setopt($curl, CURLOPT_REFERER, "https://space.bilibili.com/");
        curl_setopt($curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($curl);
        if (curl_errno($curl)) {
            return curl_error($curl);
        }
        curl_close($curl);
}

