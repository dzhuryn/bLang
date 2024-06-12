<?php
namespace bLang;

class params
{
    /** @var $modx \DocumentParser */
    private $modx;
    /** @var $tpl \DLTemplate */
    private $tpl;
    /** @var $bLangModuleObj bLangModule */
    private $bLangModuleObj;
    /** @var $bLang \bLang\bLang */
    private $bLang;
    private $BT;
    private $BTT;

    private  $data;
    
    public function __construct($modx,$bLangModuleObj,$bLang,$tpl)
    {
        $this->modx = $modx;




        $this->bLangModuleObj = $bLangModuleObj;
        $this->bLang = $bLang;
        $this->tpl = $tpl;

        //название таблиц
        $this->BT = $modx->getFullTableName('blang_tmplvars');
        $this->BTT = $modx->getFullTableName('blang_tmplvar_templates');

        $this->data = [
            'moduleurl' => $this->bLangModuleObj->moduleurl,
            'manager_theme' => $modx->config['manager_theme'],
            'action' => isset($_GET['action'])?$_GET['action']:'home',
            'stay.'.$_SESSION['stay'] => 'selected',
            'selected' => [isset($_GET['action'])?$_GET['action']:'home' => 'selected'],
			'csrf' => csrf_token()
        ];
        foreach ($this->bLangModuleObj->_lang as $key => $value) {
            $this->data['_'.$key] = $value;
        }




    }
    
