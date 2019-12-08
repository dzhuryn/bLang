<!doctype html>
<html lang="ru">
<head>
    <title>bLang</title>


    <link rel="stylesheet" type="text/css" href="media/style/[+manager_theme+]/style.css"/>


    <link type="text/css" rel="stylesheet" href="../assets/lib/webix/codebase/skins/compact.css">

    <script src="http://code.jquery.com/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>
    <script type="text/javascript" src="../assets/lib/webix/codebase/webix.js"></script>

    <style>
        .bottom-actions {
            text-align: right;
            margin-top: 10px;
        }

        .webix_cell.webix_row_select{
            background-color: #fff !important;
            color:rgb(102, 102, 102) !important;
        }

        .webix_first.webix_column> div.webix_cell.webix_row_select {
            color: #fff !important;
            background: #27ae60 !important;
        }

        .webix_cell.wait-save.webix_row_select {
            background-color: #ff000038 !important;
        }
    </style>
</head>
<body>

<h1>
    <i class="fa fa-file-text"></i>Менеджер языков
</h1>


<div class="sectionBody">
    <div id="modulePane" class="dynamic-tab-pane-control tab-pane">
        <div class="tab-row">
            <h2 class="tab [+selected.home+]"><a href="[+moduleurl+]action=home">Словарь</a></h2>

            <h2 class="tab [+selected.params+] [+selected.paramForm+] [+selected.params+]"><a href="[+moduleurl+]action=params">Параметры</a></h2>
            <h2 class="tab [+selected.settings+]"><a href="[+moduleurl+]action=settings">Настройки</a></h2>



        </div>

