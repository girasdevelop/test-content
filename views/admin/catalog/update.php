<?php

/* @var $this Itstructure\AdminModule\components\AdminView */
/* @var $model Itstructure\AdminModule\models\MultilanguageValidateModel */

$this->title = 'Update Catalog: ' . $model->mainModel->getDefaultTranslate('title');
$this->params['breadcrumbs'][] = ['label' => 'Catalogs', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->mainModel->getDefaultTranslate('title'), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="catalog-update">

    <?php echo $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
