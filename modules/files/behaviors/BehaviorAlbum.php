<?php

namespace app\modules\files\behaviors;

use app\modules\files\models\Album;

/**
 * Class BehaviorAlbum
 * BehaviorAlbum class to add, update and remove owners of Album model.
 *
 * @package Itstructure\AdminModule\behaviors
 */
class BehaviorAlbum extends Behavior
{
    /**
     * Load Album model by conditions.
     *
     * @param array $conditions
     *
     * @return Album
     */
    protected function loadModel(array $conditions): Album
    {
        return Album::findOne($conditions);
    }

    /**
     * Remove owner of Album model.
     *
     * @param int    $ownerId
     * @param string $owner
     * @param string $ownerAttribute
     *
     * @return bool
     */
    protected function removeOwner(int $ownerId, string $owner, string $ownerAttribute): bool
    {
        return Album::removeOwner($ownerId, $owner, $ownerAttribute);
    }
}
