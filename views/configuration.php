<link rel='stylesheet' id='configuration-style.css'
 href='<?php echo basename(dirname( __FILE__ )); ?>/../../wp-content/plugins/sociallymap/views/styles/configuration.css?ver=20120208' type='text/css' media='all' />

<h1>Configuration du plugin sociallymap </h1>

<form method="post" action="options.php">
	<?php settings_fields('sociallymap_publisher_settings') ?>
	<label class="sociallymap_label">
		Flux RSS
		<input type="text" name="sociallymap_publisher_linkRSS" 
		value="<?php echo get_option('sociallymap_publisher_linkRSS')?>"/>
	</label>


	<label class="sociallymap_label">
		Catégorie cible de la publication
		<select name="sociallymap_publisher_categorie" >
			<?php foreach (get_categories() as $key => $value) { ?>	
				<option value="<?php echo $value->name;?>"
				<?php if($value->name === get_option('sociallymap_publisher_categorie')) echo "selected" ?> >
				<?php echo $value->name;?></option>
			<?php } ?>
		</select>
	</label>

	<label class="sociallymap_label">
		Activer la publication
		<input type="checkbox" name="sociallymap_publisher_isActive"
		<?php if("on" === get_option('sociallymap_publisher_isActive')) echo "checked" ?> >
	</label>

	<?php submit_button('Valider la configuration'); ?>
</form>

<!-- DISPLAY DEBUG
<h1>#1 Link RSS</h1>
<?php echo get_option('sociallymap_publisher_linkRSS')?>
<h1>#2 Active ?</h1>
<?php echo get_option('sociallymap_publisher_isActive')?>
<h1>#3 Categorie</h1>
<?php echo get_option('sociallymap_publisher_categorie')?> -->