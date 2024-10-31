jQuery(document).ready(function($){

    var load_modal = $('.poi_acf_settings_div .modal');
    var success_alert = $('.poi_acf_settings_div .alert.alert-success');

    $('.poi_acf_settings_div a.nav-tab').click(function(){

        $(this).siblings().removeClass('nav-tab-active');
        $(this).addClass('nav-tab-active');
        // , form:not(.wrap.wc_settings_div .nav-tab-content)'
        $('.nav-tab-content').hide();
        $('.nav-tab-content').eq($(this).index()).show();
        window.history.replaceState('', '', poi_acf_obj.this_url+'&t='+$(this).index());
        poi_acf_obj.poi_acf_tab = $(this).index();

    });

    $('.poi_acf_settings_div .poi_general_inputs').on('change', function(){

        load_modal.show();
        success_alert.hide();

        var poi_acf_general = {};

        $.each($('.poi_acf_settings_div .poi_general_inputs'), function(){
            var this_input = $(this);

            poi_acf_general[this_input.prop('name')] = this_input.val();
        })

        var data = {

            action: 'poi_acf_general_settings_update',
            poi_acf_general_settings : poi_acf_general,
            poi_acf_general_nonce : poi_acf_obj.poi_acf_general_nonce,
        }

        $.post(ajaxurl, data, function(response, code){

            load_modal.hide();


            if(code == 'success' && response == 1){

                success_alert.show();

                setTimeout(function(){

                    success_alert.fadeOut();
                }, 5000);

            }

        })
    });

    $('.poi_del_author.btn').on('click', function () {

        if(!confirm(poi_acf_obj.file_del_confirm)){
            return;
        }

        load_modal.show();
        success_alert.hide();
        var this_btn = $(this);
        var copy_btn = $('.poi_copy_author.btn');


        var data = {
            'action': 'poi_acf_delete_author_file',
        }

        $.post(ajaxurl, data, function (response, status) {
            load_modal.hide();

            if (status == 'success' && response == '1') {

                var old_text = success_alert.text();
                success_alert.text(poi_acf_obj.file_del_success);
                success_alert.show();
                this_btn.hide()

                copy_btn.addClass('btn-danger');
                copy_btn.removeClass('btn-success');

                setTimeout(function(){

                    success_alert.text(old_text);
                    success_alert.fadeOut();

                }, 5000);

            }else{



            }

        });


        });



    $('.poi_copy_author.btn').on('click', function () {

        load_modal.show();
        var this_btn = $(this);
        var del_btn = $('.poi_del_author.btn');


        var data = {
            'action': 'poi_acf_copy_author_file',
        }

        $.post(ajaxurl, data, function (response, status) {
            load_modal.hide();

            if (status == 'success') {

                if(response.is_error != 'yes'){

                    if (response.file_exist == 'yes') {

                        $('.poi-acf-modal.modal').modal('show');

                    }

                    if(response.file_copy == 'yes'){

                        alert('file coppied');
                        this_btn.removeClass('btn-danger');
                        this_btn.addClass('btn-success');
                        del_btn.show();
                    }

                }else{

                    alert(response.error);

                }
            }
        });



    });

    $('.poi-acf-modal.modal .confirm').on('click', function () {

        load_modal.show();

        $('.poi-acf-modal.modal').modal('hide');

        var data = {
            'action': 'poi_acf_copy_author_file',
            'replace_file': 'yes',
        }

        $.post(ajaxurl, data, function (response, status) {

            load_modal.hide();
            if (status == 'success') {

                if(response.is_error != 'yes'){


                    if (response.file_exist == 'yes' && response.file_copy == 'yes') {

                        alert('file coppied');

                    }

                }else{

                    alert(response.error);

                }
            }
        });



    });

    $('.poi_acf_settings_div .poi_acf_location_img').on('click', function(){

        var all_div = $('.poi_acf_settings_div .poi_acf_location_img');
        all_div.find('img').removeClass('poi_selected_img');
        var this_div = $(this);
        var this_img = this_div.find('img');
        this_img.addClass('poi_selected_img');
        var this_type = this_img.data('type');
        console.log(this_type);
        var display_location = $('#poi_acf_display_location');
        display_location.val(this_type);
        display_location.trigger('change');




    });

});