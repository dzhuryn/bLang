<?php

namespace bLang;

use Yandex\Translate\Exception;

require_once MODX_BASE_PATH . 'assets/lib/MODxAPI/modResource.php';

class  translate
{

    /** @var $bLang bLang */
    private $bLang;
    /** @var $modx \DocumentParser */
    private $modx;

    public function __construct($modx, $bLang)
    {
        $this->modx = $modx;
        $this->bLang = $bLang;
        $this->init();;
    }


    private function init()
    {

        $settings = $this->bLang->getSettings();
        $provider = $settings['translate_provider'];

        require_once MODX_BASE_PATH . 'assets/modules/blang/translator/' . $provider . '/index.php';
    }

    public function translate($string, $from, $to)
    {

        try {
            return bLangTranslate($string, $from, $to, $this->bLang);
        } catch (Exception $e) {
            $this->modx->logEvent(143, 3, $e->getMessage(), 'bLang');
            return '';
        }

    }


    public function translateDoc($docId)
    {
        //получаем список полей
        $settings = $this->bLang->getSettings();
        $sql = "select `name`,`type` from " . $this->modx->getFullTableName('blang_tmplvars');

        if (intval($settings['fields']) !== 1) {
            $fields = explode(',', $settings['fields']);
            $sql .= "where `name` in ('" . implode("','", $this->modx->db->escape($fields)) . "')";
        }
        $translateFields = $this->modx->db->makeArray($this->modx->db->query($sql));


        $doc = new \modResource($this->modx);
        $doc->edit($docId);
        $docFields = $doc->toArray();


        foreach ($translateFields as $field) {
            $fieldName = $field['name'];

            $fieldType = $field['type'];


            if (in_array($fieldType, ['text', 'rawtext', 'textarea', 'rawtextarea', 'textareamini', 'richtext'])) {
                $docFields = $this->translateTextField($docFields, $fieldName,$fieldType);
            }
        }

        $docFields = $this->translateSingleMultiTVFields($docFields);

        $doc->fromArray($docFields);
        $doc->save(false, false);

        if(!empty($_POST['translatePageBuilder'])) {
            $this->translatePageBuilder($docId);
        }

    }

    private function translateTextField(array $docFields, $fieldName,$fieldType='')
    {
        $languages = $this->bLang->languages;
        $suffixes = $this->bLang->suffixes;
        $default = $this->bLang->defaultLang;

        if (($key = array_search($default, $languages)) !== false) {
            unset($languages[$key]);
        }
        array_unshift($languages, $default);


        $fromLang = $fromField = '';
        foreach ($languages as $lang) {
            $suffix = $suffixes[$lang];

            $fieldFullName = $fieldName . $suffix;

            if (!empty($docFields[$fieldFullName])) {
                $fromLang = $lang;
                $fromField = $fieldFullName;
                break;
            }
        }

        //не нашли поля для перевода
        if (empty($fromLang) || empty($fromField)) {
            return $docFields;
        }


        foreach ($languages as $lang) {
            if ($lang == $fromLang) {
                continue;
            }

            $suffix = $suffixes[$lang];
            $fieldFullName = $fieldName . $suffix;

            //если есть значение пропускаем
            if (!empty($docFields[$fieldFullName])) {
                continue;
            }
            $translateString = $docFields[$fromField];
            if($fieldType== 'richtext'){
                $translateString = strip_tags($translateString);
            }
            $translateResult = $this->translate($translateString, $fromLang, $lang);
            if($fieldType== 'richtext'){
                $translateResult = strip_tags($translateResult);
            }
            $docFields[$fieldFullName] = $translateResult;


        }


        return $docFields;
    }

    private function translateSingleMultiTVFields($docFields)
    {
        //ищем multitv
        $docTVS = $this->modx->getTemplateVars('*', '*', $docFields['id']);

        foreach ($docTVS as $docTV) {
            if ($docTV['type'] !== 'custom_tv:multitv') {
                continue;
            }

            $tvValue = json_decode($docTV['value'], true)['fieldValue'];
            if (empty($tvValue)) {
                continue;
            }

            //получаем конфигурацию

            $settings = [];
            $file = MODX_BASE_PATH . 'assets/tvs/multitv/configs/' . $docTV['name'] . '.config.inc.php';
            if (!file_exists($file)) {
                continue;
            }
            require $file;

            if (empty($settings['langFields'])) {
                continue;
            }

            foreach ($tvValue as $key => $item) {
                foreach ($settings['langFields'] as $langField) {

                    $response = $this->translateTextField($tvValue[$key], $langField);
                    if (!empty($response) && is_array($response)) {
                        $tvValue[$key] = $response;
                    }
                }
            }

            $docFields[$docTV['name']] = json_encode(['fieldValue' => $tvValue]);


        }
        return $docFields;
    }

