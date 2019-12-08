<?php
/**
 * MODX Manager language file
 *
 * @version 1.2
 * @date 2016/11/24
 * @author The MODX Project Team
 *
 * @language Russian
 * @package modx
 * @subpackage manager
 *
 * Please commit your language changes on Transifex (https://www.transifex.com/projects/p/modx-evolution/) or on GitHub (https://github.com/modxcms/evolution).
 */
//$modx_textdir = 'rtl'; // uncomment this line for RTL languages
$modx_lang_attribute = 'ru'; // Manager HTML/XML Language Attribute see http://en.wikipedia.org/wiki/ISO_639-1
$modx_manager_charset = 'UTF-8';

$_lang["blang_title"] = 'Мультиязычность';
$_lang["wok"] = 'Словарь';
$_lang["paramDefault"] = 'Добавить стандартние параметры';
$_lang["tmplvars_tab"] = 'Вклкадка';
$_lang["sync"] = 'Обновить тв параметры';
$_lang["update"] = 'Обновить';
$_lang["tab_params"] = 'Параметры';
$_lang["tab_settings"] = 'Настройки';
$_lang["sort"] = 'Сортировать';
$_lang["sort_title"] = 'Порядок сортировки параметров';
$_lang["empty_category"] = 'Без ккатегории';


$_lang["remove_record"] = 'Удалить записи';
$_lang["remove_record_answer"] = 'Вы уверенны что хотите удалить следующие записи ';
$_lang["remove_record_empty"] = 'Нечего удалять';
$_lang["remove_record_error"] = 'Ошибка';

$_lang["translate_all_record_empty"] = 'Нечего удалять';
$_lang["translate_all_record_error"] = 'Ошибка';
$_lang["translate_all_record"] = 'Перевести записи';
$_lang["translate_all_record_answer"] = 'Вы уверенны что хотите перевести следующие записи ';


$_lang["settings_languages"] = 'Языки';
$_lang["settings_yandexKey"] = 'Ключ для yandex api';
$_lang["settings_save"] = 'Сохранить';
$_lang["settings_fields"] = 'Поля для перевода (pagetitle,longtitle)<br> или 1 если все';
$_lang["settings_translate"] = 'Автоперевод (1/0)';
$_lang["settings_clientPrefix"] = 'Префикс модуля clientSettings';
$_lang["settings_translate_provider"] = 'Сервис для перевода';
$_lang["settings_default"] = 'Главный язык';
$_lang["settings_roots"] = 'Префиксы языков';
$_lang["settings_menu_controller_fields"] = 'Список полей, которые нужно подгрузить в контроллере lang_menu';
$_lang["settings_content_controller_fields"] = 'Список полей, которые нужно подгрузить в контроллере lang_content';
$_lang["settings_suffix"] = 'Суффиксы для полей ресурсов (_ru,__ua)';
$_lang["settings_autoFields"] = 'Автоматичиски подставлять поля в $modx->documentObject';
$_lang["settings_autoUrl"] = 'Автоматичиски подставлять префикс (_root) для урлов';

$_lang["settings_btn_save"] = 'Сохранить';
$_lang["settings_btn_cancel"] = 'Отмена';
$_lang["settings_pb_show_btn"] = 'Показовать кнопку для автоперевода Pagebuilder';

$_lang["settings_pb_is_te3"] = 'На сайте установлен Template Edit 3';
$_lang["settings_pb_config"] = 'Конфигурация PageBuilder';
$_lang["settings_pb_config_description"] = '( Название главного контейнера==язык контейнера,дочерний контейнер1==язык контейнера) <br>
<b>people_ru==ru,people_en==en,people_ua==ua||ru=ru,en==en,ua==ua</b>
';

$_lang["settings_default_to_new_tab"] = 'Переносить основные поля на новую вкладку';


$_lang["settings_yes"] = 'Да';
$_lang["settings_no"] = 'Нет';



$_lang["removeLang"] = 'Удалить языковие TV параметры';
$_lang["remove"] = 'Удалить';
$_lang["lang_caption"] = 'Язык';

$_lang["add_row"] = 'Добавить';
$_lang["name_not_unique"] = 'Имя уже используется';

$_lang["header_name"] = 'Название';
$_lang["header_tyltip"] = 'Подсказка';

$_lang["new_tmplvars"] = 'Новый тв параметр';

$_lang["params_new"] = 'Новый параметр';
$_lang["params_action_save"] = 'Сохранить';
$_lang["params_action_save_and_close"] = 'Сохранить и закрыть';
$_lang["params_action_copy"] = 'Сделать копию';
$_lang["params_action_remove"] = 'Удалить';
$_lang["params_action_cancel"] = 'Отмена';
$_lang["params_action_all"] = 'Добавить ко всем шаблонам';

$_lang["params_name"] = 'Имя параметра';
$_lang["params_caption"] = 'Заголовок';
$_lang["params_tab"] = 'Вкладка';
$_lang["params_description"] = 'Описание';
$_lang["params_category"] = 'Существующие категории';
$_lang["params_new_category"] = 'Новая категория';
$_lang["params_type"] = 'Тип ввода';
$_lang["params_elements"] = 'Возможные значения';
$_lang["params_elements_description"] = 'Это поле поддерживает привязку данных с использованием @-команд';
$_lang["params_default_text"] = 'Значение по умолчанию';
$_lang["params_default_text_description"] = 'Это поле поддерживает привязку данных с использованием @-команд';
$_lang["params_rank"] = 'Порядок в списке';
$_lang["params_template_caption"] = 'Укажите шаблоны, которые могут использовать этот Параметр ';
$_lang["params_check_all"] = 'Включить все';
$_lang["params_check_none"] = 'Выключить все';
$_lang["params_check_toggle"] = 'Переключить';
$_lang["params_not_unique"] = 'Объект Параметр  с именем [+name+] уже существует. Пожайлуста, введите другое имя.';
$_lang["params_action_save_and_new"] = 'Сохранить и создать новый';

$_lang["params_tab"] = 'Нзавание вкладки';
$_lang["params_multitv_translate_fields"] = 'Поля которые нужно автоматичиски переводить (для multitv)';
$_lang["params_bLang"] = 'bLang';





