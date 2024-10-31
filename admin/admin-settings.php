<?php
global $poi_acf_url;
?>
<div class="wrap poi_acf_settings_div">

    <div class="h4 my-3">
        POI ACF for WordPress
        <a href="<?php echo admin_url('/edit.php?post_type=poi_acf_template') ?>" class="page-title-action" target="_blank"><?php _e("POI ACF Templates",'poi-acf-wp'); ?></a>

    </div>


    <h2 class="nav-tab-wrapper">
        <a class="nav-tab nav-tab-active"><?php _e("Content Display",'poi-acf-wp'); ?></a>
        <a class="nav-tab nav-tab-active"><?php _e("Author Profile",'poi-acf-wp'); ?></a>
<!--        <a class="nav-tab nav-tab-active">--><?php //_e("Shortcode & Templates",'poi-acf-wp'); ?><!--</a>-->
    </h2>

    <div class="alert alert-success my-3" style="display: none">
        <?php _e("Settings saved successfully.",'poi-acf-wp'); ?>
    </div>


    <?php
        if(function_exists('poi_acf_add_new_settings_form_call_back')){
            poi_acf_add_new_settings_form_call_back();
        }
    ?>

    <div class="poi-acf-modal modal fade" id="poi_acf_modal" tabindex="-1" role="dialog" aria-labelledby="poi_acf_modalLabel" aria-hidden="true" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="poi_acf_modalLabel"><?php _e("Already Exist",'poi-acf-wp'); ?></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Author.php <?php _e(" file already exist. Are you want to replace it.",'poi-acf-wp'); ?>
                </div>
                <div class="modal-footer">
                    <button type="button " class="btn btn-primary confirm"><?php _e("Yes",'poi-acf-wp'); ?></button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php _e("No",'poi-acf-wp'); ?></button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal" id="ab_load_modal" data-backdrop="static" tpoi_acfdex="-1" role="dialog" aria-labelledby="ajax_load_modalLabel"  >
        <div class="modal-dialog" role="document" style="max-width: 50px;">
            <div class="modal-content" style="margin-top: 45vh; width: max-content">

                <img src="<?php echo  $poi_acf_url ?>images/loader.gif" style="width: 50px; height: 50px"/>

            </div>
        </div>
    </div>


</div>




<script type="text/javascript" language="javascript">

    jQuery(document).ready(function($){


            <?php if(isset($_GET['t'])): ?>



            $('.nav-tab-wrapper .nav-tab:nth-child(<?php echo $_GET['t']+1; ?>)').click();

            <?php endif; ?>

    });

</script>


