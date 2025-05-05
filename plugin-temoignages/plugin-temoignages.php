<?php
/*
Plugin Name: Témoignage clients
Description: Un plugin pour afficher les témoignages de clients.
Version: 1.0
Author: Lucile
*/

function temoignage_plugin_enqueue_styles() {
    wp_enqueue_style(
        'temoignage-plugin-style',
        plugin_dir_url( __FILE__ ) . 'style.css'
    );
}
add_action( 'wp_enqueue_scripts', 'temoignage_plugin_enqueue_styles' );

// Enregistrer le Custom Post Type 'temoignage'
function temoignages_custom_post_type() {
    register_post_type( 'temoignage', array(
        'labels' => array(
            'name' => 'Témoignages',
            'singular_name' => 'Témoignage',
            'add_new' => 'Ajouter un témoignage',
            'add_new_item' => 'Ajouter un nouveau témoignage',
            'edit_item' => 'Modifier le témoignage',
            'new_item' => 'Nouveau témoignage',
            'view_item' => 'Voir le témoignage',
            'search_items' => 'Rechercher un témoignage',
            'not_found' => 'Aucun témoignage trouvé',
            'menu_name' => 'Témoignages',
        ),
        'public' => true,
        'has_archive' => true,  // Archive des témoignages activée
        'rewrite' => array( 'slug' => 'temoignages' ), // Permet de personnaliser l'URL
        'supports' => array( 'title', 'editor', 'thumbnail' ), // Champs supportés : titre, contenu, image à la une
        'menu_icon' => 'dashicons-testimonial',  // Icône dans le menu admin
    ) );
}
add_action( 'init', 'temoignages_custom_post_type' );


// Shortcode pour afficher les témoignages
function afficher_temoignages_shortcode( $atts ) {
    ob_start();

    $args = array(
        'post_type' => 'temoignage',
        'posts_per_page' => 3, // Affiche les 3 derniers témoignages
        'orderby' => 'date',
        'order' => 'DESC',
    );

    $query = new WP_Query( $args );

    if ( $query->have_posts() ) {
        echo '<div class="temoignages-liste">';
        while ( $query->have_posts() ) {
            $query->the_post();
            echo '<div class="temoignage">';
                echo '<h3>' . get_the_title() . '</h3>';
                echo '<div class="temoignage-content">' . get_the_content() . '</div>';
                
                if ( has_post_thumbnail() ) {
                    echo '<div class="temoignage-thumbnail">' . get_the_post_thumbnail( get_the_ID(), 'thumbnail' ) . '</div>';
                }

                // Afficher la fonction si elle existe
                if ( get_field('fonction_du_client') ) {
                    echo '<p class="fonction"><strong>Fonction :</strong> ' . esc_html(get_field('fonction_du_client')) . '</p>';
                }

                echo '<p class="slug"><em>Slug : ' . get_post_field( 'post_name', get_post() ) . '</em></p>';
            echo '</div>';
        }
        echo '</div>';
        wp_reset_postdata();
    } else {
        echo '<p>Aucun témoignage pour le moment.</p>';
    }

    return ob_get_clean();
}
add_shortcode( 'afficher_temoignages', 'afficher_temoignages_shortcode' );
