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

// === TODO LIST ===
// - Load custom css file for present and future changes. [DONE]
// - Output normal grid styled with Bulma before adding the slider functionality. [IN PROGRESS]
// - Load library used to implement list to slider behaviour on mobile use.

/**
 * Enqueues our css file into WP.
 *
 * @return void
 */
function maite_custom_grid_scripts() {
    wp_enqueue_style( 'maite-customization-style',
        plugin_dir_url( __FILE__ )
        . 'assets/css/maite-customizations.css'
    );
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
 * Shortcode for outputing a grid with WooCommerce and LearnDash products.
 *
 * @return string $complete_html_grid
 */
function maite_custom_shortcode() {

    // Getting all WooCommerce products.
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => 10,
    );
    $loop = new WP_Query( $args );

    $all_products = array();
    while ( $loop->have_posts() ) : $loop->the_post();
        global $product;
        
        // Creating a column for every product.
        $a_product =
        '<div class="column is-4">
            <div class="card-image">
                <figure class="image ">
                    <img src="' . get_the_post_thumbnail_url( $product->get_id() ) . '" alt="Placeholder image">
                </figure>
            </div>
            <div class="card-content">
                <div class="media">
                    <div class="media-content">
                        <p class="title is-4">' . $product->get_title() .'</p>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-one-third">
                        <i class="fas fa-star" style="color: yellow;"></i> ' . intval($product->get_average_rating()) . '
                    </div>
                    <div class="column is-two-thirds">
                        <i class="far fa-calendar"></i> ' . 'Here there will be dates' . '
                    </div>
                </div>

                <div class="content">
                    ' . get_the_excerpt() . '
                </div>
                <div class="columns">
                    <div class="column is-one-third">
                        <span class"is-half"><strong>$' . $product->get_price() . '</strong></span>
                    </div>
                    <div class="column is-two-thirds">
                    <a href="' . get_the_permalink() . '" class="button is-half is-primary is-rounded">+ Información</a>
                    </div>
                </div>
            </div>
        </div>';
        array_push($all_products, $a_product);
    endwhile;
    wp_reset_query();

    // Getting all LearnDash products.
    $args = array(
        'post_type'      => 'sfwd-courses',
        'posts_per_page' => 10,
    );
    $loop = new WP_Query( $args );

    while ( $loop->have_posts() ) : $loop->the_post();
        global $post;

        // Creating a column for every product.
        $a_product =
        '<div class="column is-4">
            <div class="card-image">
                <figure class="image ">
                    <img src="' . get_the_post_thumbnail_url( $post->ID ) . '" alt="Placeholder image">
                </figure>
            </div>
            <div class="card-content">
                <div class="media">
                    <div class="media-content">
                        <p class="title is-4">' . get_the_title() .'</p>
                    </div>
                </div>
                <div class="columns">
                    <div class="column is-one-third">
                        <i class="fas fa-star" style="color: yellow;"></i> ' . 'X' . '
                    </div>
                    <div class="column is-two-thirds">
                        <i class="far fa-calendar"></i> ' . 'Here there will be dates' . '
                    </div>
                </div>

                <div class="content">
                    ' . get_the_excerpt() . '
                </div>
                <div class="columns">
                    <div class="column is-one-third">
                        <span class"is-half"><strong>$' . '9999' . '</strong></span>
                    </div>
                    <div class="column is-two-thirds">
                    <a href="' . get_the_permalink() . '" class="button is-half is-primary is-rounded">+ Información</a>
                    </div>
                </div>
            </div>
        </div>';

    array_push($all_products, $a_product);
    endwhile;
    // return print_r($learndash_products);


    // Gathering all products into chunks of 3 will help us display them in Bulma columns (rows),
    // in addition all future changes to individual rows will be easier to handle.
    $product_rows = array_chunk($all_products, 3);
    
    // The final output returned from the shortcode.
    $complete_html_grid = '';

    // Append 3 products together to form a row.
    foreach ($product_rows as $row) {
        // Append every product in a row to a string, enclose them within a 'columns' class.
        $complete_html_grid .= '<div class="columns">' .  implode($row) . '</div>';
    }

    return $complete_html_grid;
}
add_shortcode( 'product_list', 'maite_custom_shortcode' );

/**
 * Echoes the CDN for Bulma.css and Font Awesome 5 on the site header.
 *
 * @return void
 */
function load_maite_cdns() {
    echo '
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@0.8.0/css/bulma.min.css">
    <script defer src="https://use.fontawesome.com/releases/v5.3.1/js/all.js"></script>
    ';
}
add_action( 'wp_head', 'load_maite_cdns' );
