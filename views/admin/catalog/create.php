<?php

/* @var $this Itstructure\AdminModule\components\AdminView */
/* @var $model Itstructure\AdminModule\models\MultilanguageValidateModel */

$this->title = 'Create Catalog';
$this->params['breadcrumbs'][] = ['label' => 'Catalogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Create';
?>
<div class="catalog-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
