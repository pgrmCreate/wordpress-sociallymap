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
				<input type="checkbox" name="sociallymap_draft" class="sociallymap_formRSS_newFlux" value="1"
				<?php if(1 == $default_options[3]->default_value) echo "checked" ?> >
			</label>

			<label class="sociallymap_label">
				Mobile 
				<select name="sociallymap_modal_mobile">
					<option <?php if(1 == $default_options[1]->default_value) echo "selected" ?> 
					value="1">
						Fenêtre modale
					</option>
					<option <?php if(0 == $default_options[1]->default_value) echo "selected" ?> 
					value="0">
						Externe
					</option>
				</select>
			</label>

			<label class="sociallymap_label">
				Bureau (haute résolution) 
				<select name="sociallymap_modal_desktop">
					<option <?php if(1 == $default_options[2]->default_value) echo "selected" ?> 
					value="1">
						Fenêtre modale
					</option>
					<option <?php if(0 == $default_options[2]->default_value) echo "selected" ?> 
					value="0">
						Externe
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