<div id="tab-page1" class="tab-page [+action+]-page" style="display:block;">

   <div id="vocabulary"></div>
    <div class="bottom-actions">
        <a id="Button4" class="btn btn-success add-item" href="javascript:;">
            <span>Добавить</span>
        </a>
    </div>

    <script>
       var vocabulary =  webix.ui({
            container: "vocabulary",
           view: "datatable",
           id: "data",
           footer:true,
           columns: [
              { id: "id", editor: "text", name: "id", header: "id", width:50,css:"id-cell-group" },


              { id: "name", editor: "text", name: "name", header:["[+_header_name+]",{content:"selectFilter"}],   width:200 },
              { id: "title", editor: "text", name: "title",   header:["[+_header_tyltip+]",{content:"selectFilter"}] },
              [+lang_columns+]
              {
                 id: "id",
                 header: ["<center><a style='margin-top:10px ' class='deleteButton btnSmall webix_icon fa-close' onclick='removeAll()'></a></center>"],
                 footer: ["<center><a style='margin-top:10px ' class='deleteButton btnSmall webix_icon fa-close' onclick='removeAll()'></a></center>"],
                 template: "<center><a class='deleteButton btnSmall webix_icon fa-close remove-item' data-id='#id#'  ></a></center>",
                 width: 50
              },
              {
                 id: "id",
                 header: ["<a class='webix_icon fa-globe' onclick='translateAll()'></a>"],
                 footer: ["<a class='webix_icon fa-globe' onclick='translateAll()'></a>"],
                 template: "<a class='webix_icon fa-globe' onclick='my_translate(#id#)'></a>",
                 width: 50
              }

           ],
           editable: true,
           autoheight: true,
           select: true,
           multiselect:true,

           url: "[+moduleurl+]action=getVocabulary",
           save: "[+moduleurl+]action=saveVocabulary"
        });

        $(document).on('click','.add-item',function () {
            vocabulary.add({})
        });
       $(document).on('click','.remove-item',function () {
           var id = $(this).data('id');
           vocabulary.remove(id)
       });



       webix.dp($$("data")).attachEvent('onAfterSaveError', function (id, status, response, details) {
           webix.alert("Произошла ошибка");


       });

       $$("data").attachEvent("onDataUpdate", function(id, data, old){
           $$("data").addRowCss(id, "wait-save");

       });
       //
       webix.dp($$("data")).attachEvent("onAfterSave", function(response, id, object){



           if(typeof response !== 'object' || response === null){
               webix.alert("Произошла ошибка");
               return false;
           }

           if(response.status !== true){
               webix.alert("Произошла ошибка");
                return  false;
           }

           if(response.newid !== undefined){
               id = response.newid
           }


           $$("data").removeRowCss(id, "wait-save");

       });

       function my_translate(id) {
           var dataTable = $$("data");
           var record = dataTable.getItem(id);
           $.ajax({
               url:'[+moduleurl+]action=translate&staticElements=off',
               data:record,
               type:'POST',
               success:function (elem) {

                   if(typeof elem !== 'object'){
                       webix.alert("Произошла ошибка, не удалось обработать ответ");
                       return false;
                   }
                   dataTable.updateItem(id, elem);
               },
               error:function (jqXHR, textStatus, errorThrown) {
                   webix.alert("Произошла ошибка - "+errorThrown);
               }
           })

       }

       function translateAll() {
           var selected =  $$('data').getSelectedItem();
           if(selected === undefined){
               webix.alert({
                   title: "[+_translate_all_record_empty+]",// the text of the box header
                   text: "[+_translate_all_record_error+]",
               });
               return ;
           }
           var checked = [];
           if(selected.length === undefined){
               checked.push(selected.id);
           }
           else{
               selected.forEach(function (el) {
                   checked.push(el.id)
               })
           }


           webix.confirm({
               title: "[+_translate_all_record+]",// the text of the box header
               text: "[+_translate_all_record_answer+]"+checked.join(','),
               callback: function(result) {
                   if (result) {
                       checked.forEach(function (id) {
                           my_translate(id);
                       })
                   }
               }
           });


       }

       function removeAll()	{

           var selected =  $$('data').getSelectedItem();
           if(selected === undefined){
               webix.alert({
                   title: "[+_remove_record_error+]",// the text of the box header
                   text: "[+_remove_record_empty+]",
               });
               return ;
           }
           var checked = [];
           if(selected.length === undefined){
               checked.push(selected.id);
           }
           else{
               selected.forEach(function (el) {
                   checked.push(el.id)
               })
           }


           webix.confirm({
               title: "[+_remove_record+]",// the text of the box header
               text: "[+_remove_record_answer+]"+checked.join(','),
               callback: function(result) {
                   if (result) {
                       checked.forEach(function (id) {
                           $$("data").remove(id);
                       })
                   }
               }
           });
       }



    </script>
</div>