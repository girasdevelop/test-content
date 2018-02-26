<?php

namespace app\modules\files\behaviors;

use yii\base\Behavior;
use yii\db\ActiveRecord;
use app\modules\files\models\Mediafile;

class MediafileBehavior extends Behavior
{
    /**
     * Owner name.
     *
     * @var string
     */
    public $name = '';

    /**
     * Owner mediafiles attribute names.
     *
     * @var array
     */
    public $attributes = [];

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            ActiveRecord::EVENT_AFTER_INSERT => 'addOwners',
            ActiveRecord::EVENT_AFTER_UPDATE => 'updateOwners',
            ActiveRecord::EVENT_BEFORE_DELETE => 'deleteOwners',
        ];
    }

    /**
     * Add owners to mediafile
     */
    public function addOwners()
    {
        foreach ($this->attributes as $attr) {
            if ($mediafile = $this->loadModel(['url' => $this->owner->{$attr}])) {
                $mediafile->addOwner($this->owner->primaryKey, $this->name, $attr);
            }
        }
    }

    /**
     * Update owners of mediafile
     */
    public function updateOwners()
    {
        foreach ($this->attributes as $attr) {
            Mediafile::removeOwner($this->owner->primaryKey, $this->name, $attr);

            if ($mediafile = $this->loadModel(['url' => $this->owner->{$attr}])) {
                $mediafile->addOwner($this->owner->primaryKey, $this->name, $attr);
            }
        }
    }

    /**
     * Delete owners of mediafile
     */
    public function deleteOwners()
    {
        foreach ($this->attributes as $attr) {
            Mediafile::removeOwner($this->owner->primaryKey, $this->name, $attr);
        }
    }

    /**
     * Load model by id
     *
     * @param array $conditions
     *
     * @return Mediafile
     */
    private function loadModel(array $conditions)
    {
        return Mediafile::findOne($conditions);
    }
}
