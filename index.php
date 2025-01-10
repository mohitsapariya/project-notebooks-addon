<?php
/**
 * Plugin Name: Project Listing Addon
 * Plugin URI:  https://yourwebsite.com/
 * Description: Override the [project-all-listing] shortcode to add custom functionality.
 * Version:     1.0
 * Author:      Your Name
 * Author URI:  https://yourwebsite.com/
 * License:     GPL2
 */


if (!defined('ABSPATH')) {
    exit;
}
define('PTO_NB_PLUGIN_PATHS_ADDON', plugin_dir_path(__FILE__));

function custom_project_all_listing_shortcode($atts) {
	$category_slug="";
	if(!empty($atts) && isset($atts['cat']) && !empty($atts['cat'])){  		
		$category_name = $atts['cat']; 
		$taxonomy = 'project-categories';
		$term = get_term_by('name', $category_name, $taxonomy);		
		if ($term) {
			$category_slug = $term->slug;
		} 
	}

     ob_start();
        $current_user = wp_get_current_user();
        $user_roles = $current_user->roles;
        $permision_assign = 0;
        ?>
        <div class="main-project-lists">
            <div class="main-project-lists-row">
                <div class="projects-list">
                    <div class="projects-list-tab-row">
                        <ul class="pto-project-tabs">
                            <li class="tab-link current" data-tab="all-projects">
                                <h2 class="pto-header-two"><?php esc_html_e('All Projects', PTO_NB_MYPLTEXT); ?></h2>
                            </li>
                            <li class="tab-link my-projects-tab" data-tab="my-projects">
                                <h2 class="pto-header-two"><?php esc_html_e('My Projects', PTO_NB_MYPLTEXT); ?></h2>
                            </li>
                        </ul>
                        <?php
                        if (is_user_logged_in()) {
                            $user_permision = get_option("user_per");
                            if ($user_permision == "allo-user-create-own-project") {
                                ?>
                                <a href="<?php echo esc_url(site_url()); ?>/wp-admin/post-new.php?post_type=pto-project" class="add-new-btn front-primary-btn pto-button-setting"><?php esc_html_e('Add new', PTO_NB_MYPLTEXT); ?></a>
                            <?php } else {
                                global $current_user;
                                $user_roles = $current_user->roles;
                                $cnt = 0;
                                foreach ($user_roles as $role) {
                                    if ($role == "project_manager" || $role == "project_plugin_administrators" || $role == "administrator") {
                                        ?>
                                        <a href="<?php echo esc_url(site_url()); ?>/wp-admin/post-new.php?post_type=pto-project" class="add-new-btn front-primary-btn pto-button-setting"><?php esc_html_e('Add new', PTO_NB_MYPLTEXT); ?></a>
                                        <?php
                                    }
                                }
                            }
                        } ?>

                    </div>

                    <div class="tab-data all-projects-show-box">
                        <div id="all-projects" class="tab-content current">
                            <ul class="projects-list-block">
                                <?php
                                $get_user_post = array();
                                if (is_user_logged_in()) {
                                    $current_user_id = get_current_user_id();
                                    $get_user_post = get_user_meta($current_user_id, 'pto_project_request_id', true);
                                }
                                if (empty($get_user_post)) {
                                    $get_user_post = array();
                                }
                                $args = array(
                                    'post_type' => 'pto-project',
                                    'orderby'    => 'ID',
                                    'post_status' => 'publish',
                                    'order'    => 'DESC',
                                    'posts_per_page' => -1, // this will retrive all the post that is published 

                                );
								if (!empty($category_slug)) {
									$args['tax_query'] = array(
										array(
											'taxonomy' => 'project-categories', // Replace 'project-category' with your actual taxonomy slug
											'field'    => 'slug',            // You can use 'term_id' or 'name' instead of 'slug'
											'terms'    => $category_slug,
										),
									);
								}
                                $result = new \WP_Query($args);
                                $request_allow_or_not = get_option("request_access");
                                if ($result->have_posts()) {
                                    while ($result->have_posts()) {
                                        $result->the_post();
                                        $post_id =  get_the_ID();
                                        $get_show_project_link = get_post_meta($post_id, "show_projects_chk", true);
                                        $c_user_id = get_current_user_id();
                                        $author_id = get_post_field('post_author', $post_id);
                                        $get_user_req_post = get_post_meta($post_id, 'pto_project_user_id', true);
                                        $img = get_the_post_thumbnail_url(get_the_ID());
                                        $get_project_due_date = get_post_meta($post_id, "pto_project_due_date", "true");
                                        $duedate =  strtotime($get_project_due_date);
                                        $current_date =  strtotime(date("m/d/Y"));
                                        $due_check = 0;
                                        if (empty($img)) {
                                            $img = PTO_NB_PLUGIN_PATH . "assets/images/noimg-low.png";
                                        }
                                        if (empty($get_user_req_post)) {
                                            $get_user_req_post = array();
                                        }

                                        if ($due_check == 0) {
                                            if ($get_show_project_link == 1) {
                                                ?>
                                                <li class="single-project-list">
                                                    <div class="single-project-block">
                                                        <div class="small-priject-banner-img">
                                                            <?php

                                                            if (!empty($img)) { ?>
                                                                <img src="<?php echo esc_html($img);  ?>">
                                                            <?php } ?>
                                                        </div>
                                                        <div class="single-project-info">
                                                            <?php if (is_user_logged_in()) {
                                                                if (($c_user_id == $author_id) || (in_array($c_user_id, $get_user_req_post))) {
                                                                    $permision_assign = 1; ?>
                                                                    <a href="<?php echo esc_url(site_url()); ?>/wp-admin/post.php?post=<?php echo esc_html($post_id); ?>&action=edit" class='edit-button'><input type="submit" class=" front-primary-btn pto-button-setting" value="Edit"></a>
                                                                <?php } else {
                                                                    if (array_key_exists($post_id, $get_user_post)) {
                                                                        ?>
                                                                        <a class="edit-button"><input type="submit" class="front-primary-btn pto-button-setting" value="Requested"></a>
                                                                        <?php
                                                                    } else {


                                                                        $ur_check = 0;
                                                                        if (in_array("project_plugin_administrators", $user_roles)) {
                                                                            $ur_check = 1;
                                                                        }
                                                                        if (in_array("administrator", $user_roles)) {
                                                                            $ur_check = 1;
                                                                        }
                                                                        if ($ur_check == 1) {
                                                                            ?>
                                                                            <a href="<?php echo esc_url(site_url()); ?>/wp-admin/post.php?post=<?php echo esc_html($post_id); ?>&action=edit" class='edit-button'><input type="submit" class=" front-primary-btn pto-button-setting" value="Edit"></a>
                                                                            <?php
                                                                        } else {
                                                                            
                                                                            if( $request_allow_or_not == "on" ){
                                                                                ?>
                                                                                <a class='edit-button'><input type="submit" class="project-access-btn front-primary-btn pto-button-setting" data-id="<?php echo esc_html($post_id); ?>" id="project-access-btn" value="Request access"></a>
                                                                                <?php
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            } ?>
                                                            <a href="<?php echo esc_url(get_permalink($post_id)); ?>">
                                                            
                                                            <h4 class="post-title pto-header-four"><?php the_title(); ?></h4></a>
                                                            <?php echo "</a>"; ?>
                                                        </div>
                                                    </div>
                                                </li>
                                                <?php
                                            }
                                        }
                                    }
                                }
                                if ($permision_assign == 1) {

                                    $user = wp_get_current_user();

                                    $user->add_cap('edit_posts');
                                    $user->add_cap('edit_others_posts');
                                    $user->add_cap('publish_posts');
                                    $user->add_cap('manage_categories');
                                    $user->add_cap('edit_published_posts');
                                    //$user->add_role('editor');
                                }
                                wp_reset_postdata();
                                ?>
                            </ul>
                        </div>
                        <div id="my-projects" class="tab-content">
                            <ul class="projects-list-block">
                                <?php
                                $c_user_id = get_current_user_id();
                                $args = array(
                                    'post_type' => 'pto-project',
                                    'orderby'    => 'ID',
                                    'post_status' => 'publish',
                                    'order'    => 'DESC',
                                    'posts_per_page' => -1, // this will retrive all the post that is published 
                                );
                                $result = new \WP_Query($args);

                                if ($result->have_posts()) {
                                    while ($result->have_posts()) {
                                        $result->the_post();
                                        $post_id =  get_the_ID();
                                        $c_user_id = get_current_user_id();
                                        $author_id = get_post_field('post_author', $post_id);
                                        $get_user_req_post = get_post_meta($post_id, 'pto_project_user_id', true);
                                        if (empty($get_user_req_post)) {
                                            $get_user_req_post = array();
                                        }
                                        $req_ids = array();
                                        $img = get_the_post_thumbnail_url(get_the_ID());
                                        if (empty($img)) {
                                            $img = PTO_NB_PLUGIN_PATH . "assets/images/noimg-low.png";
                                        }
                                        foreach ($get_user_req_post as $reqest_id) {
                                            $req_ids[$reqest_id] = $reqest_id;
                                        }
                                        $get_show_project_link = get_post_meta($post_id, "show_projects_chk", true);

                                        if ($get_show_project_link == "1") {

                                            if (($c_user_id == $author_id) || (array_key_exists($c_user_id, $req_ids))) {
                                                $get_project_due_date = get_post_meta($post_id, "pto_project_due_date", "true");
                                                $duedate =  strtotime($get_project_due_date);
                                                $current_date =  strtotime(date("m/d/Y"));
                                                $due_check = 0;
                                                if ($due_check == 0) {
                                                    ?>
                                                    <li class="single-project-list">
                                                        <div class="single-project-block">
                                                            <div class="small-priject-banner-img">
                                                                <img src="<?php echo esc_html($img);  ?>">
                                                            </div>
                                                            <div class="single-project-info">


                                                                <a href="<?php echo esc_url(site_url()); ?>/wp-admin/post.php?post=<?php echo esc_html($post_id); ?>&action=edit" class='edit-button'><input type="submit" class=" front-primary-btn pto-button-setting" id="project-access-btn" value="Edit"></a>

                                                                <a href=" <?php echo esc_url(get_post_permalink(get_the_ID())); ?> ">
                                                                    <h4 class="post-title pto-header-four"><?php the_title(); ?></h4>
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </li>
                                                    <?php
                                                }
                                            }
                                        }
                                    }
                                }
                                wp_reset_postdata();
                                ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="search-projects-block">
                    <div class="search-projects">
                        <h3 class="pto-header-three" for="search"><?php esc_html_e('Projects Search', PTO_NB_MYPLTEXT); ?></h3>
                        <div class="search-projects-form-box">
                            <div class="cust-field input-field">
                                <label for="search" class="pto_text_setting"><?php esc_html_e('Search for a project name', PTO_NB_MYPLTEXT); ?></label>
                                <input type="search" id="search-project" data class="s-project" name="search-project">
                            </div>

                            <input type="submit" value='submit' class="search-project-btn front-primary-btn pto-button-setting" id="search-project-btn" style="margin-top:5px;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
}


function override_project_all_listing_shortcode() {
    if (shortcode_exists('project-all-listing')) {
        remove_shortcode('project-all-listing'); 
		add_shortcode('project-all-listing', 'custom_project_all_listing_shortcode');
    }
     
	
}
add_action('init', 'override_project_all_listing_shortcode');


function create_project_tag_taxonomy() {
    
    $labels = array(
        'name'              => 'Task Tags',
        'singular_name'     => 'Task Tag',
        'search_items'      => 'Search Task Tags',
        'all_items'         => 'All Task Tags',
        'parent_item'       => 'Parent Task Tag',
        'parent_item_colon' => 'Parent Task Tag:',
        'edit_item'         => 'Edit Task Tag',
        'update_item'       => 'Update Task Tag',
        'add_new_item'      => 'Add New Task Tag',
        'new_item_name'     => 'New Task Tag Name',
        'menu_name'         => 'Task Tags',
    );

    
    register_taxonomy(
        'task_tag', 
        'pto-tasks', 
        array(
            'hierarchical' => false,
            'labels'        => $labels,
            'show_ui'       => true,
            'show_admin_column' => true,
            'query_var'     => true,
            'rewrite'       => array('slug' => 'task_tag'),
        )
    );
	
	
	add_post_type_support('pto-tasks', 'thumbnail');
}


add_action('init', 'create_project_tag_taxonomy');




function save_project_tag_terms($post_id) {
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return $post_id;

   
    if ('pto-tasks' !== get_post_type($post_id)) {
        return $post_id;
    }

    
    if (isset($_POST['tax_input']['task_tag'])) {
        $task_tags = $_POST['tax_input']['task_tag'];
       
        wp_set_post_terms($post_id, $task_tags, 'task_tag');
    }

    return $post_id;
}
add_action('save_post', 'save_project_tag_terms');
function override_project_custom_template(){
if (class_exists('ptoffice\classes\WPNB_Posthooks')) {
    class WPNB_Posthooks_Extended extends \ptoffice\classes\WPNB_Posthooks {
        public function __construct() {
            parent::__construct(); 
        }

        
        public function wpnb_custom_template_include_new($template) {
            global $post;

					if (!empty($post)) {
						if ($post->post_type == "pto-project") {
							$file = PTO_NB_PLUGIN_PATHS_ADDON . 'single-pto-project.php';
							$template = $file;
						}
					}
				
					return $template;
				}
        }
    

    $extended_instance = new WPNB_Posthooks_Extended();

   
    if (has_filter('template_include', array('ptoffice\classes\WPNB_Posthooks', 'wpnb_custom_template_include'))) {
        remove_filter('template_include', array('ptoffice\classes\WPNB_Posthooks', 'wpnb_custom_template_include'), 99);
    }

   
    add_filter('template_include', array($extended_instance, 'wpnb_custom_template_include_new'), 99);
	}
}

add_action('init', 'override_project_custom_template');

add_action('add_meta_boxes', 'replace_existing_meta_box', 1111);

function replace_existing_meta_box() {
    
    remove_meta_box('custom_meta_box-key_view', 'pto-project', 'normal');

    
    add_meta_box(
        'custom_meta_box-new_key_view',  
         'Key Information <i class="fa fa-info-circle fas-tooltip" title="Want the next project chair to see important information for your notebook? Keep it all here for them to see "Key Information" as they begin taking over."></i>', 
        'custom_meta_box_callback_key_view',  
        'pto-project', 
        'normal',  
        'low'  
    );
}


function custom_meta_box_callback_key_view($post) {
   global $post;
        $post_meta = get_post_meta($post->ID,'keyinformation' , true);
        $key_check = get_post_meta( $post->ID , 'pto-key-view' , true );
        if ($post_meta){
            $content = $post_meta;
        }else{
            $content = "";
        }
        wp_editor($content,'keyinfo' , $settings = array(
           'textarea_name' =>'keyinformation' ,
           'textarea_rows' => 10
       ));
       ?>
       <div class="pto-publish-tab-frontend">
      
        <input type="checkbox" name="pto-key-view" <?php if( $key_check == "on" ){ echo "checked"; } ?>>

        <label>Show key information tab on public view &nbsp;<i class="fa fa-info-circle fas-tooltip" title="Checking this option will show this section's details on the front-end view of this project notebook. If you do not wish for this section to be visible on the front-end, leave this option unchecked."></i></label>
        
        	<label style="float: right;" class="keyinformation_ai">AI</label>
        
        
    </div>
    <?php
}


add_action('admin_enqueue_scripts', 'enqueue_custom_js_script');

function enqueue_custom_js_script() {
    $screen = get_current_screen();
    if ($screen->post_type === 'pto-project') {
        wp_enqueue_script('project-script', plugin_dir_url(__FILE__) . 'assets/js/project-script.js', array('jquery'), null, true);
    }
	wp_localize_script('project-script', 'customScriptVars', array(
        'ajax_url' => admin_url('admin-ajax.php'), 
        'nonce'    => wp_create_nonce('refine_content_nonce'), 
    ));
}

add_action('wp_ajax_refine_and_reword_content', 'refine_and_reword_content');

function refine_and_reword_content() {
    
    if (!isset($_POST['nonce']) || !wp_verify_nonce($_POST['nonce'], 'refine_content_nonce')) {
        wp_send_json_error(array('message' => 'Security check failed.'));
    }

    
    $content = isset($_POST['content']) ? sanitize_text_field($_POST['content']) : '';

    
    if (empty($content)) {
        wp_send_json_error(array('message' => 'No content provided.'));
    }

    
    $refined_content = call_openai_to_refine_content($content);

   
    if ($refined_content) {
        wp_send_json_success(array('refined_content' => $refined_content));
    } else {
        wp_send_json_error(array('message' => 'Failed to refine content.'));
    }
}

function call_openai_to_refine_content($content) {
    $api_key = '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';  
    $url = 'https://api.openai.com/v1/chat/completions';  

   
    $prompt = "Refine and reword the following text to make it clearer, more concise, and more professional:\n\n" . $content;

   
    $request_data = array(
        'model' => 'gpt-3.5-turbo',  
        'messages' => [
            [
                'role' => 'system',
                'content' => 'You are a helpful assistant who rewords and refines content to be clear, concise, and professional.'
            ],
            [
                'role' => 'user',
                'content' => $prompt
            ]
        ],
        'max_tokens' => 500,  
        'temperature' => 0.7,  
    );

   
    $response = wp_remote_post($url, array(
        'method'    => 'POST',
        'body'      => json_encode($request_data),
        'headers'   => array(
            'Content-Type'  => 'application/json',
            'Authorization' => 'Bearer ' . $api_key,
        ),
    ));

    
    if (is_wp_error($response)) {
        return false; 
    }

   
    $data = json_decode(wp_remote_retrieve_body($response), true);
	//echo "<pre>";
//print_r($data);exit;
    
    if (isset($data['choices'][0]['message']['content'])) {
        return trim($data['choices'][0]['message']['content']);
    }

    return false;  
}

