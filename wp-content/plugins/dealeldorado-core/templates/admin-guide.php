<?php defined('ABSPATH') || exit; ?>
<div class="wrap ded-admin-wrap">
<div class="ded-admin-header">
    <h1><i class="fas fa-book me-2 text-info"></i>Guide d'utilisation DealElDorado</h1>
</div>

<div class="row g-4">
<div class="col-lg-8">

<div class="ded-card mb-4">
<h3>🚀 Démarrage rapide</h3>
<div class="alert alert-info">
    <strong>Prérequis :</strong> Les plugins <strong>Content Egg Pro</strong> et <strong>DealElDorado Core</strong> doivent être activés dans WordPress → Extensions.
</div>

<h5>Étape 1 : Activer les plugins</h5>
<ol>
    <li>Allez dans <strong>Extensions</strong> dans le BO WordPress</li>
    <li>Activez <strong>Content Egg Pro</strong></li>
    <li>Activez <strong>DealElDorado Core</strong></li>
    <li>Activez le thème <strong>DealElDorado</strong> dans Apparence → Thèmes</li>
</ol>

<h5>Étape 2 : Vérifier la configuration API</h5>
<p>Allez dans <strong>DealElDorado → Configuration API</strong> et vérifiez que les clés API sont correctes (elles sont lues automatiquement depuis votre fichier <code>.env</code>).</p>

<h5>Étape 3 : Créer votre premier article produit</h5>
<ol>
    <li>Cliquez sur <strong>Articles → Ajouter</strong></li>
    <li>Donnez un titre au produit (ex: "iPhone 15 Pro 256Go")</li>
    <li>Dans la métabox <strong>Content Egg</strong> (en bas de page), sélectionnez les modules</li>
    <li>Entrez un mot-clé et cliquez "Update" pour importer les offres</li>
    <li>Publiez l'article</li>
</ol>
</div>

<div class="ded-card mb-4">
<h3>📋 Shortcodes disponibles</h3>

<table class="table table-bordered">
<thead class="table-light"><tr><th>Shortcode</th><th>Description</th><th>Paramètres</th></tr></thead>
<tbody>
<tr>
    <td><code>[ded_compare]</code></td>
    <td>Tableau de comparaison de prix</td>
    <td><code>keyword</code>, <code>module</code>, <code>limit</code></td>
</tr>
<tr>
    <td><code>[ded_search_bar]</code></td>
    <td>Barre de recherche stylisée</td>
    <td><code>placeholder</code>, <code>button_text</code></td>
</tr>
<tr>
    <td><code>[ded_top_deals]</code></td>
    <td>Grille des meilleurs deals</td>
    <td><code>count</code></td>
</tr>
<tr>
    <td><code>[ded_price_box]</code></td>
    <td>Boîte de prix d'un marchand</td>
    <td><code>merchant</code>, <code>price</code>, <code>url</code>, <code>badge</code></td>
</tr>
<tr>
    <td><code>[ded_affiliate_disclaimer]</code></td>
    <td>Mention de liens affiliés</td>
    <td>Aucun</td>
</tr>
</tbody>
</table>

<h6>Exemple d'utilisation :</h6>
<pre class="bg-light p-3 rounded"><code>[ded_compare keyword="iPhone 15 Pro" module="CjProducts" limit="10"]

[ded_search_bar placeholder="Trouvez votre produit..." button_text="Chercher"]

[ded_price_box merchant="Amazon" price="1199" url="https://amzn.to/xxx" badge="-15%"]

[ded_affiliate_disclaimer]</code></pre>
</div>

<div class="ded-card mb-4">
<h3>🔌 Modules Content Egg disponibles</h3>
<div class="row g-3">
    <?php
    $modules = array(
        array('name' => 'CjProducts', 'label' => 'CJ Products', 'desc' => 'Réseau affilié CJ.com'),
        array('name' => 'Clickbank', 'label' => 'Clickbank', 'desc' => 'Produits digitaux & physiques'),
        array('name' => 'Viglink', 'label' => 'Sovrn/VigLink', 'desc' => 'Monétisation automatique'),
        array('name' => 'Amazon', 'label' => 'Amazon', 'desc' => 'Programme Amazon Partenaires'),
        array('name' => 'Ebay', 'label' => 'eBay', 'desc' => 'Produits eBay via API'),
    );
    foreach ($modules as $m):
        $is_active = !empty(get_option('cegg_module_' . $m['name'], array())['is_active']);
    ?>
    <div class="col-md-6">
        <div class="d-flex gap-3 align-items-center p-2 rounded border">
            <span class="badge <?php echo $is_active ? 'bg-success' : 'bg-secondary'; ?>"><?php echo $is_active ? 'Actif' : 'Inactif'; ?></span>
            <div>
                <div class="fw-semibold"><?php echo esc_html($m['label']); ?></div>
                <div class="text-muted small"><?php echo esc_html($m['desc']); ?></div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
</div>

</div><!-- col-lg-8 -->

<div class="col-lg-4">
<div class="ded-card mb-3">
<h5><i class="fas fa-link me-2 text-primary"></i>Liens utiles</h5>
<ul class="list-unstyled">
    <li class="mb-2"><a href="<?php echo esc_url(admin_url('admin.php?page=dealeldorado-api')); ?>">⚙️ Configuration API</a></li>
    <li class="mb-2"><a href="<?php echo esc_url(admin_url('post-new.php')); ?>">➕ Nouvel article</a></li>
    <li class="mb-2"><a href="<?php echo esc_url(admin_url('appearance.php?page=content-egg-templates')); ?>">🎨 Templates Content Egg</a></li>
    <li class="mb-2"><a href="<?php echo esc_url(home_url('/')); ?>" target="_blank">🌐 Voir le site</a></li>
    <li class="mb-2"><a href="https://accounts.clickbank.com/login.htm" target="_blank">💲 Clickbank</a></li>
    <li class="mb-2"><a href="https://members.cj.com/" target="_blank">🔗 CJ.com</a></li>
    <li class="mb-2"><a href="https://platform.sovrn.com/" target="_blank">📊 Sovrn</a></li>
</ul>
</div>

<div class="ded-card">
<h5><i class="fas fa-info-circle me-2 text-warning"></i>Information domaine</h5>
<p class="small">Domaine final : <strong>dealeldorado.com</strong></p>
<p class="small text-muted">Actuellement en développement sur <code>localhost/dealeldorado</code></p>
<p class="small">N'oubliez pas de mettre à jour l'URL du site dans <strong>Réglages → Général</strong> avant la mise en production.</p>
</div>
</div>
</div>
</div>
