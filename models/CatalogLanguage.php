<?php

namespace app\models;

use Itstructure\AdminModule\models\Language;

/**
 * This is the model class for table "catalog_language".
 *
 * @property integer $catalog_id
 * @property integer $language_id
 * @property string $title
 * @property string $description
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Catalog $catalog
 * @property Language $language
 */
class CatalogLanguage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'catalog_language';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'catalog_id',
                    'language_id',
                ],
                'required',
            ],
            [
                [
                    'catalog_id',
                    'language_id',
                ],
                'integer',
            ],
            [
                ['description'],
                'string',
            ],
            [
                [
                    'created_at',
                    'updated_at',
                ],
                'safe',
            ],
            [
                ['title'],
                'string',
                'max' => 255,
            ],
            [
                ['catalog_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Catalog::className(),
                'targetAttribute' => ['catalog_id' => 'id'],
            ],
            [
                ['language_id'],
                'exist',
                'skipOnError' => true,
                'targetClass' => Language::className(),
                'targetAttribute' => ['language_id' => 'id'],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'catalog_id' => 'Catalog ID',
            'language_id' => 'Language ID',
            'title' => 'Title',
            'description' => 'Description',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCatalog()
    {
        return $this->hasOne(Catalog::className(), ['id' => 'catalog_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getLanguage()
    {
        return $this->hasOne(Language::className(), ['id' => 'language_id']);
    }
}
