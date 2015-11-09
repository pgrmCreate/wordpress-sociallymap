<div class="wrap">
	<h1>
		Mes RSS
		<a href="?page=sociallymap-rss-add" class="page-title-action">Ajouter une entité</a>
	</h1>

	<div class="sociallymap_containRSS">
		<table class="wp-list-table widefat fixed striped users">
			<thead>
				<tr>
					<td id="cb" class="manage-column column-cb check-column">
						<label class="screen-reader-text" for="cb-select-all-1">Select All</label>
						<input id="cb-select-all-1" type="checkbox">
					</td>

					<th scope="col" id="username" class="manage-column column-username column-primary sortable desc">
						<a href="http://localhost/plugins/wordpress/sociallymap/wp-admin/users.php?orderby=login&amp;order=asc">
							<span>RSS</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>

					<th scope="col" id="name" class="manage-column column-name sortable desc">
						<a href="http://localhost/plugins/wordpress/sociallymap/wp-admin/users.php?orderby=name&amp;order=asc">
							<span>Catégorie</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>

					<th scope="col" id="email" class="manage-column column-email sortable desc">
						<a href="http://localhost/plugins/wordpress/sociallymap/wp-admin/users.php?orderby=email&amp;order=asc">
							<span>Auteur</span>
							<span class="sorting-indicator"></span>
						</a>
					</th>

					<th scope="col" id="email" class="manage-column column-email sortable desc">
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
						<tr id="user-1">
							<th scope="row" class="check-column">
								<label class="screen-reader-text" for="user_1">Select root</label>
								<input type="checkbox" name="users[]" id="user_1" class="administrator" value="1">
							</th>
					
							<th>
								<?php echo $value['link']; ?>
							</th>

							<th>
								<?php echo $value['category']; ?>
							</th>			

							<th>
								<?php echo $value['author']; ?>
							</th>

							<th>
								<button class="button button-primary">
									<i class="dashicons-before dashicons-welcome-write-blog sociallymap-icon-button"></i>
								</button>
								
								<button class="button button-danger">
									<i class="dashicons-before dashicons-dismiss sociallymap-icon-button"></i>
								</button>
							</th>
						</tr>
					<?php
					}
					?>
			</tbody>
		</table>
	</div>
</div>