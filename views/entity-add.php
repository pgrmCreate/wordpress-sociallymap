<?php
    $data = get_query_var('data');
    $default_value = $data['data'];
?>

<?php
if (isset($_POST['sociallymap_isNotValid'])) {
?>
<div id="message" class="error notice is-dismissible">
    <p>
        Veuillez remplir les champs correctement
    </p>
    <button type="button" class="notice-dismiss">
        <span class="screen-reader-text">Cacher l'information</span>
    </button>
</div>
<?php
}
?>

<div class="wrap">
    <h1>
        Ajouter une nouvelle entité
        <a href="?page=sociallymap-entity-list" class="page-title-action sm-title-action">
            Voir la liste des entités
        </a>
    </h1>
    <form method="post">
        <input type="hidden" name="sociallymap_postEntity" value="1"/>

        <table class="form-table">
            <tbody>
                <tr class="form-field form-required">
                    <th>
                        <label>Entité active</label>
                    </th>
                    <td>
                        <input type="checkbox" name="sociallymap_activate" value="1" checked>
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th>
                        <label>Label</label>
                    </th>
                    <td>
                        <input type="text" placeholder="Mon entité" name="sociallymap_name">
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th>
                        <label>Identifiant de l'entité sociallymap</label>
                    </th>
                    <td>
                        <input type="text" placeholder="Identifiant de l'entité sociallymap" name="sociallymap_entityId" >
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th>
                        <label>Ajout de la balise 'readmore'</label>
                    </th>
                    <td>
                        <input type="checkbox" name="sociallymap_morebalise" value="1" checked>
                    </td>
                </tr>

                <tr>
                    <th><label>Catégorie cible de la publication :</label></th>
                </tr>

                <?php foreach (get_categories(['hide_empty' => 0]) as $key => $value) { ?>
                    <tr class="form-field form-required">
                    <th>
                        <i><?php echo $value->name;?></i>
                    </th>
                    <td class="sm-categories-option">
                            <input name="sociallymap_category[]" type="checkbox"
                            value="<?php echo get_cat_ID($value->name);?>">
                    </td>
                </tr>
                <?php } ?>

                <tr class="form-field form-required">
                    <th>
                        <label>Publier en mode</label>
                    </th>
                    <td>
                        <select name="sociallymap_publish_type">
                            <option value="publish">Publier</option>
                            <option value="draft">Brouillon</option>
                            <option value="pending">En attente de relecture</option>
                            <option value="private">Privée</option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th>
                        <label>Image</label>
                    </th>
                    <td>
                        <select name="sociallymap_image">
                            <option value="content">Inserer dans le contenu</option>
                            <option value="thumbnail">Inserer en tant qu'image à la une</option>
                            <option value="both">Inserer en tant que contenu et image à la une</option>
                        </select>
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th>
                        <label>Afficher les articles dans une fenêtre modale</label>
                    </th>
                    <td>
                        <input type="checkbox" name="sociallymap_display_type"
                         value="modal" checked>
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th>
                        <label>Inclure les balises de liens canoniques</label>
                    </th>
                    <td>
                        <input type="checkbox" name="sociallymap_link_canonical"
                         value="1" checked>
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th>
                        <label>Ne pas indexer les articles publiés via Sociallymap dans les moteurs de recherche</label>
                    </th>
                    <td>
                        <input type="checkbox" name="sociallymap_noIndex" value="1">
                    </td>
                </tr>
                <tr class="form-field form-required">
                    <th>
                        <label>Ne pas suivre les liens (nofollow)</label>
                    </th>
                    <td>
                        <input type="checkbox" name="sociallymap_noFollow" value="1">
                    </td>
                </tr>
                <tr class="form-field">
                    <th>
                        <label>Libellé suite d'article</label>
                    </th>
                    <td>
                        <input type="text" placeholder="Lire la suite" name="sociallymap_readmore">
                    </td>
                </tr>
            </tbody>
        </table>

        <p class="submit">
            <button type="submit" name="submit" id="submit" class="button button-primary">
                <i class="dashicons-before dashicons-plus-alt sociallymap-icon-button"></i>
                Enregistrer
            </button>
        </p>
    </form>
</div>