<?php

use bLang\bLang;

require_once MODX_BASE_PATH . 'assets/snippets/DocLister/core/controller/site_content_menu.php';


class lang_menuDocLister extends site_content_menuDocLister
{

    /** @var $bLang bLang */
    private $bLang;

    public function __construct(DocumentParser $modx, array $cfg = array(), $startTime = null)
    {
        $this->bLang = bLang::GetInstance($modx);
        $fields = $this->bLang->getSettings('menu_controller_fields');
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

        parent::__construct($modx, $cfg, $startTime);
    }

    protected function makeUrl($data = array())
    {
        if (intval($this->bLang->getSettings('autoUrl')) === 1) {
            return parent::makeUrl($data);
        } else {
            return $this->bLang->getLangUrl(parent::makeUrl($data));
        }
    }

    public function getDocs($tvlist = '')
    {
        $levels = parent::getDocs($tvlist);
        $fields = $this->bLang->getSettings('menu_controller_fields');
        if (!empty($fields)) {
            $fields = explode(',', $fields);
            $tvPrefix = $this->getCFGDef('tvPrefix', 'tv.');
            $tvs = $this->docTvs;

            foreach ($levels as $levelKey => $level) {
                foreach ($level as $docKey => $doc) {
                    foreach ($fields as $field) {

                        $fieldFull = $field . $this->bLang->suffix;
                        if (!$this->bLang->isDefaultField($fieldFull)) {
                            $doc[$field] = $tvs[$docKey][$tvPrefix . $fieldFull];
                        }

                    }
                    $levels[$levelKey][$docKey] = $doc;
                }
            }
        }

        $this->levels = $levels;
        return $levels;
    }
}
