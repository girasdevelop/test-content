<?php

/* @var $this Itstructure\AdminModule\components\AdminView */
/* @var $model Itstructure\AdminModule\models\MultilanguageValidateModel */
/* @var $albums Itstructure\MFUploader\models\album\Album[] */

$this->title = 'Create Catalog';
$this->params['breadcrumbs'][] = ['label' => 'Catalogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="catalog-create">

    <?php echo $this->render('_form', [
        'model' => $model,
        'albums' => $albums,
    ]) ?>

</div>
