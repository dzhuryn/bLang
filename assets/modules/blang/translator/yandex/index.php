<?php

use bLang\bLang;

require_once 'vendor/autoload.php';

use Panda\Yandex\TranslateSdk;

function yandexLangPrepare($lang,$langKeyResponse){
    $langKey = [];
    $langKeyResponse = explode('||',$langKeyResponse);

    foreach ($langKeyResponse as $item) {
        $itemResponse =explode('==',$item);

        $langKey[$itemResponse[0]] = $itemResponse[1];
    }


   if(!empty($langKey[$lang])){
       return $langKey[$lang];
   }
   return $lang;

}

function bLangTranslate($string, $from, $to, $bLang)
{
    try {
        $settings = $bLang->getSettings();
	if (empty($settings['yandexKey'])) return;
        $from = yandexLangPrepare($from,$settings['yadexn_lang_key']);
        $to = yandexLangPrepare($to,$settings['yadexn_lang_key']);
        $cloud = TranslateSdk\Cloud::createApi($settings['yandexKey']);
        $translate = new TranslateSdk\Translate($string, $from);
        $translate->setSourceLang($from)->setTargetLang($to);
        $translate->setFormat(TranslateSdk\Format::HTML);
        $result = json_decode($cloud->request($translate), true);
        if (!empty($result['translations'][0]['text'])) return $result['translations'][0]['text'];
    } catch (TranslateSdk\Exception\ClientException $e) {
        
    }

}
