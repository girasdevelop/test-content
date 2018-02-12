<?php

use app\models\Catalog;
use Itstructure\AdminModule\components\MultilanguageValidateComponent;

return [
    /**
     * Component class.
     */
    'class' => MultilanguageValidateComponent::class,

    /**
     * List of models.
     * Each model is identified by the name of the table.
     * In the config attributes of each model, you need to specify:
     * Dynamic (translated fields) dynamicFields.
     * Field dynamicFields needs to have: 'name' - field name.
     * Field dynamicFields (not necessary) may have 'rules'.
     */
    'models' => [
        Catalog::tableName() => [
            'dynamicFields' => [
                [
                    'name' => 'title',
                    'rules' => [
                        [
                            'required',
                            'message' => 'Field "{attribute}" must not be empty.'
                        ],
                        [
                            'string',
                            'max' => 255,
                        ],
                        [
                            'unique',
                        ]
                    ]
                ],
                [
                    'name' => 'description',
                    'rules' => [
                        [
                            'required',
                            'message' => 'Field "{attribute}" must not be empty.'
                        ],
                        [
                            'string',
                        ]
                    ]
                ],
            ],
        ]
    ]
];
