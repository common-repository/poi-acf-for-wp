<?php

    global  $poi_acf_general_settings, $poi_acf_url;
    $display_locations = array(

            'after_content' => __("After Content",'poi-acf-wp'),
            'before_content' => __("Before Content",'poi-acf-wp'),
            'only_acf' => __("Show only ACF fields",'poi-acf-wp'),
    );

    $template_args = array(
        'numberposts' => -1,
        'post_type' => 'poi_acf_template',
        'post_status' => 'any',
    );




    $template_posts = get_posts($template_args);

    $author_file_exist = poi_acf_is_author_file_exist();

    $btn_class = 'danger';
    $btn_title = '';
    $del_btn = 'none';

    if($author_file_exist){
        $btn_class = 'success';
        $btn_title = __('File already exist.', 'poi-acf-wp');
        $del_btn = '';
    }

    $poi_acf_display_location  = isset($poi_acf_general_settings['poi_acf_display_location']) ? $poi_acf_general_settings['poi_acf_display_location']: 'after_content';


?>




<div class="nav-tab-content">

    <form action="<?php echo $current_url; ?>" method="post">


        <div class="container-fluid mt-3">
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="h5"><?php _e("ACF fields display location",'poi-acf-wp'); ?>:</div>
                </div>
            </div>

            <div class="row mb-5">

                <?php

                    foreach ($display_locations as $id => $location_name){

                        $img_src = $poi_acf_url.'images/'.$id.'.jpg';
                        $selected_location = $poi_acf_display_location == $id ? 'poi_selected_img' : '';

                        ?>
                            <div class="col-md-3 col-lg-2 col-4 poi_acf_location_img">

                                <figure class="figure">

                                    <img src="<?php echo $img_src ?>" alt="<?php echo $location_name ?>" class="img-thumbnail w-100 <?php echo $selected_location; ?>" data-type="<?php echo $id; ?>">
                                    <figcaption class="figure-caption text-center">
                                        <?php echo $location_name; ?>
                                    </figcaption>
                                </figure>
                            </div>

                        <?php
                    }

                ?>

            </div>


            <div class="row mb-3 d-none">

                <div class="col-md-3">
                    <label for="poi_acf_display_location"><?php _e("ACF fields display location",'poi-acf-wp'); ?></label>
                </div>
                <div class="col-md-4">
                    <select class="form-control poi_general_inputs" id="poi_acf_display_location" name="poi_acf_display_location">

                        <?php



                            foreach ($display_locations as $key => $value){

                                $selected = $key == $poi_acf_display_location ? 'selected' : '';
                                echo "<option value='$key' $selected>$value</option>";

                            }

                        ?>


                    </select>
                </div>

            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="h5"><?php _e("Templates",'poi-acf-wp'); ?>:</div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-3">
                    <label for="poi_acf_template_selection"><?php _e("ACF fields template",'poi-acf-wp'); ?></label>
                </div>
                <div class="col-md-4">
                    <select class="form-control poi_general_inputs" id="poi_acf_template_selection" name="poi_acf_template_selection">

                        <?php


                        $template_posts  = $template_posts ?? array();

                        $default_text = __('Default','poi-acf-wp');

                        $current_template = isset($poi_acf_general_settings['poi_acf_template_selection']) ? $poi_acf_general_settings['poi_acf_template_selection'] : 'default';
                        $template_selected = 'default' == $current_template ? 'selected' : '';


                        echo "<option value='default' $template_selected >$default_text</option>";


                        if(!empty($template_posts)){

                            foreach ($template_posts as $index => $template){

                                $template_selected = $template->ID == $current_template ? 'selected' : '';
                                echo "<option value='{$template->ID}' $template_selected>{$template->post_title}</option>";

                            }

                        }


                        ?>


                    </select>
                </div>


            </div>





        </div>


    </form>

</div>

<div class="nav-tab-content hide">

    <div class="container-fluid mt-3">


        <div class="row">
            <div class="col-md-12">
                <div class="h4"><?php _e("Author Profile",'poi-acf-wp') ?></div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <a class="btn btn-<?php echo $btn_class; ?> text-white poi_copy_author" title="<?php echo $btn_title; ?>"><?php _e("Copy author.php file",'poi-acf-wp') ?></a>
                <a class="btn btn-warning text-white poi_del_author" style="display: <?php echo $del_btn; ?>"><?php _e("Delete author.php file",'poi-acf-wp') ?></a>
            </div>
        </div>

    </div>

</div>


<div class="nav-tab-content hide poi_shortcodes_wrapper">

    <?php

        $poi_field_group_ids = poi_acf_get_field_groups();




    ?>

    <div class="container-fluid mt-3">


        <div class="row mb-3">
            <div class="col-md-12">
                <div class="h4"><?php _e("Template Selection",'poi-acf-wp') ?></div>
            </div>
        </div>





        <hr class="clearfix mb-3">


        <div class="row mb-3">
            <div class="col-md-12">
                <div class="h4"><?php _e("Shortcodes",'poi-acf-wp') ?></div>
            </div>
        </div>





        <div class="row">
            <div class="col-md-12">

                <?php

                    if(!empty($poi_field_group_ids)){
                        echo "<ol>";
                        foreach ($poi_field_group_ids as $index => $field_group){

                            $group_fields = acf_get_fields($field_group['ID']);

                            if(!empty($group_fields)){


                            ?>

                                <li class="h5"><?php echo $field_group['title'] ?></li>

                                <ul class="list-group">



                            <?php

                                foreach ($group_fields as $field_index => $single_field){

                                    ?>

                                        <li class='list-group-item mb-0'>
                                            <span class="mb-2 d-inline-block">
                                            <?php echo $single_field['label']." (".$single_field['type'].")" ?>
                                            </span>
                                            <ul class="list-group">
                                                <li class="list-group-item">
                                                    [poi_acf_field id=<?php echo $single_field['ID']?> page="page_name"]
                                                </li>
                                            </ul>

                                        </li>


                                    <?php

                                }


                            }

                            echo "</ul>";

                        }
                        echo "</ol>";
                    }

                ?>

            </div>
        </div>

    </div>

</div>