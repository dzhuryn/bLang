<div class="tab-page" id="tabGeneral" style="display: block;">
    <!--
    <h1>
        <i class="fa fa-list-alt"></i>
        [[if? &is=`[+id+]:empty` &separator=`:` &then=`[+_params_new+]` &else=`[+name+]`  ]]
    </h1>

    -->
    <div class="container container-body">
        <form action="[+moduleurl+]action=paramForm[[if? &is=`[+id+]:!empty` &then=`&param=[+id+]`]]" id="form" method="post">


            <div id="actions">

                <div class="btn-group">

                    <div class="btn-group">
                        <a id="Button1" class="btn btn-success" href="javascript:;"  onclick="actions.save()">
                            <i class="fa fa-floppy-o"></i><span>Сохранить</span>
                        </a>
                        <span class="btn btn-success plus dropdown-toggle"></span>
                        <select id="stay" name="stay">
                            <option id="stay1" value="1" [+stay.1+]>Создать новый</option>
                            <option id="stay2" value="2" [+stay.2+]>Продолжить</option>
                            <option id="stay3" value="" [+stay.+]>Закрыть</option>
                        </select>
                    </div>



                    <a id="Button6" class="btn btn-secondary [[if? &is=`[+id+]:empty` &then=`disabled`]]" href="javascript:;" onclick="actions.duplicate();">
                        <i class="fa fa-clone"></i><span>[+_params_action_copy+]</span>
                    </a>
                    <a id="Button3" class="btn btn-secondary [[if? &is=`[+id+]:empty` &then=`disabled`]]" href="javascript:;" onclick="actions.delete();">
                        <i class="fa fa-trash"></i><span>[+_params_action_remove+]</span>
                    </a>
                    <a id="Button5" class="btn btn-secondary" href="javascript:;" onclick="actions.cancel();">
                        <i class="fa fa-times-circle"></i><span>[+_params_action_cancel+]</span>
                    </a>

                </div>

            </div>



            <input type="hidden" name="id" value="[+id+]">
            
            <div class="row form-row">
                <label class="col-md-3 col-lg-2">[+_params_name+]</label>
                <div class="col-md-9 col-lg-10">
                    <div class="form-control-name clearfix">
                        <input name="name" type="text" maxlength="50" value="[+name+]" class="form-control form-control-lg"  />
                    </div>
                </div>
            </div>
            <div class="row form-row">
                <label class="col-md-3 col-lg-2">[+_params_caption+]</label>
                <div class="col-md-9 col-lg-10">
                    <input name="caption" type="text" maxlength="80" value="[+caption+]" class="form-control"  />
                </div>
            </div>

            <div class="row form-row">
                <label class="col-md-3 col-lg-2">[+_params_description+]</label>
                <div class="col-md-9 col-lg-10">
                    <input name="description" type="text" maxlength="255" value="[+description+]" class="form-control" >
                </div>
            </div>
            <div class="row form-row">
                <label class="col-md-3 col-lg-2">[+_params_category+]</label>
                <div class="col-md-9 col-lg-10">
                    <select name="categoryid" class="form-control" >
                        <option>&nbsp;</option>
                        [+categories+]
                    </select>
                </div>
            </div>
            <div class="row form-row">
                <label class="col-md-3 col-lg-2">[+_params_new_category+]</label>
                <div class="col-md-9 col-lg-10">
                    <input name="newcategory" type="text" maxlength="45" value="[+newcategory+]" class="form-control" >
                </div>
            </div>
            <div class="row form-row">
                <label class="col-md-3 col-lg-2">[+_params_type+]</label>
                <div class="col-md-9 col-lg-10">
                    <select name="type" size="1" class="form-control" >
                        <optgroup label="Standard Type">[+standardTVType+]</optgroup>
                        <optgroup label="Custom Type">[+customTVType+]</optgroup>
                    </select>
                </div>
            </div>
            <div class="row form-row">
                <label class="col-md-3 col-lg-2">[+_params_elements+]	<small class="form-text text-muted">[+_params_elements_description+]</small>
                </label>
                <div class="col-md-9 col-lg-10">
                    <textarea name="elements" maxlength="65535" rows="4" class="form-control" >[+elements+]</textarea>
                </div>
            </div>
            <div class="row form-row">
                <label class="col-md-3 col-lg-2">[+_params_default_text+]
                    <small class="form-text text-muted">[+_params_default_text_description+]</small>
                </label>
                <div class="col-md-9 col-lg-10">
                    <textarea name="default_text" class="form-control" rows="4" >[+default_text+]</textarea>
                </div>
            </div>
            <div class="row form-row">
                <label class="col-md-3 col-lg-2">[+_params_rank+]</label>
                <div class="col-md-9 col-lg-10">
                    <input name="rank" type="text" maxlength="4" size="1" value="[+rank+]" class="form-control"  />
                </div>
            </div>

            <h4><b>[+_params_bLang+]</b></h4>
            <div class="row form-row">
                <label class="col-md-3 col-lg-2">[+_params_tab+]</label>
                <div class="col-md-9 col-lg-10">
                    <input name="tab" type="text" maxlength="255" value="[+tab+]" class="form-control" >
                </div>
            </div>
            <!--
            <div class="row form-row">
                <label class="col-md-3 col-lg-2">[+_params_multitv_translate_fields+]</label>
                <div class="col-md-9 col-lg-10">
                    <input name="multitv_translate_fields" type="text" maxlength="255" value="[+multitv_translate_fields+]" class="form-control" >
                </div>
            </div>
