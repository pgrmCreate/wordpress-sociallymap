<div class="wrap">
	<h1>
		Ajouter une nouvelle entité
		<a href="?page=sociallymap-rss-list" class="page-title-action">Voir la liste des entités</a>
	</h1>
	<form method="post" class="sociallymap_formRSS">
		<?php settings_fields('sociallymap_publisher_updateRSS') ?>
		<input type="hidden" name="sociallymap_postRSS" value="1"/>

		<label class="sociallymap_label">
			Ajouter un nom optionel
			<input type="text" placeholder="Nom du flux RSS" name="sociallymap_name"
			class="sociallymap_formRSS_newFlux">
		</label>

		<label class="sociallymap_label">
			Catégorie cible de la publication
			<select name="sociallymap_category">
				<?php foreach (get_categories() as $key => $value) { ?>	
					<option value="<?php echo get_cat_ID($value->name);?>"
					<?php if($value->name === get_option('sociallymap_publisher_categorie')) echo "selected" ?> >
					<?php echo $value->name;?></option>
				<?php } ?>
			</select>
		</label>

		<label class="sociallymap_label">
			Publication en tant que brouillon
			<input type="checkbox" name="sociallymap_activate" class="sociallymap_formRSS_newFlux" value="1">
		</label>

		<label class="sociallymap_label">
			Mobile 
			<select name="sociallymap_modal_mobile">
				<option value="1">Fenêtre modale</option>
				<option value="0">Externe</option>
			</select>
		</label>

		<label class="sociallymap_label">
			Bureau (résolution grande) 
			<select name="sociallymap_modal_desktop">
				<option value="1">Fenêtre modale</option>
				<option value="0">Externe</option>
			</select>
		</label>	

		<p class="submit sociallymap_valid-submit">
			<button type="submit" name="submit" id="submit" class="button button-primary">
				<i class="dashicons-before dashicons-plus-alt sociallymap-icon-button"></i>
				Ajouter
			</button>
		</p>
	</form>
</div>