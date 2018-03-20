<?php

namespace app\modules\files\components;

use yii\helpers\Html;
use yii\widgets\LinkPager;

class FilesLinkPager extends LinkPager
{
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        parent::run();
    }

    protected function renderPageButton($label, $page, $class, $disabled, $active)
    {
        $options = ['class' => $class === '' ? null : $class];
        if ($active) {
            Html::addCssClass($options, $this->activePageCssClass);
        }
        if ($disabled) {
            Html::addCssClass($options, $this->disabledPageCssClass);
            return Html::tag('li', Html::tag('span', $label), $options);
        }
        $params = $this->pagination->params;
        $owner = isset($params['owner']) ? $params['owner'] : null;
        $ownerId = isset($params['ownerId']) ? (int)$params['ownerId'] : null;
        $ownerAttribute = isset($params['ownerAttribute']) ? $params['ownerAttribute'] : null;

        $linkOptions              = $this->linkOptions;
        $linkOptions['data-page'] = $page;
        $linkOptions['onclick']   = 'getFiles("'.($page + 1).'","'.$owner.'","'.$ownerId.'","'.$ownerAttribute.'")';

        return Html::tag('li', Html::a($label, '#pagination', $linkOptions), $options);
    }
}