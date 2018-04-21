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
 * @property UploadComponentInterface[]|LocalUploadComponent[] $tmpUploadComponents Upload components to delete files according with their different types.
 *
 * @package Itstructure\FilesModule\traits
 */
trait MediaFilesTrait
{
    /**
     * Upload components to delete files according with their different types.
     *
     * @var UploadComponentInterface[]|LocalUploadComponent[]
     */
    protected $tmpUploadComponents = [];

    /**
     * Delete mediafiles from owner.
     *
     * @param string $owner
     * @param int $ownerId
     * @param Module $module
     *
     * @return void
     */
    protected function deleteMediafiles(string $owner, int $ownerId, Module $module): void
    {
        $mediafileIds = OwnersMediafiles::getMediaFilesQuery([
            'owner' => $owner,
            'ownerId' => $ownerId,
        ])
        ->select('id')
        ->all();

        $mediafileIds = array_map(function ($data) {return $data->id;}, $mediafileIds);

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
    protected function findMediafileModel(int $id): Mediafile
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
    protected function deleteMediafileEntry($id, Module $module)
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
                    $this->setComponentIfNotIsset($mediafileModel->storage, $module->get('local-upload-component'));
                    break;
                }

                default: {
                    throw new InvalidConfigException('Unknown type of the file storage');
                }
            }

            /** @var UploadModelInterface|BaseUpload $deleteModel */
            $deleteModel = $this->tmpUploadComponents[$mediafileModel->storage]->setModelForDelete($mediafileModel);

            if (!$deleteModel->delete()){
                return false;
            }

            return 1;
        }
    }

    /**
     * Set tmp upload component if not isset.
     *
     * @param string $storage
     * @param UploadComponentInterface $component
     *
     * @return void
     */
    private function setComponentIfNotIsset(string $storage, UploadComponentInterface $component): void
    {
        if (!isset($this->tmpUploadComponents[$storage])){
            $this->tmpUploadComponents[$storage] = $component;
        }
    }
}