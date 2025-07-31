<script type="text/javascript">
$(document).ready(function(){
    var urls = {
        <?php foreach($thumbnailStack as $id=>$thumbnail): ?>
        <?php echo $id ?>:<?php echo $thumbnail ?>,
        <?php endforeach; ?>
    };
    // Asumiendo que dynamicThumbnail() ya maneja la lógica de carga asíncrona
    // y que es compatible con las URLs de miniaturas generadas.
    // Si no se carga dinámicamente, esta parte no tendrá efecto directo en el src inicial.
    dynamicThumbnail(urls);
});
</script>

<div class="row">
<?php
    foreach ($data as $website):
        $url = Yii::app()->controller->createUrl("website/show", array("domain"=>$website->domain));

        // --- INICIO DE LA MODIFICACIÓN DE LA LÓGICA DE LA MINIATURA ---
        // 1. Limpiar el dominio para que coincida con el nombre de archivo generado por Puppeteer.
        //    Asumimos que Helper::cropDomain() ya hace esto (elimina http://, https://, www.).
        //    Si el nombre de archivo en /var/www/html/thumbs/ es diferente (ej. con www. o http://),
        //    necesitaríamos ajustar esta línea para que coincida EXACTAMENTE.
        $thumbnailFileName = Helper::cropDomain($website->domain) . '.png';

        // 2. Construir la ruta relativa de la miniatura que Apache debe servir.
        $thumbnailPath = '/thumbs/' . $thumbnailFileName;

        // 3. Construir la ruta completa en el sistema de archivos del servidor
        //    para verificar si el archivo existe.
        $serverPathToFile = $_SERVER['DOCUMENT_ROOT'] . $thumbnailPath;

        // 4. Determinar la URL final de la imagen. Por defecto, será la imagen "no disponible".
        $imageUrl = Yii::app()->baseUrl . '/images/not-available.png';

        // 5. Verificar si el archivo de miniatura REALMENTE existe en el servidor.
        if (file_exists($serverPathToFile)) {
            // Si existe, usamos la URL de la miniatura generada.
            $imageUrl = Yii::app()->baseUrl . $thumbnailPath;
        }
        // --- FIN DE LA MODIFICACIÓN DE LA LÓGICA DE LA MINIATURA ---
?>
    <div class="col col-12 col-md-6 col-lg-4 mb-4">
        <div class="card mb-3">
            <h3 class="card-header"><?php echo Helper::cropDomain($website->idn) ?></h3>
            <a href="<?php echo $url ?>">
                <img class="card-img-top" id="thumb_<?php echo $website->id ?>" src="<?php echo $imageUrl; ?>" alt="Miniatura de <?php echo Helper::cropDomain($website->idn) ?>">
            </a>
            <div class="card-body">
                <p class="card-text">
                    <?php echo Yii::t("website", "Estimate Price") ?>: <strong><?php echo Helper::p($website->price) ?></strong>
                </p>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <?php echo Helper::getMaxLabel(
                        $website->search_engine->google_index,
                        $website->search_engine->bing_index,
                        $website->search_engine->yahoo_index,
                        $website->search_engine->google_backlinks
                    ) ?>: <span class="badge badge-success card-badge"><?php echo Helper::f(max(
                        $website->search_engine->google_index,
                        $website->search_engine->bing_index,
                        $website->search_engine->yahoo_index,
                        $website->search_engine->google_backlinks
                    )) ?></span>
                </li>
                <li class="list-group-item">
                    Facebook: <span class="badge badge-success card-badge"><?php echo Helper::f($website->social->facebook_shares) ?></span>
                </li>
                <li class="list-group-item">
                    <?php echo Yii::t("website", "Norton") ?>:<img src="<?php echo Yii::app() -> getBaseUrl(true) ?>/images/trust-badge/norton-seal.png" alt="Norton Security Seal">
                </li>
            </ul>
            <div class="card-body">
                <a class="btn btn-primary" href="<?php echo $url ?>">
                    <?php echo Yii::t("website", "Explore more") ?>
                </a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>

<?php $this -> widget('LinkPager', array(
        'pages' => $dataProvider->getPagination(),
        'htmlOptions' => array(
                'class' => 'pagination flex-wrap',
        ),
        'firstPageCssClass'=>'page-item',
    'previousPageCssClass'=>'page-item',
    'internalPageCssClass'=>'page-item',
    'nextPageCssClass'=>'page-item',
    'lastPageCssClass'=>'page-item',
        'cssFile' => false,
        'header' => '',
        'hiddenPageCssClass' => 'disabled',
        'selectedPageCssClass' => 'active',
)); ?>

<div class="clearfix"></div>