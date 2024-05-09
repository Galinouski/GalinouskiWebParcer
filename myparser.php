<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>My Parser (D. Galinouski)</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            justify-content: center;
            align-items: center;
            padding: 20px;
            background: #f5ca8f;
        }
    </style>
    <script type="text/javascript" src="./aes.js"></script>
    <script type="text/javascript" src="./require.js"></script>

</head>
<body>

<div>
<?php

use DiDom\Document;
use DiDom\Query;

require('vendor/autoload.php');

$document = new Document('https://www.wiki-prom.ru/navigator.html', true);

try {
    $directories = $document->find('.rub-dir');
}
    catch (\DiDom\Exceptions\InvalidSelectorException $e) {
}

foreach( $directories as $directory) {
    if ($directory->text() == "Машиностроение и металлообработка") {
        echo "<h1>".$directory->text()."</h1>";
        break;
    }
}

$sub_dir_count = $document->find('.subdir-list')[2]->first('ul')->count('li');

//for ( $i=0; $i<$sub_dir_count; $i++) {
//    //echo $document->find('.subdir-list')[2]->first('ul')->find('li')[$i]->text(); // вывод на экран всех аодкатегорий
//    //echo "<br>";
//}

for ( $i=0; $i<$sub_dir_count; $i++) {  // вывести всю информацию
//for ( $i=0; $i<4; $i++) {

    $sub_dir_href = $document->find('.subdir-list')[2]->first('ul')->find('li>a')[$i]->getAttribute('href');
    $document2 = new Document($sub_dir_href, true);
    $main_title = $document2->find('h1')[0]->text();

    $factories = $document2->find('.com-list-cnt');
    echo "<br>";
        foreach( $factories as $factory) {

            echo "<b>".$main_title."; </b>";
            echo "<b>".$factory->find('.title>a')[0]->text()."; </b>";
            echo $factory->find('.com-list-prod>span')[0]->text();

            $factory_href = $factory->find('.title>a')[0]->getAttribute('href');

            $document3 = new Document($factory_href, true);

            try {
                $inn = $document3->find('.opn-inf-box>ul')[0]->find('li')[3]->text();
            } catch (Error $e) {
                continue;
            }
            if($inn != " "){
                echo " " . $inn. "; ";
            }


            //$count_info_box = count($document3->find('.cnt-box>span'));
            $information = $document3->find('.cnt-box');
            foreach ( $information as $info_box){
                $info = $info_box->find('span')[0]->text();
                $info = str_replace('показать email', '', $info);
                if($info != " "){
                    echo " ".$info."; ";
                }
            }


            $pattern4 = '/\'...........................................\=\'|\'.......................\=\'|\'......................\=\=\'/';
            preg_match_all($pattern4, $document3, $matches);
            $adr_arr = $matches[0];

            foreach($adr_arr as $mach)
            {
                ?>

                <span class="">e-mail: <span class="email" id="switch" content=<?= $mach?>>идёт дешифровка...</span>; </span>

                <?php
            }

            echo "<br><br>";

        }

}

?>
</div>

<script>

    function get_mails()
    {
        Object.values(document.getElementsByClassName('email')).forEach(load_eadr);
    }

    function load_eadr(e)
    {
        let key = '59b6ab46d379b89d794c87b74a511fbd59b6ab46d379b89d794c87b74a511fbd';
        let iv = '0aaff094b6dc29742cc98a4bac8bc8f9';
        let mach = [];
        mach = e.getAttribute('content');

        let adr_arr = [];
        adr_arr = mach;
        {
            let decrypted = CryptoJS.AES.decrypt(adr_arr, CryptoJS.enc.Hex.parse(key), {iv: CryptoJS.enc.Hex.parse(iv)});
            e.innerHTML = decrypted.toString(CryptoJS.enc.Utf8);
            console.log(decrypted.toString(CryptoJS.enc.Utf8));
        }
    }
    get_mails();

</script>


</body>
</html>




