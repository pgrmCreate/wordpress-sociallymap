<?php
	$data = get_query_var('data');
	$listEntities = $data['data'];
?>

<div class="wrap">
	<h1>
		Mes entités
		<a href="?page=sociallymap-entity-add" class="page-title-action">
			<i class="dashicons-before dashicons-plus-alt sociallymap-icon-link"></i>
			Ajouter une entité
		</a>
	</h1>

	<div class="sociallymap_containEntity">
		<table class="wp-list-table widefat fixed striped users">
			<form method="post">
				<input type="hidden" name="sociallymap_deleteEntity" value="1"/>
				<thead>
					<tr>
						<th scope="col" id="Entity" class="manage-column column-username"
						colspan="2">
							Nom de l'entité
						</th>

						<th scope="col" id="entityId" class="manage-column column-username"
						colspan="4">
							Identifiant de l'entité sociallymap
						</th>

						<th scope="col" id="category" class="manage-column column-name"
						colspan="2">
							Catégorie
						</th>

						<th scope="col" id="author" class="manage-column column-email"
						colspan="2">
							Auteur
						</th>

						<th scope="col" id="action" class="manage-column column-email"
						colspan="1">
							Active
						</th>

						<th scope="col" id="action" class="manage-column column-email"
						colspan="2">
							Dernière publication
						</th>
					</tr>
				</thead>
				<tbody id="the-list" data-wp-lists="list:user">
					<?php
						foreach ($listEntities as $key => $value) {
						?>
							<tr>
								<td colspan="2">
									<?php echo $value->name; ?>
									<div class="row-actions">
										<span class="edit">
											<a href="?page=sociallymap-entity-edit&id=<?php echo $value->id; ?>">
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

								<td colspan="4">
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

								<td colspan="1">
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
				<?php if ((int)count((array)$listEntities) > 3) : ?>
				<tfoot>
					<tr>
						<th scope="col" id="Entity" class="manage-column column-username"
						colspan="2">
							Nom de l'entité
						</th>

						<th scope="col" id="entityId" class="manage-column column-username"
						colspan="4">
							Identifiant de l'entité sociallymap
						</th>

						<th scope="col" id="category" class="manage-column column-name"
						colspan="2">
							Catégorie
						</th>

						<th scope="col" id="author" class="manage-column column-email"
						colspan="2">
							Auteur
						</th>

						<th scope="col" id="action" class="manage-column column-email"
						colspan="1">
							Active
						</th>

						<th scope="col" id="action" class="manage-column column-email"
						colspan="2">
							Dernière publication
						</th>
					</tr>
				</tfoot>
				<?php endif; ?>
			<form>
		</table>
	</div>
</div>


