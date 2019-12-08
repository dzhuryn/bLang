<?php

use bLang\bLang;

include_once(MODX_BASE_PATH . 'assets/snippets/DocLister/core/controller/site_content.php');

class lang_contentDocLister extends site_contentDocLister
{
    /** @var $bLang bLang */
    private $bLang;

    public function __construct(DocumentParser $modx, array $cfg = array(), $startTime = null)
    {


        $this->bLang = bLang::GetInstance($modx);

        $fields = !empty($cfg['langFields'])?$cfg['langFields']:$this->bLang->getSettings('content_controller_fields');
        if (!empty($fields)) {
            $fields = explode(',', $fields);
            $addTV = [];
            foreach ($fields as $field) {
                $fieldFull = $field . $this->bLang->suffix;
                if ($this->bLang->isDefaultField($fieldFull)) {
                    continue;
                }
                $addTV[] = $fieldFull;
            }
            if (!empty($addTV)) {
                $addTV = implode(',', $addTV);
                $cfg['tvList'] = empty($cfg['tvList']) ? $addTV : $cfg['tvList'] . ',' . $addTV;
            }
        }

        if (intval($this->bLang->getSettings('autoUrl')) !== 1) {
            $cfg['makeUrl'] = 0;
        }

        parent::__construct($modx, $cfg, $startTime);
    }

    public function getDocs($tvlist = '')
    {


        $docs = parent::getDocs($tvlist);
        $fields = $this->getCFGDef('langFields',$this->bLang->getSettings('content_controller_fields'));



        $suffix = $this->bLang->suffix;

        if (!empty($fields)) {
            $fields = explode(',', $fields);
            $tvPrefix = $this->getCFGDef('tvPrefix', 'tv.');
            foreach ($docs as $key => $doc) {
                foreach ($fields as $field) {
                    $fieldFull = $field . $this->bLang->suffix;


                    if (!$this->bLang->isDefaultField($fieldFull)) {
                        $docs[$key][$field] = $doc[$tvPrefix . $fieldFull];
                    }
                }
            }
        }

        foreach ($docs as $key => $doc) {
            if (intval($this->bLang->getSettings('autoUrl')) !== 1) {
                $docs[$key]['url'] = $this->bLang->getLangUrl($this->modx->makeUrl($key));
            }
        }


        $this->_docs = $docs;
        return $docs;
    }
}