    public function getAndProcessParamForm(){

        $this->checkUpdatePossibilityInAction();


        $resourceData = [];
        $resourceTemplates  = [];
        if(!empty($_GET['param'])){
            $resourceData = $this->modx->db->getRow($this->modx->db->select('*',$this->BT,'id = '.intval($_GET['param'])));
            $resourceTemplates = $this->modx->db->getColumn('templateid',$this->modx->db->select('*',$this->BTT,'tmplvarid = '.intval($_GET['param'])));
        }

        $errors = [];
        if(!empty($_POST)){

            $stay = $_POST['stay'];
            $_SESSION['stay'] = $stay;

            $formData = $_POST;
            $templates = !empty($formData['template']) && is_array($formData['template'])?$formData['template']:[];
            $nextAction = $formData['action'];
            unset($formData['action']);
            unset($formData['stay']);
            unset($formData['template']);

            $formType =  'edit';
            if(empty($formData['id'])){
                unset($formData['id']);
                $formType = 'new';
            }
            $formData['category'] = $formData['categoryid'];
            unset($formData['categoryid']);

            if(empty($formData['name'])){
                $formData['name'] = 'Untitled variable';
            }
            if(empty($formData['caption'])){
                $formData['caption'] = 'Untitled variable';
            }
            $formData = $this->modx->db->escape($formData);

            //проверяем уникальность
            $uniqueWhere = '`name` = "'.$formData['name'].'"';
            if(!empty($formData['id'])){
                $uniqueWhere .= ' AND id != '.$formData['id'];
            }
            $checkUnique = $this->modx->db->getValue($this->modx->db->select('id',$this->BT,$uniqueWhere));
            if(!empty($checkUnique)){
                $errors[] = str_replace('[+name+]',$formData['name'],$this->data['_params_not_unique']);
            }

            if (empty($errors)) {
                //проверяем не перейменовали мы название tv поля
                if(!empty($formData['id'])){
                    $oldName = $this->modx->db->getValue($this->modx->db->select('name',$this->BT,'id = '.$formData['id']));

                    //изменилось название
                   if($oldName != $formData['name']){

                       $this->renameModxTV($oldName,$formData['name']);

                   }
                }

                //проверяем новую категорию
                if (!empty($formData['newcategory'])) {
                    $checkCategory = $this->bLangModuleObj->checkCategory($formData['newcategory']);

                    if (empty($checkCategory)) {
                        $formData['category'] = $this->bLangModuleObj->newCategory($formData['newcategory']);
                    } else {
                        $formData['category'] = $checkCategory;
                    }
                }
                unset($formData['newcategory']);

                $formData['rank'] = intval($formData['rank']);
                $formData['category'] = intval($formData['category']);
                if (!empty($formData['id'])) {
                    $this->modx->db->update($formData, $this->BT, 'id = ' . $formData['id']);
                    $paramId = $formData['id'];
                } else {
                    $paramId = $this->modx->db->insert($formData, $this->BT);
                }


                //удаляем шаблоны
                $this->modx->db->delete($this->BTT, 'tmplvarid = ' . $paramId);
                foreach ($templates as $templateId) {
                    $this->modx->db->insert([
                        'tmplvarid' => $paramId,
                        'templateid' => $templateId,
                        'rank' => 0
                    ], $this->BTT);
                }

                $resourceData = $formData;
                $resourceTemplates = $templates;

                $this->updateTV();
                if ($stay == 1) {
                    $this->modx->sendRedirect($this->bLangModuleObj->moduleurl . 'action=paramForm');
                }
                if ($stay == '') {
                    $this->modx->sendRedirect($this->bLangModuleObj->moduleurl . 'action=params');
                }
                if ($formType == 'new') {
                    $this->modx->sendRedirect($this->bLangModuleObj->moduleurl . 'action=paramForm&param=' . $paramId);
                }
            } else {
                $resourceData = $_POST;
                $resourceTemplates = $_POST['template'];
            }



        }

        //стандартные параметры
        $tvTypes = [
            'text'=>'Text',
            'rawtext'=>'Raw Text (deprecated)',
            'textarea'=>'Textarea',
            'rawtextarea'=>'Raw Textarea (deprecated)',
            'textareamini'=>'Textarea (Mini)',
            'richtext'=>'RichText',
            'dropdown'=>'DropDown List Menu',
            'listbox'=>'Listbox (Single-Select)',
            'listbox-multiple'=>'Listbox (Multi-Select)',
            'option'=>'Radio Options',
            'checkbox'=>'Check Box',
            'image'=>'Image',
            'file'=>'File',
            'url'=>'URL',
            'email'=>'Email',
            'number'=>'Number',
            'date'=>'Date',
        ];
        foreach ($tvTypes as $type => $typeCaption) {
            $this->data['standardTVType'] .= $this->tpl->parseChunk('@CODE:<option value="[+type+]" [+selected+]>[+caption+]</option>',[
                'type'=>$type,
                'caption'=>$typeCaption,
                'selected'=>!empty($resourceData['type']) && $resourceData['type'] == $type?'selected':'',
            ]);
        }
        //custom tv
        $custom_tvs = scandir(MODX_BASE_PATH . 'assets/tvs');
        $customTVS = ['custom_tv'=>'Custom Input'];
        foreach($custom_tvs as $ctv) {
            if(strpos($ctv, '.') !== 0 && $ctv != 'index.html') {
                $type = 'custom_tv:' . $ctv;
                $customTVS['custom_tv:' . $ctv] = $ctv;
            }
        }
        foreach ($customTVS as $type => $typeCaption) {
            $this->data['customTVType'] .= $this->tpl->parseChunk('@CODE:<option value="[+type+]" [+selected+]>[+caption+]</option>',[
                'type'=>$type,
                'caption'=>$typeCaption,
                'selected'=>!empty($resourceData['type']) && $resourceData['type'] == $type?'selected':'',
            ]);
        }

        $modxCategories = $this->modx->db->makeArray($this->modx->db->select('*',$this->modx->getFullTableName('categories')),'id');

        foreach ($modxCategories as $category) {
            $this->data['categories'] .= $this->tpl->parseChunk('@CODE:<option value="[+id+]" [+selected+]>[+caption+]</option>',[
                'id'=>$category['id'],
                'caption'=>$category['category'],
                'selected'=>!empty($resourceData['category']) && $resourceData['category'] == $category['id']?'selected':'',
            ]);
        }
        //генерим список шаблонов
        $templates = $this->modx->db->makeArray($this->modx->db->select('*',$this->modx->getFullTableName('site_templates')));

        $templateCategory = [];
        foreach ($templates as $template) {
            $templateCategory[$template['category']][] = $template;
        }
        $this->data['templates'] = '';

        foreach ($templateCategory as $categoryId => $templates) {
            $wrap = '';
            foreach ($templates as $template) {
                $wrap .= $this->tpl->parseChunk('@CODE:<li><label><input name="template[]" value="[+id+]" type="checkbox" [+checked+]> [+name+]&nbsp;<small>([+id+])</small> [+description+] </label></li>', [
                    'id' => $template['id'],
                    'name' => $template['templatename'],
                    'description' => !empty($template['description']) ? ' - ' . $template['description'] : '',
                    'checked' => !empty($resourceTemplates) && in_array($template['id'], $resourceTemplates) ? 'checked' : '',
                ]);
            }

            $this->data['templates'] .= $this->tpl->parseChunk('@CODE:<li><strong>[+categoryName+]</strong><ul>[+wrap+]</ul></li>',[
                'wrap'=>$wrap,
                'categoryName'=>!empty($modxCategories[$categoryId])?$modxCategories[$categoryId]['category']:$this->data['_empty_category']
            ]);
        }

        if(!empty($errors)){
            foreach ($errors as $error) {
                $this->data['errors'] .= $this->tpl->parseChunk("@CODE:alert('[+error+]');\n",['error'=>$error]);
            }
        }
        return $this->tpl->parseChunk('@FILE:params/paramForm', array_merge($this->data,$resourceData),true);
    }

