<?php
/**
 * The main template file
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists
 *
 * Methods for TimberHelper can be found in the /lib sub-directory
 *
 * @package  WordPress
 * @subpackage  Timber
 * @since   Timber 0.1
 */

$context          = Timber::context();
$context['posts'] = new Timber\PostQuery();
$context['hero']   = get_field('hero');
$context['quote']   = get_field('quote');
$context['cta']   = get_field('cta');
$context['related_posts']   = get_field('related_posts');

$related_posts   = get_field('related_posts');
$post_id_array = $related_posts[0]['related_post'];
$args = array(
'post__in' => $post_id_array
);
$context['featured_posts'] = get_posts($args);

// Add featured image to post
for ($i=0;$i<count($context['featured_posts']);$i++) {
	$link = get_permalink($context['featured_posts'][$i]->ID);
	$image = get_the_post_thumbnail_url($context['featured_posts'][$i]->ID, 'medium');
	$context['featured_posts'][$i]->image = $image;
	$context['featured_posts'][$i]->link = $link;
}

Timber::render( 'front-page.twig', $context );