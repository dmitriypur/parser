<?php

$proxyN = 0;
$error = false;
$postDelimiter = '.';
$_SESSION['timestamp'] = date('d.m.Y_H:i:s');
$proxy = file('proxy.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$proxyCount = count($proxy);

function debug($data)
{
    echo '<pre>' . print_r($data, 1) . '</pre>';
}
function getDomain($link)
{
    $clearLink = str_replace(array('http://', 'https://'), '', $link);
    $clearLink = explode('/', $clearLink)[0];
    return $clearLink;
}

function write($group, $search, $type, $stuff, $url, $point, $region = '')
{
    $file = fopen('data/result-' . $region . '_' . $_SESSION['timestamp'] . '.csv', 'a+');
    $line = $group . ';' . $search . ';' . $type . ';' . $stuff . ';' . $url . ';' . $point;
    $line = str_replace(array("\r", "\n"), "", $line);
    fwrite($file, iconv('UTF-8', 'Windows-1251', $line) . PHP_EOL);
    fclose($file);
}

function curlGetPage($url, $proxyN, $proxy)
{

    $ip = $proxy[$proxyN];
//    echo $ip;

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_PROXY, $ip);
   curl_setopt($ch, CURLOPT_PROXYTYPE, 'HTTP');
    curl_setopt($ch, CURLOPT_PROXYPORT, '59100');
   curl_setopt($ch, CURLOPT_PROXYUSERPWD, 'pavloffpaf:gUhTCebysh');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 10);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_FRESH_CONNECT, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML,like Gecko) Chrome/27.0.1453.94 Safari/537.36");
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

    $response = curl_exec($ch);
    sleep(1);
    curl_close($ch);
    return $response;
}


//page-url: https://yandex.ru/showcaptcha?cc=1&retpath=https%3A//yandex.ru/search%3Ftext%3D%25D0%25B0%25D0%25B2%25D1%2582%25D0%25BE%25D1%2581%25D0%25B5%25D1%2580%25D0%25B2%25D0%25B8%25D1%2581%26lr%3D46%26suggest_reqid%3D622886877164759034303625723367976_437699240d9b797642e8d1f135a49ca4&t=2/1647590527/f024f0c09b3034eac005a864fb085cba&u=eea2c3f0-3fa54099-1e89d6df-641a1389&s=67dcda120e2bcc5bdfb316e1e6f22861