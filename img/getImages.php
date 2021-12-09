<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

for($i=1;$i<64;$i++) {
    $url='https://via.placeholder.com/200/CCCCCC/000000/n'.$i.'?text='.$i;
    curl_setopt($ch, CURLOPT_URL, $url);
    file_put_contents('n'.$i.'.png',curl_exec($ch));
}
curl_close($ch);



