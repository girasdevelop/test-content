<?php

namespace app\modules\files\models;

use yii\behaviors\TimestampBehavior;

/**
 * Class ActiveRecord
 *
 * @package Itstructure\FilesModule\models
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    /**
     * Connect behavior to the basic model.
     *
     * @return array
     */
    public function behaviors()
    {
        return [
            TimestampBehavior::class
        ];
    }
}
