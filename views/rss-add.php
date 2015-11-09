<div class="wrap">
	<h1>
		Ajouter une nouvelle entité
		<a href="?page=sociallymap-rss-list" class="page-title-action">Voir la liste des entités</a>
	</h1>
	<form method="post" class="sociallymap_formRSS">
		<?php settings_fields('sociallymap_publisher_addRSS') ?>
		<input type="hidden" name="sociallymap_addRSS_valid" value="1"/>

		<label class="sociallymap_label">
			Ajouter un nouveau flux RSS
			<input type="text" placeholder="Nouveau flux RSS" name="sociallymap_addRSS_value"
			class="sociallymap_formRSS_newFlux">
		</label>

		<label class="sociallymap_label">
			Catégorie cible de la publication
			<select name="sociallymap_publisher_categorie">
				<?php foreach (get_categories() as $key => $value) { ?>	
					<option value="<?php echo $value->name;?>"
					<?php if($value->name === get_option('sociallymap_publisher_categorie')) echo "selected" ?> >
					<?php echo $value->name;?></option>
				<?php } ?>
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