    public function translateClientSettings(&$fields)
    {

        $translateFields = [];
        foreach (glob(MODX_BASE_PATH . 'assets/modules/clientsettings/config/*.php') as $file) {
            $config = include $file;
            if(empty($config['langFields'])){
                continue;
            }
            $translateFields = array_merge($translateFields, $config['langFields']);

        }
        foreach ($translateFields as $translateField) {

            $fieldValues = [];
            foreach ($this->bLang->languages as $lang) {
                $suffix = $this->bLang->suffixes[$lang];

                $fieldNameFull = $translateField . $suffix;
                $fieldValues[$fieldNameFull] = $fields[$fieldNameFull][1];
            }

            $result = $this->translateTextField($fieldValues, $translateField);

            foreach ($result as $fieldName => $fieldValue) {
                $fields[$fieldName][1] = $fieldValue;
            }
        }


        //$fields['field_text_ua'][1] = time();


    }

    private function translatePageBuilderFields($config, $values, $fromLang, $toLang)
    {

        foreach ($values as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $childKey => $child) {
                    $values[$key][$childKey] = $this->translatePageBuilderFields($config[$key] ["fields"], $child, $fromLang, $toLang);
                }

            } else {
                $fieldType = $config[$key]['type'];
                $translate = isset($config[$key]['translate']) ? $config[$key]['translate'] : true;

                if ($translate == true && in_array($fieldType, ['text', 'textarea', 'richtext'])) {
                    if($fieldType == 'richtext'){
                        $value = strip_tags($value);
                    }
                    $newValue = $this->translate($value, $fromLang, $toLang);

                 //   echo $value;
                 //   echo '<br><br>';

                    if($fieldType == 'richtext'){
                        $newValue = str_replace("\n",'<br>',$newValue);
                        $newValue = '<p>'.$newValue.'</p>';
                    }
                    $values[$key] = $newValue;
                }
            }
        }
        return $values;
    }

    private function translatePageBuilder($docId)
    {
        $pageBuilderConfig = [];
        $pageBuilderConfigString = $this->bLang->getSettings('pb_config');

        foreach (explode('||',$pageBuilderConfigString) as $groupKey => $group) {
            foreach (explode(',',$group) as $key=> $container) {
                $response = explode('==',$container);

                //основной контейер
                if($key == 0){
                    $pageBuilderConfig[$groupKey]['mainContainer'] = [
                        'name'=>$response[0],
                        'lang'=>$response[1],
                    ];
                }
                else{
                    $pageBuilderConfig[$groupKey]['containers'][] = [
                        'name'=>$response[0],
                        'lang'=>$response[1],
                    ];
                }

            }
        }

        $table = $this->modx->getFullTableName('pagebuilder');



        foreach ($pageBuilderConfig as $item) {

            $originalContainer = $item['mainContainer']['name'];
            $originalContainerLanguage = $item['mainContainer']['lang'];

            $containers = $item['containers'];

            $blocks = $this->modx->db->makeArray($this->modx->db->select('*', $table, '`document_id` = '.$docId.' and `container` = "'.$originalContainer.'"', '`index` ASC'));

            $path = MODX_BASE_PATH . 'assets/plugins/pagebuilder/config/';
            foreach ($containers as $container) {
                $containerName = $container['name'];
                $containerLanguage = $container['lang'];


                $this->modx->db->delete($table,'`document_id` = '.$docId.' and `container` = "'.$containerName.'"');

                foreach ($blocks as $block) {

                    $values = json_decode($block['values'], true);

                    $configFile = $path . $block['config'] . '.php';
                    if (!file_exists($configFile)) {
                        continue;
                    }
                    $config = require $configFile;
                    $fields = $config['fields'];




                    $response = $this->translatePageBuilderFields($fields, $values, $originalContainerLanguage, $containerLanguage);

                    $block['values'] = json_encode($response,JSON_UNESCAPED_UNICODE);
                    $block['container'] = $containerName;


                    unset($block['id']);

                    $block = $this->modx->db->escape($block);
                     $this->modx->db->insert($block,$table);


                }


            }




        }




    }


}
