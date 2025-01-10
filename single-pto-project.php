<?php

/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Twenty_Nineteen
 * @since Twenty Nineteen 1.0
 */

get_header();
?>

<div id="primary" class="content-area" wdaddad>
	<main id="main" class="site-main">
		<div class="main-project-lists pto-project-single-page-sec">
			<div class="container">
				<?php
				// Start the Loop.
				while (have_posts()) :
					the_post();
					$post_id = get_the_id();
					delete_post_meta($post_id, 'cpt_selected_data', " ", true);
					$all_post_data = get_post_meta($post_id, "pto_sub_menu_cpt_add", true);
					$selected_cpt = get_post_meta($post_id, "cpt_selected_data", true);
					$all_post_data = json_decode($all_post_data);
					$all_post_data = (array) $all_post_data;
					/* check publish or not */

					$metting = get_post_meta($post_id, "pto-meeting-view", true);
					$budget_item = get_post_meta($post_id, "pto-budget-items-view", true);
					$tasks = get_post_meta($post_id, "pto-tasks-view", true);
					$note = get_post_meta($post_id, "pto-note-view", true);
					$kanban = get_post_meta($post_id, "pto-kanban-view", true);
					$key = get_post_meta($post_id, "pto-key-view", true);

					/* end */
					$keyinformation = get_post_meta($post_id, 'keyinformation', true);
					$pto_metting = $pto_budget = $pto_task = $pto_notes = $kanban_board = array();
					if (!empty($all_post_data)) {

						foreach ($all_post_data as $cpt_id) {
							$post_type = get_post_type($cpt_id);
							if ($post_type == "pto-meeting") {
								$pto_metting[$cpt_id] = $cpt_id;
							} else if ($post_type == "pto-tasks") {
								$pto_task[$cpt_id] = $cpt_id;
							} else if ($post_type == "pto-note") {
								$pto_notes[$cpt_id] = $cpt_id;
							} else if ($post_type == "pto-kanban") {
								$kanban_board[$cpt_id] = $cpt_id;
							} else if ($post_type == "pto-budget-items") {
								$pto_budget[$cpt_id] = $cpt_id;
							}
						}
					}
					?>
					<ul class="pto-front-tabs">
						<?php if ($key == "on"  && array_key_exists("Key", $selected_cpt)) { ?>
							<li>
								<h2 class="pto-header-two"><button class="pto-front-tablinks" onclick="openTabProject(event, 'Key_Information')" id="defaultOpen">
									<?php esc_html_e("Key Information", PTO_NB_MYPLTEXT); ?> </button></h2>
								</li>
							<?php }
							if ($metting == "on" && array_key_exists("pto-meeting", $selected_cpt)) { ?>
								<li>
									<h2 class="pto-header-two"><button class="pto-front-tablinks" onclick="openTabProject(event, 'Meeting_Minutes')" id="defaultOpen"><?php esc_html_e("Meeting Minutes", PTO_NB_MYPLTEXT); ?> </button></h2>
								</li>
							<?php }
							if ($tasks == "on" && array_key_exists("pto-tasks", $selected_cpt)) { ?>
								<li>
									<h2 class="pto-header-two"><button class="pto-front-tablinks" onclick="openTabProject(event, 'Task_Management')" id="defaultOpen">
										<?php esc_html_e("Task Management", PTO_NB_MYPLTEXT); ?> </button></h2>
									</li>
								<?php }
								if ($note == "on" && array_key_exists("Notes", $selected_cpt)) { ?>
									<li>
										<h2 class="pto-header-two"><button class="pto-front-tablinks" onclick="openTabProject(event, 'Notes')" id="defaultOpen"> <?php esc_html_e("Notes", PTO_NB_MYPLTEXT); ?> </button></h2>
									</li>
								<?php }
								if ($budget_item == "on" && array_key_exists("Budget", $selected_cpt)) { ?>
									<li>
										<h2 class="pto-header-two"><button class="pto-front-tablinks" onclick="openTabProject(event, 'Budget')" id="defaultOpen"><?php esc_html_e("Budget", PTO_NB_MYPLTEXT); ?></button></h2>
									</li>
								<?php }
								if ($kanban == "on" && array_key_exists("pto-kanban", $selected_cpt)) { ?>
									<li>
										<h2 class="pto-header-two"><button class="pto-front-tablinks" onclick="openTabProject(event, 'Kanban')" id="defaultOpen"><?php esc_html_e("Kanban", PTO_NB_MYPLTEXT); ?> </button></h2>
									</li>
								<?php } ?>
							</ul>
							<?php if (!empty($keyinformation)) { ?>
								<?php if ($key == "on") { ?>
									<div id="Key_Information" class="pto-front-tabcontent">
										<?php
										print_r(apply_filters('the_content', $keyinformation));
										?>
									</div>
								<?php }
							}
							if (!empty($pto_metting)) {
								if ($metting == "on" && array_key_exists("pto-meeting", $selected_cpt)) { ?>
									<div id="Meeting_Minutes" class="pto-front-tabcontent">
										<div class="pto-project-filter">
											<select name="metting-filter" id="metting-filter" post-id="<?php echo esc_html($post_id); ?>">
												<option value=''>Please select one</option>
												<option value='name'>Meeting Name</option>
												<option value='date'>Date</option>
											</select>
										</div>
										<ul class='projects-list-block'>
											<?php
											foreach ($pto_metting as $meeting_id) {
												if (get_post_status($meeting_id) == "publish") {
													?>
													<li class='single-project-list'>
														<div class='small-priject-banner-img'><img src='<?php echo esc_html_e(PTO_NB_PLUGIN_PATH) ?>assets/images/file_icons.png' height='150' width='150'></div>
														<div class='single-project-info'><h4 class='pto-project-metting-list-single_title pto-header-four' data-id="<?php echo intval($meeting_id); ?>"><?php echo esc_html_e(get_the_title($meeting_id)); ?></h4>
															<p class='pto-project-metting-list-single_publish_date'><?php echo esc_html_e(get_the_date('m-d-Y', $meeting_id)); ?></p></div>
														</li>
														<?php
													}
												}
												?>
											</ul>


										</div>
									<?php }
								}
								if (!empty($pto_task)) {
									if ($tasks == "on"  && array_key_exists("pto-tasks", $selected_cpt)) {   ?>
										<div id="Task_Management" class="pto-front-tabcontent">
											<div class="table-responsive">
												<table class="pto-project-task-slots wp-list-table widefat striped table-view-list posts pto-project-design">
													<thead>
														<th class="manage-column column-title column-primary sorted desc">
															<h4 class="pto-admin-two"><?php esc_html_e("Task Name", PTO_NB_MYPLTEXT); ?></h4>
														</th>
														<th class="manage-column column-title column-primary sorted desc">
															<h4 class="pto-admin-two"><?php esc_html_e("Assigned To", PTO_NB_MYPLTEXT); ?></h4>
														</th>
														<th class="manage-column column-title column-primary sorted desc">
															<h4 class="pto-admin-two"><?php esc_html_e("Due Date", PTO_NB_MYPLTEXT); ?></h4>
														</th>
                                                        <th class="manage-column column-title column-primary sorted desc">
															<h4 class="pto-admin-two"><?php esc_html_e("Attachments", PTO_NB_MYPLTEXT); ?></h4>
														</th>
														<th class="manage-column column-title column-primary sorted desc">
															<h4 class="pto-admin-two"><?php esc_html_e("Status", PTO_NB_MYPLTEXT); ?></h4>
														</th>
													</thead>
													<tbody>
														<?php

														foreach ($pto_task as $pto_task_id) {
															$status = get_post_status($pto_task_id);
															$pto_user_assign_key = get_post_meta($pto_task_id, "pto_user_assign_key", true);
															$pto_task_due_date = get_post_meta($pto_task_id, "pto_task_due_date", true);
															$pto_task_status = get_post_meta($pto_task_id, "pto_task_status", true);
															$attachment_id = get_post_thumbnail_id($pto_task_id);
															if ($attachment_id) {
																$image_url = wp_get_attachment_url($attachment_id);
															}else{
																$image_url = "";
															}
															// $pto_task_status = "<span class='" . $pto_task_status . "'>" . $pto_task_status . "</span>";
															if ($status == "publish") {
																$all_username = "";
																if (!empty($pto_user_assign_key)) {
																	foreach ($pto_user_assign_key as $userid) {
																		$user = get_user_by('id', $userid);
																		$first_name = get_user_meta($userid, 'first_name', true);
																		$last_name = get_user_meta($userid, 'last_name', true);
																		$full_name = $first_name . " " . $last_name;
																		if ($full_name != " ") {
																			$all_username .= $full_name . " - ";
																		} else {
																			$all_username .= $user->data->display_name . " - ";
																		}
																	}
																}
																$remove_last_char = substr($all_username, 0, -2);
																$due_date = "";
																if (!empty($pto_task_due_date)) {
																	if (array_key_exists('due_date', $pto_task_due_date)) {
																		$due_date = $pto_task_due_date['due_date'];
																	}
																}
																?>
																<tr>
																	<td><p><?php echo esc_html_e(get_the_title($pto_task_id)) ?></p></td>
																	<td><p><?php echo esc_html_e($remove_last_char); ?> </p></td>
																	<td><p> <?php echo esc_html_e($due_date); ?> </p></td>
                                                                    <?php 
																	if(!empty($image_url)){
																	?>
                                                                    <td><p> <a href="<?php echo $image_url;?>"  target="_blank"><span class="dashicons dashicons-format-image"></span></a>
                                                                     <a href="<?php echo $image_url;?>" download><span class="dashicons dashicons-download"></span></a></p></td>
                                                                    <?php }else{?>
                                                                     <td><p></p></td>
                                                                    <?php }?>
																	<td><p><span class="<?php echo esc_html_e($pto_task_status); ?>"><?php echo esc_html_e($pto_task_status); ?> </span> </p></td>
																</tr>
																<?php
															}
														}
														?>
													</tbody>
												</table>
											</div>
										</div>
									<?php }
								}
								if (!empty($pto_notes)) {
									if ($note == "on" && array_key_exists("Notes", $selected_cpt)) {   ?>
										<div id="Notes" class="pto-front-tabcontent">
											<div class="pto-project-filter">
												<select name="notes-filter" id="notes-filter" post-id="<?php echo esc_html($post_id); ?>">
													<option value=''>Please select one</option>
													<option value='name'>Notes Name</option>
													<option value='date'>Date</option>
												</select>
											</div>
											<ul class='projects-list-block'>
												<?php
												foreach ($pto_notes as $pto_notes_id) {
													$status =  get_post_status($pto_notes_id);

													if( $status == "publish" ){
														?>
														<li class='single-project-list'>
															<div class='small-priject-banner-img'><img src='<?php echo esc_html_e(PTO_NB_PLUGIN_PATH); ?>assets/images/file_icons.png' height='150' width='150'></div>
															<div class='single-project-info'><h4 class='pto-project-notes-list-single_title pto-header-four' data-id="<?php echo intval($pto_notes_id); ?>"><?php echo esc_html_e(get_the_title($pto_notes_id)); ?> </h4>
																<p class='pto-project-notes-list-single_publish_date'><?php echo esc_html_e(
																	get_the_date('m-d-Y', $pto_notes_id)) ?></p></div>
																</li>
																<?php
															}
														}
														?>
													</ul>
												</div>
											<?php }
										}
										if (!empty($pto_budget)) {
											if ($budget_item == "on" && array_key_exists("Budget", $selected_cpt)) {  ?>
												<div id="Budget" class="pto-front-tabcontent">
													<div class="pto-store-total-budgets">
														<?php
														$budget_price = get_post_meta($post->ID, "pto_total_budgets", true);
														$nombre_format_francais = number_format($budget_price, 2, '.', ',');
														?>
														<label><?php esc_html_e("Starting Budget", PTO_NB_MYPLTEXT); ?> :</label>
														<h6 class="pto-header-six"> $<?php echo esc_html($nombre_format_francais); ?></h6>
													</div>
													<div class="table-responsive">
														<table class="pto-project-task-slots test wp-list-table widefat  striped table-view-list posts pto-project-design">
															<thead>
																<th class="manage-column column-title column-primary sorted desc">
																	<h5 class="pto-header-five"><?php esc_html_e("Budget Item Name", PTO_NB_MYPLTEXT); ?></h5>
																</th>
																<th class="manage-column column-title column-primary sorted desc">
																	<h5 class="pto-header-five"><?php esc_html_e("Description", PTO_NB_MYPLTEXT); ?></h5>
																</th>
																<th class="manage-column column-title column-primary sorted desc">
																	<h5 class="pto-header-five"><?php esc_html_e("Amount", PTO_NB_MYPLTEXT); ?></h5>
																</th>
															</thead>
															<tbody>
																<?php
																foreach ($pto_budget as $pto_budget_id) {
																	$status = get_post_status($pto_budget_id);
																	if ($status == "publish") {
																		$budget_cur = get_post_meta($pto_budget_id, "budget_items_type_value", true);
																		$budget_price_current = get_post_meta($pto_budget_id, "budget_items_type_value", true);
																		$budget_price_current = number_format($budget_price_current, 2, '.', ',');
																		$cpt_single_data = get_post($pto_budget_id);
																		$budget_price_type = get_post_meta($pto_budget_id, "budget_items_type", true);
																		if ($budget_price_type == "expense") {
																			$budget_price = $budget_price - $budget_cur;
																		} else {
																			$budget_price = $budget_price + $budget_cur;
																		}
																		if ($status  == "publish") {
																			$content =  mb_strimwidth($cpt_single_data->post_content, 0, 400, "...");
																			?>
																			<tr>	
																				<td><p><?php echo esc_html_e(get_the_title($pto_budget_id))?> </p></td>
																				<td><p><?php print_r(apply_filters('the_content', $content)); ?></p></td>
																				<td><p>
																					<?php
																					if ($budget_price_type == "expense") {
																						echo "-$" . number_format($budget_price_current,2); 
																					}else{
																					 	echo "$" . number_format($budget_price_current,2); 
																					}
																					?>
																						
																					</p></td>
																			</tr>
																			<?php
																		}
																	}
																}
																?>
															</tbody>
														</table>
													</div>
													<div class="pto-project-frontend-budget-total">
														<label>Budget Remaining:</label>
														<?php
														$budget_price = number_format($budget_price, 2, '.', ',');
														?>
														<h6 class='pto-header-six'>$<?php echo esc_html_e(number_format($budget_price,2)); ?></h6>
													</div>

												</div>
											<?php }
										}

										if ($kanban == "on" && array_key_exists("pto-kanban", $selected_cpt)) {  				  ?>
											<div id="Kanban" class="pto-front-tabcontent inside">
												<div class="wp-admin pto-custom-style">
													<div class="task-kan-ban kanban-status-list">
														<?php
														foreach ($kanban_board as $kanban_board_id) {
										$all_post_data = get_post_meta($post_id, "pto_sub_menu_cpt_add", true); // get sub post data
										$get_assign3 = json_decode($all_post_data);
										$get_assign3 = (array) $get_assign3;  //convert json to array cpt ids
										$the_posts = get_posts(array('post_type' => 'pto-kanban','posts_per_page'=>'-1')); //get task cpt data
										$url_add = site_url() . "/wp-admin/post-new.php?post_type=pto-kanban&proj_id=$post_id"; //create url fronm post add
										$post_ids = get_post_meta($post_id, "pto_sub_menu_cpt_add", true);
										$ids = explode(",", $post_ids);
										$ids_arr = array();
										foreach ($ids as $ids_task) {
											$ids_arr[$ids_task] = $ids_task;
										}
									}
									$no_exists_value =  get_post_meta($post_id, 'pto_kanban_status', true); // get all options from task
									$i = 0;
									if (empty($the_posts)) {
										$the_posts = array();
									}

									foreach ($no_exists_value as $key => $pto_task_status) {
										foreach ($pto_task_status as $key2 => $data) {
											?>
											<div class="sortable-data connectedSortables" data-id="<?php echo esc_html($key2); ?>" data-ids-index="<?php echo esc_html($key); ?>">
												<div class="title"> <?php echo esc_html_e($data); ?> <span class="plus-icon"></span></div>

												<ul id="<?php echo esc_html($key2); ?>" val="<?php echo esc_html($data); ?>" class="connectedSortable">
													<?php
													$i++;
													foreach ($the_posts as $single_post) {
														if (array_key_exists($single_post->ID, $get_assign3)) {
															$ass_id2 = get_post_meta($single_post->ID, 'pto_kanban_status', true);

															if ($key == $ass_id2) {
																echo "<li class='ui-state-default' data-id='" . intval($single_post->ID) . "'><p>" . sanitize_text_field($single_post->post_title) . "</p></li>";
															}
														}
													}
												}
												?>
											</ul>
										</div>
										<?php
									}
									?>
								</div>
							</div>
						<?php }
				endwhile; // End the loop.
				?>
			</div>
		</div>
	</div>

	<div class="custom-popup-meeting custom-popup">
		<div class="custom-popup-meetinginner custom-popup-inner">
			<div class='title-date'>
				<span class='data-title'>Meeting Description</span>
				<button class="custom-popup-close">X</button>
			</div>
			<div class="custom-popup-meetinginfo">

			</div>
		</div>
	</div>
	<div class="custom-popup-note custom-popup">
		<div class="custom-popup-noteinner custom-popup-inner">
			<div class='title-date'>
				<span class='data-title'>Note Description</span>
				<button class="custom-popup-close">X</button>
			</div>
			<div class="custom-popup-noteinfo">
			</div>
		</div>
	</div>
</main><!-- #main -->
</div><!-- #primary -->
<script type="text/javascript">
	/*jQuery(".sortable-data ul").each(function(){
    	let ids = jQuery(this).attr("id");
		jQuery( function() {
		    jQuery( "#"+ ids).sortable({
		      connectWith: ".connectedSortable",
		   	}).disableSelection();
		});
	})*/
</script>
<?php
get_footer();
