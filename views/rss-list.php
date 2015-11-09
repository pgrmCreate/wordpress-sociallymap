<div class="wrap">
	<h1>
		Mes RSS
		<a href="?page=sociallymap-rss-add" class="page-title-action">Ajouter une entité</a>
	</h1>

	<div class="sociallymap_containRSS">
		<table class="wp-list-table widefat fixed striped users">
			<form method="post">
				<?php settings_fields('sociallymap_publisher_deleteRSS') ?>
				<input type="hidden" name="sociallymap_updateRSS" value="1"/>
				<thead>
					<tr>
						<td id="cb" class="manage-column column-cb check-column">
							<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
							<input id="cb-select-all-1" type="checkbox">
						</td>

						<th scope="col" id="RSS" class="manage-column column-username column-primary sortable desc"
						colspan="6">
							<a href="http://localhost/plugins/wordpress/sociallymap/wp-admin/users.php?orderby=login&amp;order=asc">
								<span>RSS</span>
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
				</thead>
				<tbody id="the-list" data-wp-lists="list:user">
					<?php
						$listRSS = get_option('sociallymap_addRSS_listingRSS');
						foreach ($listRSS as $key => $value) {
						?>
							<tr id="user-1" >
								<th scope="row" class="check-column" colspan="0">
									<label class="screen-reader-text" for="user_1">Select root</label>
									<input type="checkbox" name="users[]" id="user_1" class="administrator" value="1">
								</th>
						
								<th colspan="6">
									<b>
										#<?php echo $value['id']; ?>
									</b>
									<?php echo $value['link']; ?>
								</th>

								<th colspan="2">
									<?php echo $value['category']; ?>
								</th>			

								<th colspan="2">
									<?php echo $value['author']; ?>
								</th>

								<th colspan="2">
									<button class="button button-primary">
										<i class="dashicons-before dashicons-welcome-write-blog sociallymap-icon-button"></i>
									</button>
									
									<button class="button button-primary danger" type="submit" name="submit"
									value="<?php echo $value['id']; ?>">
										<i class="dashicons-before dashicons-dismiss sociallymap-icon-button"></i>
									</button>
								</th>
							</tr>
						<?php
						}
						?>
				</tbody>
			<form>
		</table>
	</div>
</div>