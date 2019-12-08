<?php
namespace bLang;

class bLangInstall
{

    private $version = 1.4;
    private $installVersionFile = '';

    /** @var $modx \DocumentParser */
    protected $modx;

    public function __construct($modx)
    {
        $this->modx = $modx;
        $this->installVersionFile =  MODX_BASE_PATH . 'assets/modules/blang/version.php';

        //получаем версию
        $currentVersion = 0;
        if (file_exists($this->installVersionFile)) {
             require $this->installVersionFile;

        }

        if($currentVersion>=$this->version){
            return true;
        }

        $this->firstInstall();

        file_put_contents($this->installVersionFile,'<?php'."\n".'$currentVersion = '.$this->version.';');

    }

    private function firstInstall()
    {
        $table = $this->modx->getFullTableName('blang');
        $sql = <<< OUT
        CREATE TABLE IF NOT EXISTS {$table} (
          `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
          `name` varchar(30) NOT NULL DEFAULT '',
          `title` varchar(255) NOT NULL DEFAULT '',
          `ru` varchar(1000) NOT NULL DEFAULT '',
          `ua` varchar(1000) NOT NULL DEFAULT '',
          `en` varchar(1000) NOT NULL DEFAULT '',
          PRIMARY KEY (`id`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
OUT;
        $this->modx->db->query($sql);


        $table = $this->modx->getFullTableName('blang_settings');
        $sql = <<< OUT
        CREATE TABLE IF NOT EXISTS {$table} (
          `name` varchar(50) NOT NULL DEFAULT '',
          `value` text,
          PRIMARY KEY (`name`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8
OUT;
        $this->modx->db->query($sql);

        $settingsRecordCount = $this->modx->db->getRecordCount($this->modx->db->select('*',$table));

        //пишем дефолтный конфиг
        if (empty($settingsRecordCount)) {
            $records = [
                [ 'name' => 'yadexn_lang_key', 'value' => 'ua==uk'],
                [ 'name' => 'translate_provider', 'value' => 'yandex'],
                [ 'name' => 'fields', 'value' => ''],
                [ 'name' => 'translate', 'value' => '0'],
                [ 'name' => 'yandexKey', 'value' => ''],
                [ 'name' => 'default', 'value' => 'en'],
                [ 'name' => 'menu_controller_fields', 'value' => 'pagetitle,menutitle'],
                [ 'name' => 'content_controller_fields', 'value' => 'pagetitle,menutitle,introtext,longtitle,description'],
                [ 'name' => 'lang_key', 'value' => 'ua==uk'],
                [ 'name' => 'suffixes', 'value' => 'ru==_ru||en=='],
                [ 'name' => 'languages', 'value' => 'ru||en'],
                [ 'name' => 'clientSettingsPrefix', 'value' => 'client_'],
                [ 'name' => 'autoFields', 'value' => '1'],
                [ 'name' => 'autoUrl', 'value' => '1'],
            ];
            foreach ($records as $record) {
                $this->modx->db->insert($record,$table);
            }
        }

        $table = $this->modx->getFullTableName('blang_tmplvar_templates');
        $sql = <<< OUT
        CREATE TABLE IF NOT EXISTS {$table} (
          `tmplvarid` int(10) NOT NULL DEFAULT '0' COMMENT 'Template Variable id',
          `templateid` int(11) NOT NULL DEFAULT '0',
          `rank` int(11) NOT NULL DEFAULT '0',
          PRIMARY KEY (`tmplvarid`,`templateid`)
        ) ENGINE=MyISAM DEFAULT CHARSET=utf8
OUT;
        $this->modx->db->query($sql);

        $table = $this->modx->getFullTableName('blang_tmplvars');
        $sql = <<< OUT
        CREATE TABLE IF NOT EXISTS {$table} (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `type` varchar(50) NOT NULL DEFAULT '',
          `name` varchar(50) NOT NULL DEFAULT '',
          `caption` varchar(80) NOT NULL DEFAULT '',
          `description` varchar(255) NOT NULL DEFAULT '',
          `editor_type` int(11) NOT NULL DEFAULT '0' COMMENT '0-plain text,1-rich text,2-code editor',
          `category` int(11) NOT NULL DEFAULT '0' COMMENT 'category id',
          `locked` tinyint(4) NOT NULL DEFAULT '0',
          `elements` text,
          `rank` int(11) NOT NULL DEFAULT '0',
          `display` varchar(20) NOT NULL DEFAULT '' COMMENT 'Display Control',
          `display_params` text COMMENT 'Display Control Properties',
          `default_text` text,
          `multitv_translate_fields` varchar(255) DEFAULT NULL,
          `tab` varchar(50) DEFAULT NULL,
          PRIMARY KEY (`id`),
          KEY `indx_rank` (`rank`)
        ) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
OUT;
        $this->modx->db->query($sql);
    }
}


