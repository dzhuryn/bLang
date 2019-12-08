<?php
namespace bLang;


class bLangModule
{
    /** @var $modx \DocumentParser */
    private $modx;
    private $bLang;
    public $_lang;

    public $modulePath;
    public $moduleurl;


    private function loadModuleLang()
    {
        $manager_language = $this->modx->getConfig('manager_language');

        $file = MODX_BASE_PATH . 'assets/modules/blang/module/lang/' . $manager_language . '.inc.php';

        if (!file_exists($file)) {
            $file = MODX_BASE_PATH . 'assets/modules/blang/module/lang/russian-UTF8.inc.php';
        }
        require $file;
        $this->_lang =  $_lang;


    }

    /**
     * bLangModule constructor.
     * @param $modx
     * @param $bLang bLang
     * @param $modulePath
     * @param $moduleurl
     */
    public function __construct($modx, $modulePath, $moduleurl)
    {
        $this->modx = $modx;
        $this->bLang = bLang::GetInstance($modx);
        $this->modulePath = $modulePath;
        $this->moduleurl = $moduleurl;

        $this->loadModuleLang();;

    }
    public function getModuleLang(){
        return $this->_lang;
    }

    public function createColumn($languages)
    {
        if(empty($languages)) return false;
        $table = $this->modx->getFullTableName('blang');
        $data = $this->modx->db->makeArray($this->modx->db->query("DESCRIBE $table"));

        $columns = array_column($data,"Field");



        $languages = explode('||',$languages);
        foreach ($languages as $lang) {
            if(!in_array($lang,$columns)){
                $eLang = $this->modx->db->escape($lang);
                $this->modx->db->query("ALTER TABLE $table ADD `$eLang` VARCHAR(1000) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT '' ");
            }
        }
        return true;
    }



    public function createDefaultParams()
    {
        $BT  = $this->modx->getFullTableName('blang_tmplvars');
        $params = json_decode(file_get_contents($this->modulePath . 'actions/default_fields.json'), true);


        if (is_array($params)) {
            foreach ($params as $fields) {


                foreach ($fields as $key => $val) {
                    $fields[$key] = $this->modx->db->escape($val);
                }
                $name = $this->modx->db->escape($fields['name']);
                $category = $fields['category'];

                $categoryId = $this->checkCategory($category);
                if (!$categoryId) {
                    $categoryId = $this->newCategory($category);
                }
                $fields['category'] = $categoryId;

                $id = $this->modx->db->getValue("select id from " . $BT . " where name = '" . $name . "'");
                if (empty($id)) {
                    $id = $this->modx->db->insert($fields, $BT);


                    if(!empty($_GET['template']) &&$_GET['template'] == 'all'){
                        $this->modx->db->delete($this->modx->getFullTableName('blang_tmplvar_templates'),'tmplvarid = '.$id);
                        $templates = $this->modx->db->makeArray($this->modx->db->select('*',$this->modx->getFullTableName('site_templates')));
                        foreach ($templates as $template) {

                            $this->modx->db->insert([
                                'tmplvarid'=>$id,
                                'templateid'=>$template['id']
                            ],$this->modx->getFullTableName('blang_tmplvar_templates'));
                        }
                    }

                }

            }
        }


    }


    function prepareField($value, $lang){
        return  str_replace(['[lang]','[suffix]'], [$lang, $this->bLang->suffixes[$lang]], $value);
    }
    function prepareFields($fields, $lang)
    {

        $prepareFields = [];
        foreach ($fields as $key => $val) {
            $prepareFields[$key] = $this->prepareField($val,$lang);
        }
        unset($prepareFields['id']);
        return $prepareFields;
    }




    public  function checkCategory($newCat = '')
    {
        $modx = evolutionCMS();
        $newCat = $modx->db->escape($newCat);
        $cats = $modx->db->select('id', $modx->getFullTableName('categories'), "category='{$newCat}'");
        if ($cat = $modx->db->getValue($cats)) {
            return (int)$cat;
        }

        return 0;
    }
    public function newCategory($newCat)
    {
        $modx = evolutionCMS();
        $useTable = $modx->getFullTableName('categories');
        $categoryId = $modx->db->insert(
            array(
                'category' => $modx->db->escape($newCat),
            ), $useTable);
        if (!$categoryId) {
            $categoryId = 0;
        }

        return $categoryId;
    }


