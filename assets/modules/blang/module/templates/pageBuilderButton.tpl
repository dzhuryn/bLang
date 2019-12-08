<script >
    jQuery('#actions > .btn-group').append('<a id=\"ButtonTranslateBuilder\" class=\"btn btn-secondary\" href=\"javascript:;\"><i class=\"fa fa-clone\"></i><span>Перевести PB</span></a>');


    jQuery('#ButtonTranslateBuilder').click(function() {
        var jBtn = jQuery(this);
        var jText = jBtn.find('span');
        if(jBtn.hasClass('btn-active')){
            jQuery('#translatePageBuilder').remove();
            jBtn.removeClass('btn-active');
            jText.text('Перевести PB');
            jBtn.removeClass('btn-danger');
            jBtn.addClass('btn-secondary');
        }
        else{
            jQuery('#mutate').prepend('<input type=\"hidden\" name=\"translatePageBuilder\" id=\"translatePageBuilder\" value=\"1\" />');
            jBtn.addClass('btn-active');
            jText.text('Не переводить PB');
            jBtn.addClass('btn-danger');
            jBtn.removeClass('btn-secondary');
        }
    })

</script>