    public function getParamsList()
    {

        $checkUpdatePossibility = $this->checkUpdatePossibility();
        $this->data['renderAlert'] = $this->renderPossibilityAlert($checkUpdatePossibility);

        if (empty($checkUpdatePossibility['errors'])) {
            $modxCcategories = $this->modx->db->makeArray($this->modx->db->select('*',$this->modx->getFullTableName('categories')),'id');
            //получаем список параметорв
            $bLangTmplvars = $this->modx->db->makeArray($this->modx->db->query(
                "select * from $this->BT order by `category` asc, `id` asc"
            ));
            $categories = [];
            foreach ($bLangTmplvars as $el) {
                $categories[$el['category']][] = $el;
            }
            $this->data['paramGroups'] = '';
            foreach ($categories as $categoryId => $tmplvars) {
                $wrap = '';
                foreach ($tmplvars as $tmplvar) {
                    $wrap .= $this->tpl->parseChunk('@FILE:params/param',array_merge($tmplvar,['moduleurl'=>$this->bLangModuleObj->moduleurl]));
                }
                $this->data['paramGroups']  .= $this->tpl->parseChunk('@FILE:params/paramGroup',[
                    'id'=>$categoryId,
                    'wrap'=>$wrap,
                    'name'=>!empty($modxCcategories[$categoryId])?$modxCcategories[$categoryId]['category']:$this->data['_empty_category']
                ]);
            }
        }


        return $this->tpl->parseChunk('@FILE:params/params', $this->data,true);

    }

    public function deleteParam()
    {
        $paramId = intval($_GET['param']);
        $this->modx->db->delete($this->BT,'id = '.$paramId);
        $this->modx->db->delete($this->BTT,'tmplvarid = '.$paramId);
        $this->modx->sendRedirect($this->bLangModuleObj->moduleurl.'action=params');
    }

    public function updateTV()
    {
        $ST = $this->modx->getFullTableName('site_tmplvars');
        $STT = $this->modx->getFullTableName('site_tmplvar_templates');

        $languages = $this->bLang->languages;
        $suffixes = $this->bLang->suffixes;

        $bLangParams = $this->modx->db->makeArray($this->modx->db->select('*',$this->BT));

        foreach ($bLangParams as $param) {

            foreach ($languages as $lang) {
                $tvName = $param['name'].$suffixes[$lang];
                if($this->bLang->isDefaultField($tvName)){
                    continue;
                }

              //  echo $tvName.'<br>';

                $tvId = $this->modx->db->getValue($this->modx->db->select('id',$ST,'name = "'.$this->modx->db->escape($tvName).'"'));
                $tvData = array_merge($param,['name'=>$tvName]);
                $tvData = $this->bLangModuleObj->prepareFields($tvData,$lang);


                $data = [];
                $originalFields = ['type', 'name', 'caption', 'description', 'editor_type', 'category', 'locked', 'elements', 'rank', 'display', 'display_params', 'default_text',];

                foreach ($originalFields as $fieldName) {
                    $data[$fieldName] = isset($tvData[$fieldName])?$tvData[$fieldName]:'';
                }


                if(empty($tvId)){
                    $tvId =  $this->modx->db->insert($data,$ST);
                }
                else{
                    $this->modx->db->update($data,$ST,'id = '.intval($tvId));
                }


                $this->modx->db->delete($STT,'tmplvarid = '.intval($tvId));

                $bLangTemplates = $this->modx->db->makeArray($this->modx->db->select('*',$this->BTT,'tmplvarid = '.intval($param['id'])));
                foreach ($bLangTemplates as $template) {
                    $this->modx->db->insert([
                        'tmplvarid'=>intval($tvId),
                        'templateid'=>intval($template['templateid'])
                    ],$STT);

                }
            }
        }
      //$this->modx->sendRedirect($this->bLangModuleObj->moduleurl.'action=params');
    }

