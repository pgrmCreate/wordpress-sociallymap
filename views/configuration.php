<link rel='stylesheet' id='configuration-style.css'
 href='<?php echo basename(dirname( __FILE__ )); ?>/../../wp-content/plugins/sociallymap/views/styles/configuration.css?ver=20120208' type='text/css' media='all' />

<h1>Configuration du plugin sociallymap </h1>

<ul class="subsubsub">
	<li>
	 	<a href="?page=sociallymap-configuration">
	 		Configuration générale
	 	</a>
	</li> 
	|
	<li>
		<a href="?page=sociallymap-rss">
		Mes RSS
		</a>
	</li>
</ul>

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
		Publier comme brouillon
		<input type="checkbox" name="sociallymap_publisher_isDraft"
		<?php if("on" === get_option('sociallymap_publisher_isDraft')) echo "checked" ?> >
	</label>


	<label class="sociallymap_label">
		Mobile 
		<select>
			<option>Fenêtre modale</option>
			<option>Externe</option>
		</select>
	</label>

	<label class="sociallymap_label">
		Bureau (résolution grande) 
		<select>
			<option>Fenêtre modale</option>
			<option>Externe</option>
		</select>
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