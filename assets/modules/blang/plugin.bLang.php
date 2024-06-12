<?php
use bLang\bLang;
use bLang\bLangInstall;
use bLang\translate;


$e = $modx->event;

require_once MODX_BASE_PATH . 'assets/modules/blang/classes/bLang.php';

$bLangInstall = new bLangInstall($modx);

if (bLang::$isInit === false && in_array($e->name, ['OnMakeDocUrl'])) {
    return false;
}
 $bLang = bLang::GetInstance($modx);

$bLangTranslate = new translate($modx, $bLang);
$settings = $bLang->getSettings();

switch ($e->name) {
    case 'OnWebPageInit':

        $bLang->setClientSettingFields();
        break;
    case 'OnMakeDocUrl':
        if (intval($settings['autoUrl']) !== 1) {
            return true;
        }
        $url = $params['url'];
        if (bLang::$firstPageMakeUrl === true) {
            bLang::$firstPageMakeUrl = false;
            return true;
        }

        $url = $bLang->getLangUrl($url);
        $e->setOutput($url);

        break;
    case 'OnMakePageCacheKey':
        // wait OnMakePageCacheKey
        break;
    case 'OnLoadDocumentObject':
        //    wait OnLoadDocumentObject
        break;
    case 'OnLoadWebPageCache':
        // wait OnLoadWebPageCache
        break;
    case 'OnAfterLoadDocumentObject':
        if (intval($settings['autoFields']) !== 1) {
            return true;
        }
        $docObj = $e->params['documentObject'];

        $lang = $bLang->lang;
        $suffix = $bLang->suffixes[$lang];

        $fields = $modx->db->makeArray($modx->db->query("select * from " . $modx->getFullTableName('blang_tmplvars')));


        foreach ($fields as $field) {
            $fieldName = $field['name'];
            $fieldFull = $fieldName . $suffix;

            if(empty($docObj[$fieldFull])){
                continue;
            }

            $fieldValue = is_array($docObj[$fieldFull]) ? $docObj[$fieldFull][1] : $docObj[$fieldFull];

            if (is_array($docObj[$fieldName])) {
                $docObj[$fieldName][1] = $fieldValue;
            } else {
                $docObj[$fieldName] = $fieldValue;
            }
        }
        $e->setOutput($docObj);
        break;
    case 'OnBeforeClientSettingsSave':
        $bLangTranslate->translateClientSettings($params['fields']);
        break;
    case 'OnDocFormTemplateRender':
    case 'OnDocFormRender':
        if($settings['pb_show_btn'] != 1){
            return true;
        }

        $render = DLTemplate::getInstance($modx);
        $template = '@CODE:'.file_get_contents( MODX_BASE_PATH.'assets/modules/blang/module/templates/pageBuilderButton.tpl');
        $button = $render->parseChunk($template,[]);

        if ($settings['pb_is_te3'] == 1 && $e->name == 'OnDocFormTemplateRender') {
            $modx->event->addOutput($button);
        }
         if($settings['pb_is_te3'] != 1 && $e->name == 'OnDocFormRender'){
            echo $button;
        }


        break;
    case 'OnDocFormSave':
        $docId = $params['id'];
        if (!empty($docId)  and $settings['translate']) {
            $bLangTranslate->translateDoc($docId);
        };

        break;
}