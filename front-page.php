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

/*
* Handle the nasa API
* This block lets the first daily user update the nasa image in /images/nasa_api/
* Possible option would be a daily cron job
*/
$frontpage_id = get_option('page_on_front');
$today = date("Ymd");
$last_api_call_date = false;

// returns the url of image
function updated_url_from_nasa_api() {
	$api_url = "https://apodapi.herokuapp.com/api/";
	$space_Data = json_decode(file_get_contents($api_url), true);
	return $space_Data['url'];
}

// function to clear nasa_api folder contents and save image to folder
function save_image_to_folder($image) {
	// first clear folder contents
	array_map( 'unlink', array_filter((array) glob(get_template_directory().'/images/nasa_api/*') ) );
	// Then save file
	$ch = curl_init($image);
	$fp = fopen(get_template_directory().'/images/nasa_api/nasa.jpg', 'w+');
	curl_setopt($ch, CURLOPT_URL, $image);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
	curl_setopt($ch, CURLOPT_HEADER, 'Content-Type: image/jpeg');
	curl_setopt($ch, CURLOPT_FILE, $fp);
	curl_exec($ch);
	curl_close($ch);
	fclose($fp);
}

//Get last api call from db (date)
$last_api_call = $wpdb->get_results("SELECT meta_value FROM $wpdb->postmeta WHERE post_id = $frontpage_id AND meta_key = 'last_api_call_to_nasa'");
if($last_api_call) {
	$last_api_call_date = ($last_api_call[0]->meta_value) ? $last_api_call[0]->meta_value : false ;
}

// If no date exist in db, init data in db and save file
if (!$last_api_call_date) {
	$sql = $wpdb->prepare("INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value ) VALUES ( %d, %s, %d )", $frontpage_id, 'last_api_call_to_nasa', $today); // Save time of api call
	$wpdb->query($sql);
	save_image_to_folder( updated_url_from_nasa_api());
}

// Update table with new date and save image to folder for first visitor each day
if($last_api_call_date && $last_api_call_date < $today) {
	$wpdb->query("UPDATE $wpdb->postmeta SET meta_value = '$today' WHERE post_id = $frontpage_id AND meta_key = 'last_api_call_to_nasa'");
	// Save image to folder
	save_image_to_folder( updated_url_from_nasa_api());
}

// !end of nasa API block

$context = Timber::context();
$context['posts'] = new Timber\PostQuery();
$context['hero'] = get_field('hero');
$context['quote'] = get_field('quote');
$context['cta'] = get_field('cta');
$context['related_posts'] = get_field('related_posts');

$related_posts = get_field('related_posts');
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