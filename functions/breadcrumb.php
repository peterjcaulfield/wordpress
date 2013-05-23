<?php
/* 
 * Add to functions.php
 * /
 
/*
 * Function for grabbing breadcrumb to current page
 * @param string seperator in markup for each breadcrumb item
 * @return string the breadcrumb ul 
 */
function get_breadcrumb($separator = null){
	
	global $post;
	global $wp_query;
	
	$ids = array();
	// add current post ID to id array
	array_push($ids, $post->ID);
	// get the parent post ID of the current post
	$thisParentId = $post->post_parent;
	// while the post parent id is not null, add to id's array and continue
	while($thisParentId != 0){
		array_push($ids, $thisParentId);
		$parent = get_post($thisParentId);
		$thisParentId = $parent->post_parent;
	}

    // flip array so we have current post first
	$ids = array_reverse($ids);
	// wrapper 
	$breadcrumb = '<ul class="breadcrumb">';
	
	$listItems = array();
	// mark up to li items and store in array
	foreach($ids as $link){
		$title = get_the_title($link);
		$href = '<a href=' . get_permalink($link) . '>';
		$string = '<li>' . $href . $title . '</a>' . '</li>';
		array_push($listItems, $string);	
	} 
	// if no seperator specified, use a space
	if($separator == NULL)
	    $separator = ' ';
	// convert to string using separator 
	$concat = implode($listItems, $separator);
    
	$breadcrumb .= $concat . '</ul>';
	
	echo $breadcrumb;	
}
