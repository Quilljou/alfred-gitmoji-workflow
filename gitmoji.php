<?php
require_once('workflows.php');
$w = new Workflows();
$query = urldecode(strtolower(trim($query)));

if (filemtime("data.json") <= (time() - 86400 * 7)) {
    $dataUrl = 'https://raw.githubusercontent.com/carloscuesta/gitmoji/master/src/data/gitmojis.json';
    $gitmojis = $w->request($dataUrl);
    if (isset(json_decode($gitmojis)->gitmojis)) {
        file_put_contents("data.json", $gitmojis);
    }
}


function setResult($gitmojis) {
    global $w;
    foreach ($gitmojis as $key => $value) {
        $id = $value->name;
        $emoji = $value->emoji;
        $title = $emoji." ".$value->description;
        $subTitle = "Copy ".$id." to clipboard";
        $w->result($id, $emoji, $title, $subTitle, ' ');
    }
}


function filter($var) {
    global $query;
    $description = strtolower($var->description);
    $name = strtolower($var->name);
    return strpos($description,$query) !== false || strpos($name,$query) !== false;
}

$gitmojis = json_decode(file_get_contents('data.json'))->gitmojis;
$data = $gitmojis;
if (strlen($query) != 0) {
    $data = array_filter($gitmojis, "filter");
}

setResult($data);
echo $w->toxml();