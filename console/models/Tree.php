<?php

namespace console\models;

use yii\db\ActiveRecord;
use yii\db\ActiveQuery;

/**
 * @property integer $id
 * @property integer $parentId
 * @property integer $position
 * @property string $path
 * @property integer $level
*/

class Tree extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%tree}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['level'], 'required'],
            [['parent_id', 'position', 'level'], 'integer'],
            [['path'], 'string', 'max' => 12288],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'parent_id' => Yii::t('app', 'Parent ID'),
            'position' => Yii::t('app', 'Position'),
            'path' => Yii::t('app', 'Path'),
            'level' => Yii::t('app', 'Level'),
        ];
    }

    public function getChildren(): ActiveQuery
    {
        return $this->hasMany(Tree::className(), ['parent_id' => 'id']);
    }

    public function getNextId(): int
    {
        return self::find()->select('max(id) as id')->scalar() + 1;
    }

    public function getChild(int $position): ?Tree
    {
        $children = $this->getChildren()->all();

        foreach ($children as $child) {
            if ($child->position == $position) {
                return $child;
            }
        }

        return null;
    }
}
