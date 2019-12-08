<?php
require_once MODX_BASE_PATH.'assets/snippets/DocLister/lib/DLTemplate.class.php';

use bLang\bLang;

$type = isset($type) ? $type : 'switch';
$bLang = bLang::GetInstance($modx);

$lang = $bLang->lang;
$suffix = $bLang->suffixes[$lang];



switch ($type) {
    case 'suffix':
        return $suffix;
        break;
    case 'DocInfo':
        echo $modx->runSnippet('DocInfo', ['docid' => $docid, 'field' => $field . $suffix]);
        break;
    case 'switch':

        if (isset($pl)) return '[+' . $pl . $suffix . '+]';
        if (isset($f)) return '[*' . $f . $suffix . '*]';
        if (isset($s)) return $modx->getConfig($s . $suffix);
        echo $$lang;
        break;
    case 'getTreeParams':
         // @EVAL  return $modx->runSnippet("bLang", [ 'type'=>'getTreeParams',  "parent"=>"6", 'firstEmpty'=>'1']);

        $paramKey = isset($paramKey)?$paramKey:'alias';
        $titleField = isset($titleField)?$titleField:'pagetitle';
        $titleFieldFull = $titleField.$suffix;

        $out = '';
        $firstEmpty = isset($firstEmpty) && (int)$firstEmpty == 0 ? false : true;
        $order = isset($order) && !empty($order) ? $order : "pagetitle ASC, menuindex ASC";
        if ($firstEmpty) {
            $out .= '||';
        }


        $R = $modx->getFullTableName('site_content');
        $TV = $modx->getFullTableName('site_tmplvars');
        $TVR = $modx->getFullTableName('site_tmplvar_contentvalues');



        if (isset($field) && isset($value)) {
            $where = " c.`" . $field . "`='" . $value . "'";
        }
        else{
            $where = " c.`parent` IN(" . $parent . ") ";
        }

        //поле дефолтное, тянем из siteContent Только
        if($bLang->isDefaultField($titleFieldFull)){
            $sql = "select $paramKey as keyField,$titleFieldFull as valueField from $R as `c` where $where";
            $data = $modx->db->makeArray($modx->db->query($sql));
        }
        else{
            $where .= " and tv.name = '$titleFieldFull'";
            $sql = "select $paramKey as keyField,tvr.`value` as valueField from $R as `c`
                    INNER JOIN $TVR as tvr on tvr.contentid = c.id 
                    INNER JOIN $TV as tv on tv.id = tvr.tmplvarid 
                    WHERE $where order by $order";
            $data = $modx->db->makeArray($modx->db->query($sql));
        }


        foreach ($data as $row) {
            $out .= $row['valueField'] . (strpos($_SERVER['REQUEST_URI'], MGR_DIR) !== FALSE ? ' (' . $row['keyField'] . ')' : '') . '==' . $row['keyField'] . '||';

        }
        $out = substr($out, 0, -2);
        echo $out;
        return;
        break;
    case 'list':
        include_once(MODX_BASE_PATH . 'assets/snippets/DocLister/lib/DLTemplate.class.php');
        $tpl = DLTemplate::getInstance($modx);
        //шаблоны
        $outerTpl = isset($outerTpl) ? $outerTpl : '@CODE:<div>[+list+]</div>';
        $activeTpl = isset($activeTpl) ? $activeTpl : '@CODE:<a class="active" href="[+url+]">[+title+]</a>';
        $listTpl = isset($listTpl) ? $listTpl : '@CODE:<ul>[+wrapper+]</ul>';
        $listRow = isset($listRow) ? $listRow : '@CODE:<li class="[+classes+]"><a href="[+url+]">[+title+]</a></li>';

        $languages = $bLang->languages;
        $activeLang = (string)$bLang->lang;


        $activeTitle = !empty($modx->getConfig('__' . $activeLang . '_title')) ? $modx->getConfig('__' . $activeLang . '_title') : $activeLang;
        $activeUrl = $modx->getConfig('_' . $activeLang . '_url');

        $active = $tpl->parseChunk($activeTpl, [
            'title' => $activeTitle,
            'url' => $activeUrl,
        ]);
        $listItems = '';
        foreach ($languages as $key => $lang) {
            $url = $modx->getConfig('_' . $lang . '_url');
            $title = !empty($modx->getConfig('_' . $lang . '_title')) ? $modx->getConfig('__' . $lang . '_title') : $lang;

            $class = ' lang-item';
            if ($lang == $activeLang) {
                $class .= ' active';
            }
            if ((count($languages) - 1) == $key) {
                $class .= ' last-lang-item';
            }
            $listItems .= $tpl->parseChunk($listRow, [
                'classes' => $class,
                'title' => $title,
                'url' => $url,
            ]);
        }
        $list = $tpl->parseChunk($listTpl, [
            'wrapper' => $listItems,
        ]);
        $outer = $tpl->parseChunk($outerTpl, [
            'active' => $active,
            'list' => $list,
        ]);
        echo $outer;
        break;
}
return;