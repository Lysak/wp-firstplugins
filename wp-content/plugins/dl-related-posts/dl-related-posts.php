<?php /**
 * Plugin Name: Похожие записи
 * Description: Плагин выводит ссылки на похожие записи.
 * Plugin URI: http://lysak.github.io
 * Author: Dmytrii Lysak
 * Author URI: http://lysak.github.io
 * Version: 1.0.0
 * License: GPL2
 * Text Domain: text-domain
 * Domain Path: domain/path
 */

add_filter( 'the_content', 'dl_related_posts');
add_action( 'wp_enqueue_scripts', 'wp_register_styles_scripts' );

function wp_register_styles_scripts() {
    wp_register_script( 'dl-jquery-tool-js', plugins_url( 'js/jquery.tools.min.js', __FILE__ ), array('jquery') );
    wp_register_script( 'dl-scripts-js', plugins_url( 'js/dl_scripts.js', __FILE__ ), array('jquery') );
    wp_register_style( 'dl-related-posts', plugins_url( 'css/dl_style.css', __FILE__ ) );

    wp_enqueue_script( 'dl-jquery-tool-js' );
    wp_enqueue_script( 'dl-scripts-js' );
    wp_enqueue_style( 'dl-related-posts' );
}

function dl_related_posts($content) {

    if (!is_single()) return $content;

    $id = get_the_ID();
    $categories = get_the_category( $id );

    foreach ($categories as $category) {
        $cats_id[] = $category->cat_ID;
    }
    $related_posts = new WP_Query(
        array(
            'posts_per_page' => 5,
            'category__in'   => $cats_id,
            'orderby'        => 'rand',
            'post__not_in'   => array($id)
        )
    );

    if($related_posts->have_posts()) {
        $content .= '<div class="related-posts"><h3>Возможно вас заинтересуют эти записи:</h3>';

        while ($related_posts->have_posts()) {
            $related_posts->the_post();

            if(has_post_thumbnail()) {
                $img = get_the_post_thumbnail(get_the_ID(), array(100, 100), array('alt' => get_the_title(), 'title' => get_the_title()));
            }else{
                $img = '<img src="' . plugins_url('images/no_img.jpg', __FILE__) . '" alt="' . get_the_title() . '" title="' . get_the_title() . '" width="100" height="100">';
            }

            $content .= '<a href="' . get_permalink() . '">' . $img . '</a>';
        }

        $content .= '</div>';
        wp_reset_query();
    }

    return $content;

}