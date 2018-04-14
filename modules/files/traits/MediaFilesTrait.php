<?php
namespace app\modules\files\traits;

use Yii;
use yii\web\NotFoundHttpException;
use yii\base\{InvalidConfigException, UnknownMethodException};
use app\modules\files\Module;
use app\modules\files\models\{Mediafile, OwnersMediafiles};
use app\modules\files\models\upload\BaseUpload;
use app\modules\files\components\LocalUploadComponent;
use app\modules\files\interfaces\{UploadComponentInterface, UploadModelInterface};

/**
 * Trait DeleteFilesTrait
 *
 * @package Itstructure\FilesModule\traits
 */
trait MediaFilesTrait
{
    /**
     * Delete mediafiles from owner.
     *
     * @param string $owner
     * @param int $ownerId
     * @param string $ownerAttribute
     * @param Module $module
     *
     * @return void
     */
    private function deleteMediafiles(string $owner, int $ownerId, string $ownerAttribute, Module $module): void
    {
        $mediafileIds = OwnersMediafiles::getMediaFilesQuery([
            'owner' => $owner,
            'ownerId' => $ownerId,
            'ownerAttribute' => $ownerAttribute,
        ])
        ->select('id')
        ->all();

        $mediafileIds = array_map(function ($data) {return $data->id;}, $mediafileIds);echo '<pre>';

        $this->deleteMediafileEntry($mediafileIds, $module);
    }

    /**
     * Find the media model entry.
     *
     * @param int $id
     *
     * @throws UnknownMethodException
     * @throws NotFoundHttpException
     *
     * @return Mediafile
     */
    private function findMediafileModel(int $id): Mediafile
    {
        $modelObject = new Mediafile();

        if (!method_exists($modelObject, 'findOne')){
            $class = (new\ReflectionClass($modelObject));
            throw new UnknownMethodException('Method findOne does not exists in ' . $class->getNamespaceName() . '\\' . $class->getShortName().' class.');
        }

        $result = call_user_func([
            $modelObject,
            'findOne',
        ], $id);

        if ($result !== null) {
            return $result;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Delete mediafile record with files.
     *
     * @param array|int|string $id
     * @param Module $module
     *
     * @throws InvalidConfigException
     *
     * @return bool|int
     */
    private function deleteMediafileEntry($id, Module $module)
    {
        if (is_array($id)){
            $i = 0;
            foreach ($id as $item) {
                if (!$this->deleteMediafileEntry((int)$item, $module)){
                    return false;
                }
                $i += 1;
            }
            return $i;

        } else {

            $mediafileModel = $this->findMediafileModel((int)$id);

            switch ($mediafileModel->storage) {
                case Module::STORAGE_TYPE_LOCAL: {
                    /** @var UploadComponentInterface|LocalUploadComponent $uploadComponent */
                    $uploadComponent = $module->get('local-upload-component');
                    break;
                }

                default: {
                    throw new InvalidConfigException('Unknown type of the file storage');
                }
            }

            /** @var UploadModelInterface|BaseUpload $deleteModel */
            $deleteModel = $uploadComponent->setModelForDelete($mediafileModel);

            if (!$deleteModel->delete()){
                return false;
            }

            return 1;
        }
    }
}