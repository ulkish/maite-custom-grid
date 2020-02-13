<?php
/**
 * Plugin Name:       Maite - Custom Grid
 * Plugin URI:        
 * Description:       Adds a custom grid to display courses.
 * Version:           1.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Hugo Moran
 * Author URI:        
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 *
 * Maite Custom Grid is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * Maite Custom Grid is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Maite Grid. If not, see https://www.gnu.org/licenses/gpl-2.0.html
 *
 * @package Maite Custom Grid
 */

// TODO LIST:
// Load custom css file for present and future changes. [DONE]
// Load library used to implement list to slider behaviour on mobile use.


function maite_custom_grid_scripts() {
	wp_enqueue_style( 'maite-customization-style', plugin_dir_url( __FILE__ ) . 'assets/css/maite-customizations.css' );
	// wp_enqueue_script( 'charts-js', 'https://cdn.jsdelivr.net/npm/chart.js@2.8.0/dist/Chart.min.js' );
}
add_action( 'wp_enqueue_scripts', 'maite_custom_grid_scripts' );

/**
 * Outputs a list of all the styles enqueued on the site.
 *
 * @return void
 */
function maite_inspect_styles() {
    global $wp_styles;
    echo "<h2>Enqueued CSS Stylesheets</h2><ul>";
    foreach( $wp_styles->queue as $handle ) :
        echo "<li>" . $handle . "</li>";
    endforeach;
    echo "</ul>";
}
// add_action( 'wp_print_styles', 'maite_inspect_styles' );

/**
 * Shortcode for outputing a grid with WooCommerce products.
 *
 * @return $product_grid
 */
function maite_demo_shortcode() {

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 10,
    );
    $loop = new WP_Query( $args );

    $product_grid = '<div class="columns" id="maite_products">';
    while ( $loop->have_posts() ) : $loop->the_post();
        global $product;
        // $test_image = wp_get_attachment_image_src(
            // get_post_thumbnail_id($product->id), 'large')[0];
        
        $image_url = get_the_post_thumbnail_url($product->id);
        $title     = get_the_title();
        $excerpt   = get_the_excerpt();

        $a_product = array();

        array_push($a_product, array()
        );
        
        // For each product, append a card to the grid.
        $product_grid .=
        '<div class="card is-4">
            <div class="card-image">
                <figure class="image is-3by2">
                    <img src="' . $image_url . '" alt="Placeholder image">
                </figure>
            </div>
            <div class="card-content">
                <div class="media">
                    <div class="media-content">
                        <p class="title is-4">' . $product->get_title() .'</p>
                    </div>
                </div>
                <div>Stars: ' . $product->get_average_rating() . ' </div>
                <div class="content">
                    ' . $product->get_description() . '<br>
                    <span class"is-pulled-left is-3"><strong>' . $product->get_price() . '</strong></span>
                    <a href="' . get_the_permalink() . '" class="button is-pulled-right is-primary is-rounded">+ Información</a>

                </div>
                
            </div>
        </div>';
    endwhile;


    $product_grid .= '</div>';
    wp_reset_query();

    return $product_grid;
}
add_shortcode( 'product_list', 'maite_demo_shortcode' );

/**
 * Echoes the CDN for Bulma.css on the site header.
 *
 * @return void
 */
function load_bulma_cdn() {
    echo '
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    ';
}
add_action( 'wp_head', 'load_bulma_cdn' );

// print_r(get_post_meta(20));
// print_r(get_post_meta(20, '_wc_average_rating', true));