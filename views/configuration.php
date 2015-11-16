<?php 
	$configOption = new ConfigOption();
	$default_options = $configOption->getConfig();
?>

<h1>Configuration du plugin sociallymap </h1>

<div class="wrap">
	<form method="post">
		<input type="hidden" name="sociallymap_updateConfig" value="1">
		
		<label class="sociallymap_label">
			Catégorie cible de la publication
			<select name="sociallymap_category">
				<?php foreach (get_categories() as $key => $value) { ?>	
				<option value="<?php echo $value->cat_ID;?>"
					<?php if($value->cat_ID == $default_options[0]->default_value) echo "selected" ?> >
					<?php echo $value->name;?></option>
					<?php } ?>
				</select>
			</label>

			<label class="sociallymap_label">
				Publication en tant que brouillon
				<input type="checkbox" name="sociallymap_publish_type" class="sociallymap_formRSS_newFlux" value="draft"
				<?php if("draft" == $default_options[2]->default_value) echo "checked" ?> >
			</label>

			<label class="sociallymap_label">
					Ouverture du lien
					<select name="sociallymap_display_type">
						<option value="modal" <?php if($default_options[1]->default_value == 'modal') echo ('selected');?> >
							Fenêtre modale
						</option>
						<option value="tab" <?php if($default_options[1]->default_value == 'tab') echo ('selected');?> >
							Nouvel onglet
						</option>	
						<option value="page" <?php if($default_options[1]->default_value == 'page') echo ('selected');?> >
							Même page
						</option>
					</select>
			</label>	

			<p class="submit sociallymap_valid-submit">
				<button type="submit" name="submit" id="submit" class="button button-primary">
					<i class="dashicons-before dashicons-yes sociallymap-icon-button"></i>
					Valider la configuration
				</button>
			</p>
		</form>
	</div>