-->
            <hr>
            <!--<b></b>-->
            <p>[+_params_template_caption+]</p>
            <div class="form-group">
                <a class="btn btn-secondary btn-sm" href="javascript:;" onClick="check_all();return false;">[+_params_check_all+]</a>
                <a class="btn btn-secondary btn-sm" href="javascript:;" onClick="check_none();return false;">[+_params_check_none+]</a>
                <a class="btn btn-secondary btn-sm" href="javascript:;" onClick="check_toggle(); return false;">[+_params_check_toggle+]</a>
            </div>
            <ul>
                [+templates+]
            </ul>


        </form>
    </div>


    <script type="text/javascript">
        if (!evo) {
            var evo = {};
        }
        var actionStay = [];
        evo.style = {
            actions_file: 'fa fa-file-o',
            actions_pencil: 'fa fa-pencil',
            actions_reply: 'fa fa-reply',
            actions_plus: 'fa fa-plus'
        };

        [+errors+]
        var id = '[+id+]';
        var moduleUrl = '[+moduleurl+]';


        var actionButtons = document.getElementById('actions'), actionSelect = document.getElementById('stay');
        if (actionButtons !== null && actionSelect !== null) {
            var actionPlus = actionButtons.querySelector('.plus'), actionSaveButton = actionButtons.querySelector('a#Button1') || actionButtons.querySelector('#Button1 > a');
            actionPlus.classList.add('dropdown-toggle');
            actionStay['stay1'] = '<i class="' + evo.style.actions_file + '"></i>';
            actionStay['stay2'] = '<i class="' + evo.style.actions_pencil + '"></i>';
            actionStay['stay3'] = '<i class="' + evo.style.actions_reply + '"></i>';
            if (actionSelect.value) {
                actionSaveButton.innerHTML += '<i class="' + evo.style.actions_plus + '"></i><span> + </span>' + actionStay['stay' + actionSelect.value] + '<span>' + actionSelect.children['stay' + actionSelect.value].innerHTML + '</span>';
            }
            var actionSelectNewOption = null, actionSelectOptions = actionSelect.children, div = document.createElement('div');
            div.className = 'dropdown-menu';
            actionSaveButton.parentNode.classList.add('dropdown');
            for (var i = 0; i < actionSelectOptions.length; i++) {
                if (!actionSelectOptions[i].selected) {
                    actionSelectNewOption = document.createElement('SPAN');
                    actionSelectNewOption.className = 'btn btn-block';
                    actionSelectNewOption.dataset.id = i;
                    actionSelectNewOption.innerHTML = actionStay[actionSelect.children[i].id] + ' <span>' + actionSelect.children[i].innerHTML + '</span>';
                    actionSelectNewOption.onclick = function() {
                        var s = actionSelect.querySelector('option[selected=selected]');
                        if (s) {
                            s.selected = false;
                        }
                        actionSelect.children[this.dataset.id].selected = true;
                        actionSaveButton.click();
                    };
                    div.appendChild(actionSelectNewOption);
                }
            }
            actionSaveButton.parentNode.appendChild(div);
            actionPlus.onclick = function() {
                this.parentNode.classList.toggle('show');
            };
        }


        function check_toggle() {
            var el = document.getElementsByName("template[]");
            var count = el.length;
            for(i = 0; i < count; i++) el[i].checked = !el[i].checked;
        };

        function check_none() {
            var el = document.getElementsByName("template[]");
            var count = el.length;
            for(i = 0; i < count; i++) el[i].checked = false;
        };

        function check_all() {
            var el = document.getElementsByName("template[]");
            var count = el.length;
            for(i = 0; i < count; i++) el[i].checked = true;
        };
        var actions = {
            save:function (action) {
                var form = $('#form');
                form.append('<input type="hidden" name="action" value="'+action+'" />')
                form.submit();
            },
            delete:function(){
                location.href = moduleUrl+'action=deleteParams&param='+id
            },
            duplicate:function () {
                location.href = moduleUrl+'action=duplicate&param='+id
            },
            cancel:function () {
                location.href = moduleUrl+'action=params'
            },
        };


    </script>
</div>