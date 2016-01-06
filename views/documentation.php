<?php
	$data = get_query_var('data');
?>

<h1>Configuration du Plugin</h1>
<p>
    Créer une entité dans le back office de votre blog dans le menu « Ajouter une entité ». <br>
    L'entité créée correspond à l'entité qui est posée sur votre mapping. <br>
    <b>Chaque entité WordPress posée sur le mapping possède un ID unique et devra être créée dans la
     configuration du plugin.</b>
</p>

<h1>Configuration d’une entité</h1>
<p>
    Dans le menu création d’entité, voici à quoi correspondent les différents champs :
    <ul class="sm-style-puces">
        <li>
            <p>
                <b><u>Label :<br></u></b>
            </p>
            <p>
                Renseigner le nom que vous souhaitez donner à votre entité.
            </p>
        </li>
        <li>
            <p>
                <b><u>Identifiant de l'entité :<br></u></b>
            </p>
            <p>
                Pour remplir cette case, rendez-vous sur votre mapping et récupérez le code "Identifiant à indiquer
                dans votre plugin" et le coller.
            </p>
        </li>
        <li>
            <p>
                <b><u>Publier en mode :<br></u></b>
            </p>
            <p>
                Définir le mode de publication :
                <ul class="sm-style-puces">
                    <li>Publication Directe</li>
                    <li>Publication en mode « Brouillon »</li>
                    <li>Publication « En attente de relecture »</li>
                    <li>Publication « Privée »</li>
                </ul>
            </p>
        </li>
        <li>
            <p>
                <b><u>Image :<br></u></b>
            </p>
            <p>
                <ul class="sm-style-puces">
                    <li>Publier l’image de l’article en tant que « Image à la une ».</li>
                    <li>Publier l’image dans l’article.</li>
                    <li>Publier l’image en tant que « Image à la une » ET dans l’article.</li>
                </ul>
            </p>
        </li>
        <li>
            <p>
                <b><u>Afficher les articles dans une fenêtre modale :<br></u></b>
            </p>
            <p>
                Permet d’ouvrir l’article publié dans une fenêtre modale : vos visiteurs peuvent lire
                l’article publié sur le site source à travers votre site, à la fermeture de
                la « Fenêtre Modale » les visiteurs restent sur votre site. Cela vous permet de
                conserver le trafic sur votre site web et de réduire le taux de rebond sur votre site.
            </p>
        </li>
        <li>
            <p>
                <b><u>Inclure les balises de liens canoniques : <br></u></b>
            </p>
            <p>
                Permet d’inclure, ou non les balises de lien canoniques.
            </p>
        </li>
        <li>
            <p>
                <b><u>Ne pas indexer les articles publiés via Sociallymap dans les moteurs de recherche :<br></u></b>
            </p>
            <p>
                Indexe ou non les articles publiés via cette entité sur les moteurs de recherche.
            </p>
        </li>
        <li>
            <p>
                <b><u>Ne pas suivre les liens (nofollow) :<br></u></b>
            </p>
            <p>
                Permet au moteur de recherche de suivre les liens.
            </p>
        </li>
        <li>
            <p>
                <b><u>Libellé suite d'article :<br></u></b>
            </p>
            <p>
                Libellé de la phrase permettant de lire la suite de l’article.
                Cette phrase sera présente dans l’article en elle-même et non sur les boutons de votre site.
            </p>
        </li>
    </ul>
</p>


