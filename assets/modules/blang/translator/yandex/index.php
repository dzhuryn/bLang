<?php

use bLang\bLang;

require_once 'src/Exception.php';
require_once 'src/Translation.php';
require_once 'src/Translator.php';

use Yandex\Translate\Translator;
use Yandex\Translate\Exception;


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

function bLangTranslate($string,$from,$to,$bLang){
    /** @var $bLang bLang */
    $settings = $bLang->getSettings();


    $from = yandexLangPrepare($from,$settings['yadexn_lang_key']);
    $to = yandexLangPrepare($to,$settings['yadexn_lang_key']);

    $translator = new Translator($settings['yandexKey']);
    $translation = $translator->translate($string, $from.'-'.$to);


    return (string) $translation;


}