    private function checkUpdatePossibility()
    {


        $templateTable = $this->modx->getFullTableName('site_templates');
        $templates = $this->modx->db->makeArray($this->modx->db->select('*',$templateTable));

        $languages = $this->bLang->languages;

        $errors = [];
        $warning = [];
        $addTVS = [];
        foreach ($templates as $template) {

                        //получаем имена tv полей которые доступные данному шаблону
            $TV = $this->modx->getFullTableName('site_tmplvars');
            $TT = $this->modx->getFullTableName('site_tmplvar_templates');

            $currentTemplateFields = $this->modx->db->getColumn('name',$this->modx->db->query("
               select tv.name from $TV as tv,$TT as tt where tv.id=tt.tmplvarid and tt.templateid = ".intval($template['id'])."
            "));


            //получаем все bLang tv
            $allBLangTVResponse = $this->modx->db->makeArray($this->modx->db->select('*',$this->BT));

            //получаем bLang tv привязанные к текущем шаблону
            $bLangThisTemplateFieldsResponse =  $this->modx->db->makeArray($this->modx->db->query("
               select bt.name from $this->BT as bt,$this->BTT as btt where bt.id=btt.tmplvarid and btt.templateid = ".intval($template['id'])."
            "));
            $bLangThisTemplateFieldsResponseNames = array_column($bLangThisTemplateFieldsResponse,'name');
            $bLangTVNoChecked  = [];
            foreach ($allBLangTVResponse as $item) {
                if(!in_array($item['name'],$bLangThisTemplateFieldsResponseNames)){
                    $bLangTVNoChecked[] = $item['name'];
                }
            }

            foreach ($allBLangTVResponse as $item) {

                $tvs = [];
                foreach ($languages as $language) {
                    $suffix = $this->bLang->suffixes[$language];
                    $fullName = $item['name'] . $suffix;
                    if ($this->bLang->isDefaultField($fullName)) {
                        continue;
                    }
                    $tvs[] = $fullName;
                }

                $isFieldTiedToCurrentTemplate = in_array($item['name'],$bLangThisTemplateFieldsResponseNames);

                foreach ($tvs as $tv) {
                    //если тв привязана к шаблону modx но поле не привязане к шаблону внутри bLang

                    if (in_array($tv, $currentTemplateFields) && $isFieldTiedToCurrentTemplate === false) {
                        $addTVS[$template['id']][$item['id']] = $item['id'];

                        if (!isset($errors[$template['templatename']])) {
                            $errors[$template['templatename']] = [];
                        }
                        $errors[$template['templatename']][] = $tv;
                    }

                    //если тв не привязана к шаблону modx но поле привязане к шаблону внутри bLang
                    if (!in_array($tv, $currentTemplateFields) && $isFieldTiedToCurrentTemplate === true) {
                    if(!isset($warning[$template['templatename']])){
                        $warning[$template['templatename']]  = [];
                    }

                        $warning[$template['templatename']][] = $tv;

                    }
                }
            }

        }

        return [
            'warning'=>$warning,
            'errors'=>$errors,
            'addTVS'=>$addTVS
        ];

    }

    private function renderPossibilityAlert($alertGroups)
    {
        $render = \DLTemplate::getInstance($this->modx);

        $output = '';
        if(!empty($alertGroups['errors'])){
            $errorsOutput = '';
            foreach ($alertGroups['errors'] as $templateName => $errors) {
                $errorsOutput .= $render->parseChunk('@CODE:<h4><b>Шаблон</b> - [+template+], tv - [+tvs+]</h4>',[
                    'template'=>$templateName,'tvs'=>implode(',',$errors)]);
            }
            $output .= $render->parseChunk('@CODE:<div class="alert alert-danger" role="alert"><h3>TV которые не должны быть отмечены у шаблонов</h3>[+wrap+]<a href="[+moduleurl+]action=fixparams">Исправить</a></div>',['wrap'=>$errorsOutput,'moduleurl'=>$this->bLangModuleObj->moduleurl]);
        }

        if(!empty($alertGroups['warning'])){
            $warningOutput = '';
            foreach ($alertGroups['warning'] as $templateName => $errors) {
                $warningOutput .= $render->parseChunk('@CODE:<h4><b>Шаблон</b> - [+template+], tv - [+tvs+]</h4>',[
                    'template'=>$templateName,'tvs'=>implode(',',$errors)]);
            }
            $output .= $render->parseChunk('@CODE:<div class="alert alert-warning" role="alert"><h3>TV которые должны быть отмечены у шаблонов</h3>[+wrap+]<p>После сохранения, они автоматически привяжутся</p></p></div>',['wrap'=>$warningOutput,'moduleurl'=>$this->bLangModuleObj->moduleurl]);
        }



        return $output;
    }

    public function fixParams()
    {
        $errorCheck = $this->checkUpdatePossibility();

        if(!empty($errorCheck['addTVS'])){
            foreach ($errorCheck['addTVS'] as $templateId => $bLangFields) {

                foreach ($bLangFields as $bLangFieldId) {
                    $this->modx->db->insert([
                        'tmplvarid'=>$bLangFieldId,
                        'templateid'=>$templateId,
                    ],$this->BTT);
                }

            }
        }
        $this->modx->sendRedirect($this->bLangModuleObj->moduleurl.'action=params');
    }

    public function checkUpdatePossibilityInAction()
    {
        $render = \DLTemplate::getInstance($this->modx);
        $checkUpdatePossibility = $this->checkUpdatePossibility();

        if(!empty($checkUpdatePossibility['errors'])){
            $output = $render->parseChunk('@FILE:params/error',[
                'moduleurl'=>$this->bLangModuleObj->moduleurl
            ]);
            echo $output;
            die();
        }

    }

    public function duplicateParam($paramId)
    {
        $this->checkUpdatePossibilityInAction();

        if(empty($paramId)){
            echo 'empty id';
            return;
        }

        $fields = $this->modx->db->getRow($this->modx->db->select('*',$this->BT,'id = '.$paramId));

        $oldParamId = $fields['id'];
        unset($fields['id']);

        //получаем список шаблонов
        $templates = $this->modx->db->getColumn('templateid',$this->modx->db->select('*',$this->BTT,'tmplvarid = '.$oldParamId));


        $rand = rand(100,999);
        $fields['caption'] .= ' copy '.$rand;
        $fields['name'] .= ' copy '.$rand;
        $newParamId = $this->modx->db->insert($fields,$this->BT);


        foreach ($templates as $templateId) {
            $this->modx->db->insert([
                'tmplvarid'=>$newParamId,
                'templateid'=>$templateId,
            ],$this->BTT);
        }

        $this->updateTV();
         $this->modx->sendRedirect($this->bLangModuleObj->moduleurl.'action=paramForm&param='.$newParamId);



        return '';

    }

    private function renameModxTV($oldName, $newName)
    {
        $T  = $this->modx->getFullTableName('site_tmplvars');

        $languages = $this->bLang->languages;
        foreach ($languages as $language) {
            $suffix = $this->bLang->suffixes[$language];
            $oldFullName = $oldName.$suffix;
            $newFullName = $newName.$suffix;

            $fields = [
                'name'=>$newFullName,
            ];
            $fields = $this->modx->db->escape($fields);

            $this->modx->db->update($fields,$T,"`name` = '".$this->modx->db->escape($oldFullName)."'");
        }

    }
}