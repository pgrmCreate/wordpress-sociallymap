<?php
	$data = get_query_var('data');
	$listRss = $data['data'];
?>

<div class="wrap">
	<h1>
		Mes entités
		<a href="?page=sociallymap-rss-add" class="page-title-action">
			<i class="dashicons-before dashicons-plus-alt sociallymap-icon-link"></i>
			Ajouter une entité
		</a>
	</h1>

	<div class="sociallymap_containRSS">
		<table class="wp-list-table widefat fixed striped users">
			<form method="post">
				<input type="hidden" name="sociallymap_deleteRSS" value="1"/>
				<thead>
					<tr>
						<th scope="col" id="RSS" class="manage-column column-username column-primary sortable desc"
						colspan="2">
							<a href="">
								<span>Nom de l'entité</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>

						<th scope="col" id="entityId" class="manage-column column-username column-primary sortable desc"
						colspan="2">
							<a href="">
								<span>Identifiant de l'entité sociallymap</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>

						<th scope="col" id="category" class="manage-column column-name sortable desc"
						colspan="2">
							<a href="">
								<span>Catégorie</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>

						<th scope="col" id="author" class="manage-column column-email sortable desc"
						colspan="2">
							<a href="">
								<span>Auteur</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>

						<th scope="col" id="action" class="manage-column column-email sortable desc"
						colspan="2">
							<a href="">
								<span>Active</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>

						<th scope="col" id="action" class="manage-column column-email sortable desc"
						colspan="2">
							<a href="">
								<span>Dernière publication</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
					</tr>
				</thead>
				<tbody id="the-list" data-wp-lists="list:user">
					<?php
						foreach ($listRss as $key => $value) {
						?>
							<tr>
								<td colspan="2">
									<?php echo $value->name; ?>
									<div class="row-actions">
										<span class="edit">
											<a href="?page=sociallymap-rss-edit&id=<?php echo $value->id; ?>">
												Editer
											</a>
										</span>
										|
										<span class="delete">
											<button value="<?php echo $value->id; ?>" type="submit" name="submit"
											class="sm-link-action-style">
												<a>
													Effacer
												</a>
											</button>
										</span>
									</div>
								</td>

								<td colspan="2">
									<?php echo $value->sm_entity_id; ?>
								</td>

								<td colspan="2">
									<ul class="display-list-cat">
									<?php
										$listCat = [];
										foreach ($value->options as $key => $category) {
											if($category->options_id == '1') {
												$catName = get_the_category_by_ID((integer)$category->value);
												?>
														<li> <?php echo $catName; ?> </li>
												<?php
											}
										}
									?>
									</ul>
								</td>

								<td colspan="2">
									<?php echo (get_user_by('id', $value->author_id)->user_nicename); ?>
								</td>

								<td colspan="2">
									<input type="checkbox" disabled <?php if($value->activate == true) echo "checked"; ?> >
								</td>

								<td colspan="2">
									<?php echo $value->last_published_message; ?>
								</td>
							</tr>
						<?php
						}
						?>
				</tbody>
				<?php if ((int)count((array)$listRss) > 1) : ?>
				<tfoot>
					<tr>
						<th scope="col" id="RSS" class="manage-column column-username column-primary sortable desc"
						colspan="4">
							<a href="http://localhost/plugins/wordpress/sociallymap/wp-admin/users.php?orderby=login&amp;order=asc">
								<span>Nom de l'entité</span>
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
								<span>Active</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>

						<th scope="col" id="action" class="manage-column column-email sortable desc"
						colspan="2">
							<a href="http://localhost/plugins/wordpress/sociallymap/wp-admin/users.php?orderby=email&amp;order=asc">
								<span>Dernière publication</span>
								<span class="sorting-indicator"></span>
							</a>
						</th>
					</tr>
				</tfoot>
				<?php endif; ?>
			<form>
		</table>
	</div>
</div>


