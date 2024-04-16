<div id="tab-page1" class="tab-page [+action+]-page" style="display:block;">

    <div class="container">
        <div class="row">

            <form action="[+moduleurl+]action=settings" id="settings-form" class="col-md-8" method="post">
<input type="hidden" name="_token" value="[+csrf+]">
                <div id="actions">

                    <div class="btn-group">
                        <a id="Button1" class="btn btn-success" href="javascript:;" onclick="$('#settings-form').submit();">
                            <i class="fa fa-clone"></i><span>[+_settings_btn_save+]</span>
                        </a>
                        <a id="Button2" class="btn btn-secondary" href="javascript:;" onclick="location.reload()">
                            <i class="fa fa-clone"></i><span>[+_settings_btn_cancel+]</span>
                        </a>
                    </div>

                </div>

                [+fields+]



            </form>

        </div>
    </div>

</div>