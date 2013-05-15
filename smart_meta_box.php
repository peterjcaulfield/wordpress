i<?php
/**
* Meta box generator for WordPress
* Compatible with custom post types
*
* Support input types: text, textarea, checkbox, select, radio
* 
* Original author: Nikolay Yordanov <me@nyordanov.com>
*
* Modifying original for enhanced functionality/ease of use.
*
* Changelog: 
*
* Added check in add metabox function to allow for adding of metaboxes to specfic page templates
*
* 
* */
class smart_meta_box {

	protected $meta_box;
	protected $id;

	static $prefix = '_smartmeta_';

	// create meta box based on given data
	public function __construct($id, $opts) {
		if (!is_admin())
		return;
		
		$this->meta_box = $opts;
		$this->id = $id;
		// here we hook the class' add and save methods to the add meta box and save post hooks respectively
		add_action('add_meta_boxes', array(&$this, 'add'));
		
		add_action('save_post', array(&$this, 'save'));
	}
	
	// add  metabox function
	public function add(){
		
		foreach($this->meta_box['pages'] as $page) {
			//Added check here to see if post type is page or post
			if ($page == 'post' || $page == 'page'){
			    add_meta_box($this->id, $this->meta_box['title'], array(&$this, 'show'), $page, $this->meta_box['context'], $this->meta_box['priority']);
            }else
            { 	// if post type isn't a post or a page, we can add it just to the specific custom page template
				$post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'];
				$template_file = get_post_meta($post_id,'_wp_page_template',TRUE);
				// check for a template type
				if ($template_file == $page){
                        add_meta_box($this->id, $this->meta_box['title'], array(&$this, 'show'), 'page', $this->meta_box['context'], $this->meta_box['priority']);
				}				
			}
		}	
	}
	
	// render metabox function
	public function show(){
			
		// Use nonce for verification
		echo '<input type="hidden" name="'.$this->id.'_meta_box_nonce" value="', wp_create_nonce('smartmetabox'.$this->id), '" />';
		echo '<table class="form-table">';
		
		foreach ($this->meta_box['fields'] as $field) {
			extract($field);
			
			$id = self::$prefix.$id;
			
			$value = self::get($field['id']);
			if (empty($value) && !sizeof(self::get($field['id'], false))) {
				$value = isset($field['default']) ? $default : '';
			}
		
			echo '<tr>',
			'<th style="width:20%"><label for="'. $id. '">'. $name. '</label></th>'.
			'<td>';
			
			include "fields/$type.php";
			
			if (isset($desc)) {
				echo ' <span>'.$desc.'</span>';
			}
			echo '</td></tr>';
		}// end foreach
		
		echo '</table>';
			
	}
		
	// Save data from meta box
	public function save($post_id) {
		// verify nonce
		if (!isset($_POST[$this->id.'_meta_box_nonce']) || !wp_verify_nonce($_POST[$this->id.'_meta_box_nonce'], 'smartmetabox'.$this->id)) {
			return $post_id;
		}
	
		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}
	
		// check permissions
		if ('post' == $_POST['post_type']) {
			if (!current_user_can('edit_post', $post_id)) {
				return $post_id;
			}
		} elseif (!current_user_can('edit_page', $post_id)) {
			return $post_id;
		}
		
		foreach ($this->meta_box['fields'] as $field) {
			$name = self::$prefix.$field['id'];
			
			if(isset($_POST[$name]) || isset($_FILES[$name])) {
				$old = self::get($field['id'], true, $post_id);
				$new = $_POST[$name];
		
			    if ($new != $old) {
				    self::set($field['id'], $new, $post_id);
			    }
			} elseif($field['type'] == 'checkbox') {
			/*
			* Checkboxes are not send in POST if they are not checked;
			* We should set them to 'false' instead of deleting them;
			* Otherwise, we won't be able to distinguish them from new fields, which have not been saved yet.
			*/
				self::set($field['id'], 'false', $post_id);
            } 
            else 
            {
				self::delete($field['id'], $name);
			}
		}// end foreach
	}
	
	static function get($name, $single = true, $post_id = null) {
	global $post;
	
	return get_post_meta( isset($post_id) ? $post_id : $post->ID, self::$prefix.$name, $single);
	}
	
	static function set($name, $new, $post_id = null) {
	global $post;
	
	return update_post_meta( isset($post_id) ? $post_id : $post->ID, self::$prefix.$name, $new);
	}
	
	static function delete($name, $post_id = null) {
	global $post;
	
	return delete_post_meta( isset($post_id) ? $post_id : $post->ID, self::$prefix.$name);
	}

}

// public function that calls SmartMetaBox constructor
function add_meta_box($id, $opts) {
	new smart_meta_box($id, $opts);
}

/*
* Example of meta box generator usage
* using all features supported by the generator
*/

/*
add_smart_meta_box('smart_meta_box_demo', array(
		'title'     => 'Smart meta box',
		'pages'		=> array('post'),
		'context'   => 'normal',
		'priority'  => 'high',
		'fields'    => array(
			array(
			'name' => 'Text',
			'id' => 'smb_text',
			'default' => 'default',
			'desc' => 'Description',
			'type' => 'text',
			),
			array(
			'name' => 'Textarea',
			'id' => 'smb_textarea',
			'default' => 'default',
			'desc' => 'Description',
			'type' => 'textarea',
			),
			array(
			'name' => 'Checkbox',
			'id' => 'smb_checkbox',
			'default' => 'true',
			'desc' => 'Description',
			'type' => 'checkbox',
			),
			array(
			'name' => 'Select',
			'id' => 'smb_select',
			'default' => 'second-val',
			'desc' => 'Description',
			'type' => 'select',
			'options' => array(
				'first-val' => 'First',
				'second-val' => 'Second',
				)
			),
			array(
			'name' => 'Radios',
			'id' => 'smb_radio',
			'default' => 'second-val',
			'desc' => 'Description',
			'type' => 'radio',
			'options' => array(
				'first-val' => 'First',
				'second-val' => 'Second',
				)
			),
		) // closes fields array
	) // closes opts array
); // closes function parameters 
*/
