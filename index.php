<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-type: text/html; charset=utf-8');
include_once 'phpQuery/phpQuery.php';
include_once 'functions.php';
//include_once 'google_table.php';
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="wrap">
    <div class="form-block">
        <div class="form-head">Поиск колдунщиков и агрегаторов</div>
        <form id="parser-form" method="post">
            <div class="form-group">
                <label for="lr">ID региона</label>
                <input type="number" name="lr" id="lr">
            </div>

            <div class="form-group">
                <label for="q">Запросы(каждый с новой строки. Формат группа.запрос)</label>
                <textarea name="q" id="q" cols="30" rows="10"></textarea>
            </div>
            <button type="submit">Проверить</button>
        </form>
    </div>

    <?php
    if (!empty($_POST)) {
        $region = trim($_POST["lr"]);
        $query = trim($_POST["q"]);

        $query = nl2br($query);
        $query = explode('<br />', $query);

        $inquiry = [];
        foreach ($query as $str) {
            $str = str_replace(array("\r", "\n"), "", $str);
            $str = explode('.', $str);
            $inquiry[] = $str;
        }
        if (!empty($_POST["q"]) && !$error) {
            write('Группа', 'Запрос', 'Тип', 'Название', 'URL страницы', 'Место в выдаче', $region);
        }

        foreach ($inquiry as $str) {
            $group = $str[1] ? $str[0] : '1';
            $search = $str[1] ?? $str[0];

            $url = 'https://www.yandex.ru/search/?text=' . urlencode($search) . '&lr=' . $region;
            $page = curlGetPage($url, $proxyN, $proxy);
            $html = phpQuery::newDocument($page);

            $r = pq('data-fast-name="images" li.serp-item .desktop-card')->text();
            debug($r);

//        if ($proxyN + 1 < $proxyCount) {
//            $proxyN++;
//        } else {
//            $proxyN = 0;
//        }
            $head = pq('head')->length;
            if (pq('.CheckboxCaptcha')->length || pq('.AdvancedCaptcha')->length) {
                $url_part = pq('form.CheckboxCaptcha-Form')->attr('action');
//                echo $url_part . '<br>';
                echo '<b>Captcha</b><br>';
                $error = true;
                if ($proxyN + 1 < $proxyCount) {
                    $proxyN++;
                } else {
                    $proxyN = 0;
                }

            } else if (!$head) {
                echo 'Пустая страница';
//                if ($proxyN + 1 < $proxyCount) {
//                    $proxyN++;
//                } else {
//                    $proxyN = 0;
//                }
            } else if (http_response_code() == 500) {
                echo '500';
                return json_encode(500);
//                if ($proxyN + 1 < $proxyCount) {
//                    $proxyN++;
//                } else {
//                    $proxyN = 0;
//                }
            } else {
                $error = false;
//
                if (pq('.showcase__item .card-map')->length) write($group, $search, 'Колдунщик', 'Карта организаций в шапке', '', '', $region);
                if (pq('.map2.geo-search')->length) write($group, $search, 'Колдунщик', 'Карта организаций справа', '', '', $region);
                if (pq('.showcase__item .topic')->length) write($group, $search, 'Колдунщик', 'Организации в шапке', '', '', $region);
                if (pq('ul#search-result li[data-fast-name="buy_tickets"]')->length) write($group, $search, 'Колдунщик', 'Яндекс.Путешествия', '', '', $region);
                if (pq('ul#search-result li[data-fast-name="entity/afisha"]')->length) write($group, $search, 'Колдунщик', 'Яндекс.Афиша', '', '', $region);
                if (pq('ul#search-result li[data-fast-name="videowiz"]')->length) write($group, $search, 'Колдунщик', 'Яндекс.Видео', '', '', $region);
                if (pq('ul#search-result li[data-fast-name="suggest_fact"]')->length) write($group, $search, 'Колдунщик', 'Факт', '', '', $region);
                if (pq('*[data-fast-name="images"]')->length) write($group, $search, 'Колдунщик', 'Яндекс.Картинки', '', '', $region);

                $list = [];
                $i = 0;
                $searchLinks = $html->find('ul#search-result li.serp-item a.organic__url');

                foreach ($searchLinks as $searchLink) {
                    $url = pq($searchLink)->attr('href');
                    $url = parse_url($url);
                    if ($url['host'] !== 'yabs.yandex.ru') {
                        $list[] = $searchLink;
                    }
                }


                foreach ($list as $k => $searchLink):
                    $i++;
                    $link = pq($searchLink)->attr('href');
//                $linkPos2 = pq($searchLink)->attr('accesskey');
                    $linkPos = $k + 1;
//                foreach($table_arr as $k => $table){
//                    if($k == 0){
//                        continue;
//                    }
//                    if($table[0] == 'В выдаче'){
//                        if (strpos($link, $table[2]) !== false){
//                            write($group, $search, $table[0], $table[1], getDomain($link), $linkPos, $region);
//                        }
//                    }
//                    else{
//                        if (pq($table[2])->length){
//                            write($group, $search, $table[0], $table[1], getDomain($link) ?? '', "Вес колдунщика: {$table[3]}", $region);
//                        }
//                    }
//                }

//
                echo 'код - ' . $group . '; ';
                echo 'запрос - ' . $search . '; ';
                echo 'ссылка - ' . getDomain($link) . '; ';
                echo 'позиция - ' . $linkPos . '; ';
                echo 'регион - ' . $region . '; ';
                echo '<br>';

                    if ($linkPos * 1 < 1) $linkPos = $i;
                    if (strpos($link, 'Avito.ru') !== false) write($group, $search, 'Из выдачи', 'Авито', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'market.yandex.ru') !== false) write($group, $search, 'Из выдачи', 'Яндекс маркет', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'eda.yandex.ru') !== false) write($group, $search, 'Из выдачи', 'Яндекс еда', getDomain($link), $linkPos, $region);
                    if (strpos($link, '2gis.ru') !== false) write($group, $search, 'Из выдачи', '2 Gis', getDomain($link), $linkPos, $region);
                    if (strpos($link, '.pulscen.ru') !== false) write($group, $search, 'Из выдачи', 'Пульс цен', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'tiu.ru') !== false) write($group, $search, 'Из выдачи', 'Tiu', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'otvet.mail.ru') !== false) write($group, $search, 'Из выдачи', 'Ответ mail.ru', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'auto.ru') !== false) write($group, $search, 'Из выдачи', 'Auto.ru', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'afisha.yandex.ru') !== false) write($group, $search, 'Из выдачи', 'Яндекс Афиша', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'zen.yandex.ru') !== false) write($group, $search, 'Из выдачи', 'Яндекс дзен', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'realty.yandex.ru') !== false) write($group, $search, 'Из выдачи', 'Яндекс.Недвижимость в выдаче', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'yandex.ru/collections') !== false) write($group, $search, 'Из выдачи', 'Яндекс.Избранное в выдаче', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'yandex.ru/maps') !== false) write($group, $search, 'Из выдачи', 'Яндекс.Карты в выдаче', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'uslugi.yandex.ru') !== false) write($group, $search, 'Из выдачи', 'Яндекс.Услуги в выдаче', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'dostavka.yandex.ru') !== false) write($group, $search, 'Из выдачи', 'Яндекс.Доставка', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'yandex.ru/q') !== false) write($group, $search, 'Из выдачи', 'Яндекс.Кью', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'vk.com') !== false) write($group, $search, 'Из выдачи', 'ВКонтакте', getDomain($link), $linkPos, $region);
                    if (strpos($link, 'youla.ru') !== false) write($group, $search, 'Из выдачи', 'Юла', getDomain($link), $linkPos, $region);
                endforeach;
            }
        }
        if (!empty($_POST["q"]) && !$error) {
            echo '<p>Готово. <a href="data/result-' . $region . '_' . $_SESSION['timestamp'] . '.csv">Скачать результат</a> </p>';
        }
    }
    ?>

</div>
</div>
</body>
</html>