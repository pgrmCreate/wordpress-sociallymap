<?php
$entity = new Entity;
$editingEntity = $entity->getById($_GET['id']);
?>

<div class="wrap">
	<h1>
		Editer une nouvelle entité
	</h1>

	<form method="post" class="sociallymap_formRSS">
		<input type="hidden" name="sociallymap_updateRSS" value="1">

		<label class="sociallymap_label">
			Nom du lien RSS
			<input name="sm_label" value="<?php echo($editingEntity->name); ?>" class="sociallymap_formRSS_newFlux">
		</label>		

		<label class="sociallymap_label">
			Catégorie cible de la publication
			<select name="sm_category">
				<?php foreach (get_categories() as $key => $value) { ?>	
				<option value="<?php echo $value->cat_ID; ?>"
					<?php if($editingEntity->options[0]->value === $value->cat_ID) echo "selected" ?> >
					<?php echo $value->name;?></option>
					<?php } ?>
				</select>
			</label>

			<label class="sociallymap_label">
				Publier en tant que brouillon
				<input name="sm_active" <?php if($editingEntity->activate) echo ('checked');?>
				class="sociallymap_formRSS_newFlux" type="checkbox" value="1">
			</label>

			<label class="sociallymap_label">
				Mobile
				<select name="sm_modal_mobile">
					<option value="1" <?php if($editingEntity->options[1]->value == 1) echo ('selected');?> >
						Fenêtre modale
					</option>
					<option value="0" <?php if($editingEntity->options[1]->value == 0) echo ('selected');?> >
						Externe
					</option>
				</select>
			</label>

			<label class="sociallymap_label">
				Bureau (résolution grande) 
				<select name="sm_modal_desktop">
					<option value="1" <?php if($editingEntity->options[2]->value == 1) echo ('selected');?> >
						Fenêtre modale
					</option>
					<option value="0" <?php if($editingEntity->options[2]->value == 0) echo ('selected');?> >
						Externe
					</option>
				</select>
			</label>		

			<p class="submit sociallymap_valid-submit">
				<button type="submit" name="submit" id="submit" class="button button-primary">
					<i class="dashicons-before dashicons-update sociallymap-icon-button"></i>
					Mettre a jour le lien
				</button>
			</p>
		</form>
	</div>