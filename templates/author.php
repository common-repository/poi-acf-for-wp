<?php
global $poi_author_id, $poi_acf_author_fields;

$poi_author_id = $author;

$poi_acf_fields = poi_acf_get_user_fields_data($poi_author_id);

$poi_acf_author_fields =$poi_acf_fields;



get_header(); ?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main" role="main">

        <!-- This sets the $curauth variable -->



        <?php

        ob_start();
        $curauth = (isset($_GET['author_name'])) ? get_user_by('slug', $author_name) : get_userdata(intval($author));
        ?>

        <h2>About: <?php echo $curauth->nickname; ?></h2>
        <dl>
            <dt>Website</dt>
            <dd><a href="<?php echo $curauth->user_url; ?>"><?php echo $curauth->user_url; ?></a></dd>
            <dt>Profile</dt>
            <dd><?php echo $curauth->user_description; ?></dd>
        </dl>

        <h2>Posts by <?php echo $curauth->nickname; ?>:</h2>

        <ul>
            <!-- The Loop -->

            <?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
                <li>
                    <a href="<?php the_permalink() ?>" rel="bookmark" title="Permanent Link: <?php the_title(); ?>">
                        <?php the_title(); ?></a>,
                    <?php the_time('d M Y'); ?> in <?php the_category('&');?>
                </li>

            <?php endwhile; else: ?>
                <p><?php _e('No posts by this author.'); ?></p>

            <?php endif; ?>

            <!-- End Loop -->

        </ul>


        <h2>POI ACF By <?php echo $curauth->nickname;?>:</h2>

        <ul>
            <!-- The Loop -->
        <?php


            if(!empty($poi_acf_fields)){

                foreach ($poi_acf_fields as $page_name => $poi_fields){


                    echo "<li style='list-style: none'><h3>$page_name</h3></li>";

                    echo "<ul>";

                    if(!empty($poi_fields)){


                        foreach ($poi_fields as $index => $field){


                            echo "<li><strong>{$field['label']}</strong>: {$field['value']}</li>";

                        }
                    }

                    echo "</ul>";

                }
            }else{

                ?>
                <p><?php _e('No Poi ACF by this author.'); ?></p>

                <?php


            }

        ?>





            <!-- End Loop -->

        </ul>

            <?php

                $content = ob_get_clean();
                $author_content = apply_filters('poi_author_content', $content, $poi_author_id);
//
                echo $author_content;
            ?>


        </main>
    </div>
<?php get_sidebar(); ?>
<?php get_footer(); ?>