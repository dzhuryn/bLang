<?php namespace bLang;
use Helpers\Lexicon\AbstractLexiconHandler;


$AbstractLexiconHandlerFile = MODX_BASE_PATH.'assets/snippets/FormLister/lib/LexiconHandlers/AbstractLexiconHandler.php';
if(file_exists($AbstractLexiconHandlerFile)){
require_once $AbstractLexiconHandlerFile;

/**
 * Class EvoBabelLexiconHandler
 */
class bLangLexiconHandler extends AbstractLexiconHandler
{

    /**
     * @param $key
     * @param string $default
     * @return string
     */
    public function get ($key, $default = '')
    {

        $bLang = bLang::GetInstance($this->modx);


        $out = $bLang->getLexicon($key);

        if (empty($out)) {
            $out = $default;
        }


        return $out;
    }
}

}