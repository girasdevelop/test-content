<?php

namespace app\modules\files\behaviors;

use app\modules\files\models\Mediafile;

/**
 * Class BehaviorMediafile
 * BehaviorMediafile class to add, update and remove owners of Mediafile model.
 *
 * @package Itstructure\AdminModule\behaviors
 */
class BehaviorMediafile extends Behavior
{
    /**
     * Load Mediafile model by conditions.
     *
     * @param array $conditions
     *
     * @return Mediafile
     */
    protected function loadModel(array $conditions): Mediafile
    {
        return Mediafile::findOne($conditions);
    }

    /**
     * Remove owner of Mediafile model.
     *
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     *
     * @return bool
     */
    protected function removeOwner(int $ownerId, string $owner, string $ownerAttribute): bool
    {
        return Mediafile::removeOwner($ownerId, $owner, $ownerAttribute);
    }
}
