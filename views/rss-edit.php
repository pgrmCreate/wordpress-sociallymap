<?php
	$data = get_query_var('data');
	$editingEntity = $data['data'];
?>

<div class="wrap">
	<h1>
		Editer une nouvelle entité
	</h1>

	<form method="post" class="sociallymap_formRSS">
		<input type="hidden" name="sociallymap_updateRSS" value="1">

		<table class="form-table">
			<tbody>
				<tr class="form-field form-required">
					<th>
						<label>Label</label>
					</th>
					<td>
						<input name="sociallymap_label" value="<?php echo($editingEntity->name); ?>" 
						class="sociallymap_formRSS_newFlux" placeholder="Mon entité">
					</td>
				</tr>
				<tr class="form-field form-required">
					<th>
						<label>Identifiant de l'entité</label>
					</th>
					<td>
						<input name="sociallymap_entityId" value="<?php echo($editingEntity->sm_entity_id); ?>" 
						class="sociallymap_formRSS_newFlux" placeholder="Mon entité">
					</td>
				</tr>
				<tr class="form-field form-required">
					<th>
						<label>Catégorie cible de la publication</label>
					</th>
					<td>
						<?php foreach (get_categories(['hide_empty' => 0]) as $key => $value) { ?>	
							<label class="listCats">
							<?php echo $value->name;?>
								<input 	name="sociallymap_category[]" type="checkbox" 
										value="<?php echo get_cat_ID($value->name);?>"
								<?php 
									foreach ($editingEntity->options as $key => $currentOption) {
										if($currentOption->options_id == '1' && $value->cat_ID == $currentOption->value) {
											echo "checked";
										}
									} 
								?> >	
							</label>
						<?php } ?>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th>
						<label>Actif</label>
					</th>
					<td>
						<input name="sociallymap_activate" <?php if($editingEntity->activate) echo ('checked');?>
						class="sociallymap_formRSS_newFlux" type="checkbox" value="1">
					</td>
				</tr>
				<tr class="form-field form-required">
					<th>
						<label>Type de publication</label>
					</th>
					<td>
						<select name="sociallymap_publish_type">
							<option value="publish" <?php if($editingEntity->options[2]->value == 'publish') echo ('selected');?> >Publier</option>
							<option value="draft" <?php if($editingEntity->options[2]->value == 'draft') echo ('selected');?>>Brouillon</option>
							<option value="pending" <?php if($editingEntity->options[2]->value == 'pending') echo ('selected');?>>En attente de relecture</option>
							<option value="private" <?php if($editingEntity->options[2]->value == 'private') echo ('selected');?>>Privée</option>
							<option value="future" <?php if($editingEntity->options[2]->value == 'future') echo ('selected');?>>En attente de publication</option>
						</select>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th>
						<label>Ouverture du lien</label>
					</th>
					<td>
						<select name="sociallymap_display_type">
							<option value="modal" <?php if($editingEntity->options[1]->value == 'modal') echo ('selected');?> >
								Fenêtre modale
							</option>
							<option value="tab" <?php if($editingEntity->options[1]->value == 'tab') echo ('selected');?> >
								Nouvel onglet
							</option>	
							<option value="page" <?php if($editingEntity->options[1]->value == 'page') echo ('selected');?> >
								Même page
							</option>
						</select>
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit sociallymap_valid-submit">
			<button type="submit" name="submit" id="submit" class="button button-primary">
				<i class="dashicons-before dashicons-update sociallymap-icon-button"></i>
				Mettre a jour le lien
			</button>
		</p>
	</form>
</div>