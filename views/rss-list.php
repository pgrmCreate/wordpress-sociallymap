<?php
	$data = get_query_var('data');
	$listRss = $data['data'];
?>

<div class="wrap">
	<h1>
		Mes RSS
		<a href="?page=sociallymap-rss-add" class="page-title-action">
			<i class="dashicons-before dashicons-plus-alt sociallymap-icon-link"></i>
			Ajouter une entité
		</a>
	</h1>

	<div class="sociallymap_containRSS">
		<table class="wp-list-table widefat fixed striped users">
			<form method="post">
				<input type="hidden" name="sociallymap_deleteRSS" value="1"/>
				<tdead>
					<tr>
						<th id="cb" class="manage-column column-cb check-column">
							<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
							<input id="cb-select-all-1" type="checkbox">
						</th>

						<th scope="col" id="RSS" class="manage-column column-username column-primary sortable desc"
						colspan="4">
							<a href="http://localhost/plugins/wordpress/sociallymap/wp-admin/users.php?orderby=login&amp;order=asc">
								<span>Nom du flux RSS</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>

						<th scope="col" id="category" class="manage-column column-name sortable desc"
						colspan="2">
							<a href="http://localhost/plugins/wordpress/sociallymap/wp-admin/users.php?orderby=name&amp;order=asc">
								<span>Catégorie</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>

						<th scope="col" id="author" class="manage-column column-email sortable desc"
						colspan="2">
							<a href="http://localhost/plugins/wordpress/sociallymap/wp-admin/users.php?orderby=email&amp;order=asc">
								<span>Auteur</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>

						<th scope="col" id="action" class="manage-column column-email sortable desc"
						colspan="2">
							<a href="http://localhost/plugins/wordpress/sociallymap/wp-admin/users.php?orderby=email&amp;order=asc">
								<span>Action</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>	
					</tr>
				</tdead>
				<tbody id="the-list" data-wp-lists="list:user">
					<?php
						foreach ($listRss as $key => $value) {
						?>
							<tr>
								<td scope="row" class="check-column" colspan="0">
									<label class="screen-reader-text" for="user_1">Select root</label>
									<input type="checkbox" name="users[]" class="administrator" value="1">
								</td>
						
								<td colspan="4">
									<b>
										# <?php echo $value->id; ?>
									</b>
									<?php echo $value->name; ?>
								</td>

								<td colspan="2">
									<?php echo (get_the_category_by_ID($value->options[0]->value)); ?>
								</td>			

								<td colspan="2">
									<?php echo (get_user_by('id', $value->author_id)->user_nicename); ?>
								</td>

								<td colspan="2">
									<a href="?page=sociallymap-rss-edit&id=<?php echo $value->id; ?>">
										<button class="button button-primary" type="button">
										<i class="dashicons-before dashicons-welcome-write-blog sociallymap-icon-button"></i>
										</button>
									</a>
									
									<button class="button button-primary danger" type="submit" name="submit"
									value="<?php echo $value->id; ?>">
										<i class="dashicons-before dashicons-dismiss sociallymap-icon-button"></i>
									</button>
								</td>
							</tr>
						<?php
						}
						?>
				</tbody>
			<form>
		</table>
	</div>
</div>