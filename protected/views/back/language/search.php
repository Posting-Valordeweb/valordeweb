<div class="btn-group mb-3">
    <a href="<?php echo $this->createUrl("admin/language/category") ?>" class="btn btn-primary">
        <i class="fas fa-chevron-left"></i> <?php echo Yii::t("language", "Manage Translations") ?>
    </a>
</div>
<br/><br/>

<form class="mb-3" method="get">

    <div class="form-group">
        <label for="search_q"><?php echo Yii::t("language", "Phrase") ?> <?php echo mb_strtolower(Yii::t("misc", "OR")) ?> <?php echo Yii::t("language", "Translation") ?></label>
        <input type="text" class="form-control" id="search_q" name="q" value="<?php echo $r->getQuery('q') ?>">
    </div>

    <div class="form-group">
        <label for="search_language"><?php echo Yii::t("language", "Language") ?></label>

        <select class="form-control" name="lang_id" id="search_language">
            <option value=""><?php echo Yii::t("language", "Any language") ?></option>
            <?php foreach(Language::model()->getList(false) as $language): ?>
            <option <?php echo $r->getQuery('lang_id') == $language->id ? 'selected' : null ?> value="<?php echo CHtml::encode($language->id) ?>">
                <?php echo Language::formatLanguage($language); ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="search_category"><?php echo Yii::t("language", "Category name") ?></label>

        <select class="form-control" name="category" id="search_category">
            <option value=""><?php echo Yii::t("language", "Any category") ?></option>
            <?php foreach($categories as $category): ?>
            <option <?php echo $r->getQuery('category') == $category ? 'selected' : null ?> value="<?php echo CHtml::encode($category) ?>">
                <?php echo CHtml::encode($category) ?>
            </option>
            <?php endforeach; ?>
        </select>
    </div>

    <button type="submit" class="btn btn-primary"><?php echo Yii::t("language", "Search") ?></button>
</form>

<?php if($count > 0): ?>

<div class="table-responsive">
<table class="table table-hover">
<thead class="thead-light">
<tr>
<th><?php echo Yii::t("language", "Language ID") ?></th>
<th><?php echo Yii::t("language", "Category name") ?></th>
<th><?php echo Yii::t("language", "Phrase") ?></th>
<th><?php echo Yii::t("language", "Translation") ?></th>
<th></th>
</tr>
</thead>
<tbody>
<?php foreach($rows as $row): ?>
<tr>
<td><?php echo CHtml::encode($row['language']) ?></td>
<td><?php echo Helper::highlightWordPart(CHtml::encode($row['category']), $q) ?></td>
<td><?php echo Helper::highlightWordPart(CHtml::encode($row['message']), $q) ?></td>
<td><?php echo Helper::highlightWordPart(CHtml::encode($row['translation']), $q) ?></td>
<td>
	<a target="_blank" href="<?php echo $this->createUrl("admin/language/updatemessage", array("id"=>$row['id'])) ?>">
		<?php echo Yii::t("language", "Edit phrase") ?>
	</a>
	<br/>
	<a target="_blank" href="<?php echo $this->createUrl("admin/language/translatemessage",  array("id"=>$row['id'])) ?>">
		<?php echo Yii::t("language", "Translate phrase") ?>
	</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<?php if($pagination) $this->widget('LinkPager', array(
	'pages' => $pagination,
	'cssFile' => false,
	'header' => '',
	'hiddenPageCssClass' => 'disabled',
	'selectedPageCssClass' => 'active',
	'htmlOptions' => array(
		'class' => 'pagination pagination-sm flex-wrap',
	),
    'firstPageCssClass'=>'page-item',
    'previousPageCssClass'=>'page-item',
    'internalPageCssClass'=>'page-item',
    'nextPageCssClass'=>'page-item',
    'lastPageCssClass'=>'page-item',
)); ?>

<?php endif; ?>