    private function getFieldTabConfig(){
        $template = $id =0;
        if(empty($_GET['id'])){
            if(isset($_REQUEST['newtemplate'])) {
                $template = $_REQUEST['newtemplate'];
            } else {
                if(function_exists('getDefaultTemplate')){
                    $template = getDefaultTemplate();
                }
            }
        }
        else{
            $id = $_GET['id'];
            $pageInfo = $this->modx->getPageInfo($id,1,'parent,template');
            if(empty($pageInfo)) { $pageInfo = $this->modx->getPageInfo($id,0,'parent,template'); }
            $template = $pageInfo['template'];
        }

        //получаем имена tv полей которые доступные данному шаблону
        $TV = $this->modx->getFullTableName('site_tmplvars');
        $TT = $this->modx->getFullTableName('site_tmplvar_templates');
        $currentTemplateFields = $this->modx->db->getColumn('name',$this->modx->db->query("
           select tv.name from $TV as tv,$TT as tt where tv.id=tt.tmplvarid and tt.templateid = ".intval($template)."
        "));



        $BT = $this->modx->getFullTableName('blang_tmplvars');
        $bLangTemplateVars = $this->modx->db->makeArray($this->modx->db->query(
            "select * from $BT order by `rank` asc "
        ));


        $settings = [];

        $default_to_new_tab = $this->bLang->getSettings('default_to_new_tab');


        foreach ($bLangTemplateVars as $item) {

            foreach ($this->bLang->languages as $lang) {
                $suffix = $this->bLang->suffixes[$lang];


                $fieldFull = $item['name'].$suffix;
                $tab = $this->prepareField($item['tab'],$lang);

                $tabResponse = explode('.',$tab);
                $tabName = $tabResponse[0];
                $tabSection = $tabResponse[1];

                if($item['tab'] == '[lang]' && empty($suffix) && $default_to_new_tab != 1){
                  continue;
                }
                if(!in_array($fieldFull,$currentTemplateFields) && $this->bLang->isDefaultField($fieldFull) == false){
                    continue;
                }


                if(empty($tabSection)){
                    $settings[$tabName]['fields'][] = $fieldFull;
                }
                else{
                    $settings[$tabName]['section_'.$tabSection][] = $fieldFull;
                }


            }
        }
        return $settings;
    }
    public function te3AddAfter($tab,$config){
        $settings = $this->getTE3Rules();

        $number = false;
        $index = 1;
        foreach ($config as $key => $item) {
            if($key == $tab){
                $number = $index;
            }
            $index++;
        }


        $res = array_slice($config, 0, $number, true) +
            $settings +
            array_slice($config, $number, count($config) - 1, true) ;


        return $res;


    }
    public function getTE3Rules()
    {
        $TV = $this->modx->getFullTableName('site_tmplvars');
        $tmplvars = $this->modx->db->makeArray($this->modx->db->query("
           select tv.name, tv.type from $TV as tv "
        ));
        $tmplvars = array_column($tmplvars,'type','name');

        $settings = $this->getFieldTabConfig();
        $config = [];

        foreach ($settings as $tabName => $setting) {

            $config[$tabName] = [
                'title'=>$tabName
            ];


            if (key($setting) == "fields") {
                foreach ($setting['fields'] as $field) {
                    $tvType = $tmplvars[$field];
                    $tvData = [];
                    if($tvType == 'richtext'){
                        $tvData['position'] = 'c';
                    }
                    $config[$tabName]['fields'][$field] = $tvData;
                }
            } else {
                $sectionIndex = 0;
                foreach ($setting as $key => $item) {
                    $sectionName = str_replace('section_','',$key);
                    $sectionFields = [];
                    foreach ($item as $sectionItem) {
                        $sectionFields[$sectionItem] = [];
                    }
                    $config[$tabName]['col:'.$sectionIndex.':12'] = [
                        'settings'=>[
                            'title'=>'<div class="sectionHeader mb-3">'.$sectionName.'</div>'
                        ],
                        'fields:0'=>$sectionFields

                    ];
                    $sectionIndex++;

                }


            }


        }
        return $config;
    }
    public function getMMRules()
    {

        $settings = $this->getFieldTabConfig();

        $registerTab = [];
        foreach ($settings as $tabName => $setting) {
            $tabId = 'bLang_'.$tabName;
            if(empty($registerTab[$tabName])){
                mm_createTab($tabName,$tabId);
                $registerTab[$tabName] = true;
            }
            if(!empty($setting['fields'])){
                foreach ($setting['fields'] as $field) {
                    mm_moveFieldsToTab($field,$tabId);
                }
            }

            foreach ($setting as $key => $item) {

                if(strpos($key,'section_') === 0){
                    $sectionName = str_replace('section_','',$key);
                    $sectionId = $tabId.'_section_'.$sectionName;
                    mm_ddCreateSection($sectionName,$sectionId,$tabId);
                    foreach ($item as $sectionItem) {


                        mm_ddMoveFieldsToSection($sectionItem,$sectionId);
                    }
                }
            }

        }
    }


}