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

<h1>Configuration du plugin sociallymap </h1>

<div class="wrap">
	<form method="post">
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
						<label>Publication en tant que brouillon</label>
					</th>
					<td>
						<input type="checkbox" name="sociallymap_publish_type" class="sociallymap_formRSS_newFlux" value="draft"
						<?php if("draft" == $default_options[2]->default_value) echo "checked" ?> >
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
			</tbody>
		</table>

		<p class="submit sociallymap_valid-submit">
			<button type="submit" name="submit" id="submit" class="button button-primary">
				<i class="dashicons-before dashicons-yes sociallymap-icon-button"></i>
				Valider la configuration
			</button>
		</p>
	</form>

	<?php
		if($isSaved == true) {
			?>
			<div id="message" class="updated notice is-dismissible">
				<p>
					Modification bien effectué
				</p>
				<button type="button" class="notice-dismiss">
					<span class="screen-reader-text">Cacher l'information</span>
				</button>
			</div>	
			<?php
		}
	?>
</div>