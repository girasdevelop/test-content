<?php
namespace app\modules\files\widgets;

use Yii;
use yii\helpers\{Html, Url};
use yii\widgets\InputWidget;
use app\modules\files\Module;
use app\modules\files\assets\FileSetterAsset;

/**
 * Class FileSetter
 *
 * Example:
 *
 * Container to display selected mediafile (example for image).
 * <div id="thumbnail-container"></div>
 *
 * <?php echo FileSetter::widget([
 *    'name' => UploadModelInterface::FILE_TYPE_IMAGE,
 *    'buttonName' => Module::t('main', 'Set thumbnail'),
 *    'mediafileContainer' => '#thumbnail-container',
 *    'owner' => 'post',
 *    'ownerId' => {current owner id, post id, page id e.t.c.},
 *    'ownerAttribute' => UploadModelInterface::FILE_TYPE_IMAGE,
 *    'subDir' => 'post'
 * ]); ?>
 *
 * @package app\modules\files\widgets
 *
 * @author Girnik Andrey <girnikandrey@gmail.com>
 */
class FileSetter extends InputWidget
{
    /**
     * Owner name (post, article, page e.t.c.).
     *
     * @var string|null
     */
    public $owner = null;

    /**
     * Owner id.
     *
     * @var int|null
     */
    public $ownerId = null;

    /**
     * Owner attribute (thumbnail, image e.t.c.).
     *
     * @var string|null
     */
    public $ownerAttribute = null;

    /**
     * Subdirectory to upload files.
     *
     * @var string
     */
    public $subDir = '';

    /**
     * @var string template to display widget elements
     */
    public $template = '<div class="input-group">{input}<span class="input-group-btn">{button}{reset-button}</span></div>';

    /**
     * @var string button html tag
     */
    public $buttonHtmlTag = 'button';

    /**
     * @var string button name
     */
    public $buttonName = 'Browse';

    /**
     * @var array button html options
     */
    public $buttonOptions = [];

    /**
     * @var string reset button html tag
     */
    public $resetButtonHtmlTag = 'button';

    /**
     * @var string reset button name
     */
    public $resetButtonName = '<span class="text-danger glyphicon glyphicon-remove"></span>';

    /**
     * @var array reset button html options
     */
    public $resetButtonOptions = [];

    /**
     * @var string Optional, if set, in container will be inserted selected image
     */
    public $mediafileContainer = '';

    /**
     * @var string JS function. That will be called before insert file data in to the input.
     */
    public $callbackBeforeInsert = '';

    /**
     * @var string This data will be inserted in input field
     */
    public $insertedData = self::INSERTED_DATA_ID;
    
    /**
     *
     * @var string
     */
    public $srcToFiles  = Module::FILE_MANAGER_SRC;

    /**
     * Data, which will be inserted in to the file input.
     */
    const INSERTED_DATA_ID = 'id';
    const INSERTED_DATA_URL = 'url';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->options['class'])) {
            $this->options['class'] = 'form-control';
        }

        if (empty($this->buttonOptions['id'])) {
            $this->buttonOptions['id'] = $this->options['id'] . '-btn';
        }

        if (empty($this->buttonOptions['class'])) {
            $this->buttonOptions['class'] = 'btn btn-default';
        }

        if (empty($this->resetButtonOptions['class'])) {
            $this->resetButtonOptions['class'] = 'btn btn-default';
        }

        $this->buttonOptions['role'] = 'filemanager-load';
        $this->resetButtonOptions['role'] = 'clear-input';
        $this->resetButtonOptions['data-clear-element-id'] = $this->options['id'];
        $this->resetButtonOptions['data-mediafile-container'] = $this->mediafileContainer;
    }

    /**
     * Run widget.
     */
    public function run()
    {
        if ($this->hasModel()) {
            $replace['{input}'] = Html::activeHiddenInput($this->model, $this->attribute, $this->options);
        } else {
            $replace['{input}'] = Html::hiddenInput($this->name, $this->value, $this->options);
        }

        $replace['{button}'] = Html::tag($this->buttonHtmlTag, $this->buttonName, $this->buttonOptions);
        $replace['{reset-button}'] = Html::tag($this->resetButtonHtmlTag, $this->resetButtonName, $this->resetButtonOptions);

        FileSetterAsset::register($this->view);

        if (!empty($this->callbackBeforeInsert)) {
            $this->view->registerJs('
                $("#' . $this->options['id'] . '").on("fileInsert", ' . $this->callbackBeforeInsert . ');'
            );
        }

        $modal = $this->renderFile('@app/modules/files/views/layouts/modal.php', [
            'inputId' => $this->options['id'],
            'btnId' => $this->buttonOptions['id'],
            'srcToFiles' => Url::to([$this->srcToFiles]),
            'mediafileContainer' => $this->mediafileContainer,
            'insertedData' => $this->insertedData,
            'owner' => $this->owner,
            'ownerId' => $this->ownerId,
            'ownerAttribute' => $this->ownerAttribute,
            'subDir' => $this->subDir,
        ]);

        return strtr($this->template, $replace) . $modal;
    }

    /**
     * Give ability of configure view to the module class.
     *
     * @return \yii\base\View|\yii\web\View
     */
    public function getView()
    {
        $module = \Yii::$app->controller->module;

        if (method_exists($module, 'getView')) {
            return $module->getView();
        }

        return parent::getView();
    }
}