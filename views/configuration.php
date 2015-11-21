<?php 
	$configOption = new ConfigOption();
	$default_options = $configOption->getConfig();

	$data = get_query_var('data');
	$data = $data['data'];

		if(isset($data['isSaved']) ) {
			$isSaved = $data['isSaved'];
		} else  {
			$isSaved = false;
		}
	
?>

<?php
	if($isSaved == true) {
		?>
		<div id="message" class="updated notice is-dismissible">
			<p>
				Modification bien effectuée
			</p>
			<button type="button" class="notice-dismiss">
				<span class="screen-reader-text">Cacher l'information</span>
			</button>
		</div>	
		<?php
	}
?>

<h1>Configuration du plugin sociallymap </h1>

<div class="wrap">
	<form method="post" class="sociallymap_formRSS">
		<input type="hidden" name="sociallymap_updateConfig" value="1">
		
		<table class="form-table">
			<tbody>
				<tr class="form-field form-required">
					<th>
						<label>Catégorie cible de la publication</label>
					</th>
					<td>
						<select name="sociallymap_category">
							<?php foreach (get_categories() as $key => $value) { ?>	
							<option value="<?php echo $value->cat_ID;?>"
								<?php if($value->cat_ID == $default_options[0]->default_value) echo "selected" ?> >
								<?php echo $value->name;?></option>
								<?php } ?>
						</select>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th>
						<label>Ouverture du lien</label>
					</th>
					<td>
						<select name="sociallymap_display_type">
							<option value="modal" <?php if($default_options[1]->default_value == 'modal') echo ('selected');?> >
								Fenêtre modale
							</option>
							<option value="tab" <?php if($default_options[1]->default_value == 'tab') echo ('selected');?> >
								Nouvel onglet
							</option>	
						</select>
					</td>
				</tr>
				<tr class="form-field form-required">
					<th>
						<label>Publier en mode</label>
					</th>
					<td>
						<select name="sociallymap_publish_type">
							<option value="publish" <?php if($default_options[2]->default_value == 'publish') echo ('selected');?> >Publication</option>
							<option value="draft" <?php if($default_options[2]->default_value == 'draft') echo ('selected');?>>Brouillon</option>
							<option value="pending" <?php if($default_options[2]->default_value == 'pending') echo ('selected');?>>En attente de relecture</option>
							<option value="private" <?php if($default_options[2]->default_value == 'private') echo ('selected');?>>Privée</option>
							<option value="future" <?php if($default_options[2]->default_value == 'future') echo ('selected');?>>En attente de publication</option>
						</select>
					</td>
				</tr>				
			</tbody>
		</table>

		<p class="submit sociallymap_valid-submit">
			<button type="submit" name="submit" id="submit" class="button button-primary">
				<i class="dashicons-before dashicons-yes sociallymap-icon-button"></i>
				Valider la configuration
			</button>
		</p>
	</form>
</div>