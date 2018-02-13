<?php

namespace app\models;

use yii\base\Model;
use yii\db\Connection;

/**
 * This is the model class for table "settings".
 *
 * @property Connection $db
 */
class Settings extends Model
{
    /**
     * @var int
     */
    public $initUserStatus;

    /**
     * @var string
     */
    public $initUserRole;

    /**
     * @var string
     */
    private $tableName;

    /**
     * Data base connection driver.
     *
     * @var Connection
     */
    private $db;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [
                [
                    'initUserStatus',
                    'initUserRole',
                ],
                'required',
            ],
            [
                [
                    'initUserStatus',
                ],
                'integer',
            ],
            [
                [
                    'initUserRole',
                ],
                'string',
                'max' => 64
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'initUserStatus' => 'User status after registration',
            'initUserRole' => 'User role after registration',
        ];
    }

    /**
     * Set db driver.
     *
     * @param Connection $db
     */
    public function setDb(Connection $db): void
    {
        $this->db = $db;
    }

    /**
     * Set table name.
     *
     * @param string $tableName
     */
    public function setTableName(string $tableName): void
    {
        $this->tableName = $tableName;
    }

    /**
     * Returns this object with field's values.
     *
     * @return $this
     */
    public function getSettings()
    {
        $result = $this->db->createCommand('SELECT * FROM '.$this->tableName)
            ->queryOne();

        $this->setAttributes($result, false);

        return $this;
    }

    /**
     * Save settings data.
     *
     * @return bool
     */
    public function save(): bool
    {
        if (!$this->validate()){
            return false;
        }

        $count = (int)$this->db->createCommand('SELECT COUNT(*) FROM '.$this->tableName)->queryScalar();

        if ($count > 0){
            $result = $this->db->createCommand()->update($this->tableName, $this->getAttributes())->execute();
        } else {
            $result = $this->db->createCommand()->insert($this->tableName, $this->getAttributes())->execute();
        }

        if (!$result){
            return false;
        }

        return true;
    }
}
