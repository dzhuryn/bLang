<div id="tab-page1" class="tab-page [+action+]-page" >
    <ul class="actionButtons">
        <li><a href="[+moduleurl+]action=paramForm">[+_new_tmplvars+]</a></li>

        <!--
        <li><a href="[+moduleurl+]action=updateTV" >[+_update+]</a></li>

        -->
        <li><a href="javascript:;" onclick="paramDefault()" >[+_paramDefault+]</a> </li>
    </ul>
    [+renderAlert+]
    <ul id="blang_tmplvars">[+paramGroups+]</ul>

    <script>
        var moduleurl = '[+moduleurl+]';

        function paramDefault() {
            var action  = moduleurl+'action=createDefaultParams';
            if (confirm('Are you sure you want to save this thing into the database?')) {
                action+= "&template=all"
            }
            window.location = action;

        }
    </script>
</div>