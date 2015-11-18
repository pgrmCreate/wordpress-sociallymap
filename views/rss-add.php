<?php
	$data = get_query_var('data');
	$default_value = $data['data'];
?>

<div class="wrap">
	<h1>
		Ajouter une nouvelle entité
		<a href="?page=sociallymap-rss-list" class="page-title-action">
			<i class="dashicons-before dashicons-list-view sociallymap-icon-link"></i>
			Voir la liste des entités
		</a>
	</h1>
	<form method="post" class="sociallymap_formRSS">
		<input type="hidden" name="sociallymap_postRSS" value="1"/>
	
		<table class="form-table">
			<tbody>
				<tr class="form-field form-required">
					<th>
						<label>Label</label>
					</th>
					<td>
						<input type="text" placeholder="Mon entité" name="sociallymap_name"
						class="sociallymap_formRSS_newFlux">
					</td>
				</tr>
				<tr class="form-field form-required">
					<th>
						<label>Identifiant de l'entité sociallymap</label>
					</th>
					<td>
						<input type="text" placeholder="Identifiant de l'entité sociallymap"
						name="sociallymap_entityId" class="sociallymap_formRSS_newFlux">
					</td>
				</tr>
				<tr class="form-field form-required">
					<th>
						<label>Categorie cible de la publication</label>
					</th>
					<td>
						<select name="sociallymap_category">
							<?php foreach (get_categories() as $key => $value) { ?>	
								<option value="<?php echo get_cat_ID($value->name);?>"
								<?php if($value->cat_ID === $default_value->category) echo "selected" ?> >
								<?php echo $value->name;?></option>
							<?php } ?>
						</select>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th>
						<label>Activer</label>
					</th>
					<td>
						<input type="checkbox" name="sociallymap_activate" class="sociallymap_formRSS_newFlux" value="1">
					</td>
				</tr>
			</tbody>
		</table>

		<p class="submit sociallymap_valid-submit">
			<button type="submit" name="submit" id="submit" class="button button-primary">
				<i class="dashicons-before dashicons-plus-alt sociallymap-icon-button"></i>
				Ajouter
			</button>
		</p>
	</form>
</div>