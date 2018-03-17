<?php
namespace app\modules\files\widgets;

use Yii;
use yii\helpers\{Html, Url};
use yii\widgets\InputWidget;
use app\modules\files\assets\FileSetterAsset;

/**
 * Class FileSetter
 *
 * Basic example of usage:
 *
 *  <?= FileInput::widget([
 *      'name' => 'mediafile',
 *      'buttonTag' => 'button',
 *      'buttonName' => 'Browse',
 *      'buttonOptions' => ['class' => 'btn btn-default'],
 *      'options' => ['class' => 'form-control'],
 *      // Widget template
 *      'template' => '<div class="input-group">{input}<span class="input-group-btn">{button}</span></div>',
 *      // Optional, if set, only this image can be selected by user
 *      'thumb' => 'original',
 *      // Optional, if set, in container will be inserted selected image
 *      'imageContainer' => '.img',
 *      // Default to FileInput::DATA_URL. This data will be inserted in input field
 *      'pasteData' => FileInput::DATA_URL,
 *      // JavaScript function, which will be called before insert file data to input.
 *      // Argument data contains file data.
 *      // data example: [alt: "Witch with cat", description: "123", url: "/uploads/2014/12/vedma-100x100.jpeg", id: "45"]
 *      'callbackBeforeInsert' => 'function(e, data) {
 *      console.log( data );
 *      }',
 *  ]) ?>
 *
 * This class provides filemanager usage. You can optional select all media file info to your input field.
 * More samples of usage see on github: https://github.com/PendalF89/yii2-filemanager
 *
 * @package pendalf89\filemanager\widgets
 * @author Zabolotskikh Boris <zabolotskich@bk.ru>
 */
class FileSetter extends InputWidget
{
    /**
     * @var string widget template
     */
    public $template = '<div class="input-group">{input}<span class="input-group-btn">{button}{reset-button}</span></div>';

    /**
     * @var string button tag
     */
    public $buttonHtmlTag = 'button';

    /**
     * @var string button name
     */
    public $buttonName = 'Browse';

    /**
     * @var array button html options
     */
    public $buttonOptions = ['class' => 'btn btn-default'];

    /**
     * @var string reset button tag
     */
    public $resetButtonHtmlTag = 'button';

    /**
     * @var string reset button name
     */
    public $resetButtonName = '<span class="text-danger glyphicon glyphicon-remove"></span>';

    /**
     * @var array reset button html options
     */
    public $resetButtonOptions = ['class' => 'btn btn-default'];

    /**
     * @var string Optional, if set, only this image can be selected by user
     */
    public $thumb = '';

    /**
     * @var string Optional, if set, in container will be inserted selected image
     */
    public $imageContainer = '';

    /**
     * @var string JavaScript function, which will be called before insert file data to input.
     * Argument data contains file data.
     * data example: [alt: "Witch with cat", description: "123", url: "/uploads/2014/12/vedma-100x100.jpeg", id: "45"]
     */
    public $callbackBeforeInsert = '';

    /**
     * @var string This data will be inserted in input field
     */
    public $insertedData = self::INSERTED_DATA_URL;

    /**
     * @var array widget html options
     */
    public $options = ['class' => 'form-control'];
    
    /**
     *
     * @var array selecte the frameSrc in case you use a different module name
     */
    public $frameSrc  = ['/files/api/managers/filemanager'];

    const INSERTED_DATA_ID = 'id';
    const INSERTED_DATA_URL = 'url';
    const INSERTED_DATA_ALT = 'alt';
    const INSERTED_DATA_DESCRIPTION = 'description';

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();

        if (empty($this->buttonOptions['id'])) {
            $this->buttonOptions['id'] = $this->options['id'] . '-btn';
        }

        $this->buttonOptions['role'] = 'filemanager-launch';
        $this->resetButtonOptions['role'] = 'clear-input';
        $this->resetButtonOptions['data-clear-element-id'] = $this->options['id'];
        $this->resetButtonOptions['data-image-container'] = $this->imageContainer;
    }

    /**
     * Runs the widget.
     */
    public function run()
    {
        if ($this->hasModel()) {
            $replace['{input}'] = Html::activeTextInput($this->model, $this->attribute, $this->options);
        } else {
            $replace['{input}'] = Html::textInput($this->name, $this->value, $this->options);
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
            'frameId' => $this->options['id'] . '-frame',
            'frameSrc' => Url::to($this->frameSrc), 
            'thumb' => $this->thumb,
            'imageContainer' => $this->imageContainer,
            'insertedData' => $this->insertedData,
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