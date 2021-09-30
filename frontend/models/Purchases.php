<?php

namespace frontend\models;

use Yii;

/**
 * This is the model class for table "purchases".
 *
 * @property int $id
 * @property int $id_card
 * @property string $purchase
 * @property int $cost
 * @property int $date
 */
class Purchases extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'purchases';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id_card', 'purchase', 'cost', 'date'], 'required'],
            [['id_card', 'cost', 'date'], 'integer'],
            [['purchase'], 'string', 'max' => 20],
            [['id_card'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_card' => 'Id Card',
            'purchase' => 'Purchase',
            'cost' => 'Cost',
            'date' => 'Date',
        ];
    }


    /**
     * Связь с Card
     */
    public function getCard(){
        return $this->hasOne(Card::class, ['id' => 'id_card']);
    }

}
