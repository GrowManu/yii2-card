<?php

namespace frontend\models;

use Codeception\Module\Yii2;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "card".
 *
 * @property int $id
 * @property string $series
 * @property int $number
 * @property int $date_begin
 * @property int $date_end
 * @property int $date_use
 * @property int $sum
 * @property int $status
 */
class Card extends \yii\db\ActiveRecord{

    const STATUS_INACTIVE = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_EXPIRED = 2;

    public $cardSeries = ['A100' => 'A100', 'B200' => 'B200', 'C500' => 'C500', 'D1000' => 'D1000', 'S5000' => 'S5000'];
    public $cardTime = ['Month', 'Six month`s', 'Year'];
    public $cardStatus = [ 'Not Active', 'Active'];
    public $cardCount;
    private $cardNumber = 0;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'card';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['number', 'unique'],
            ['cardNumber', 'integer'],
            ['cardTime', 'integer'],
            ['cardStatus', 'integer'],
            ['series', 'string', 'max' => 5],
            ['cardSeries', 'string', 'max' => 6],
            ['cardCount', 'integer', 'min' => 1, 'max' => 500000],
            [['number', 'date_begin', 'date_end', 'date_use', 'sum', 'status'], 'integer'],
            [['series', 'number', 'date_begin', 'date_end', 'sum', 'status', 'cardCount'], 'required'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'series' => 'Series',
            'number' => 'Number',
            'date_begin' => 'Date Begin',
            'date_end' => 'Date End',
            'date_use' => 'Date Use',
            'sum' => 'Sum',
            'status' => 'Status',
        ];
    }


    /**
     * Задает выбраную серию карт в БД
     */
    public function SeriesCard(){
        return $this->cardSeries;

    }
    /**
     * Проверяет и берет последний id для NumberCard метода
     */
    public function LookId(){
        $lastId = Card::find()
            ->where(['id' => Card::find()->max('id')])
            ->one();
        $id = $lastId->id;
        if (!is_null($id)) {
            $this->cardNumber = $id;
        }
    }

    /**
     * Создает номер карты используя свойство $cardNumber
     */
    public function NumberCard(){
            $this->cardNumber++;
            return $this->cardNumber;
    }

    /**
     * Создает unix время для начальной даты карт
     */
    public function DateBeginCard(){
       return time();
    }

    /**
     * Создает конечную дату карт
     */
    public function DateEndCard(){
        switch ($this->cardTime){
            case 0:
                return strtotime( '+ 1 month', time());
            case 1:
                return strtotime( '+ 6 month', time());;
            case 2:
                return strtotime( '+ 1 year', time());
        }
    }

    /**
     * Создает начальный статус для карт
     */
    public function defaultStatusCard(){
        return self::STATUS_INACTIVE;
    }

    /**
     * Создает сумму для карт в зависимости от их серии
     */
    public function SumCard(){
        switch ($this->cardSeries){
            case 'A100':
                 return 100;
            case 'B200':
                 return 200;
            case 'C500':
                return 500;
            case 'D1000':
                return 1000;
            case 'S5000':
                 return 5000;
        }
    }


    /**
     * Создает многомерный масив для MakeCard() метода
     */
    public function MakeArray(){
        $this->LookId();
        $this->series = $this->SeriesCard();
        $this->date_begin = $this->DateBeginCard();
        $this->date_end = $this->DateEndCard();
        $this->date_use = null;
        $this->sum = $this->SumCard();
        $this->status = $this->defaultStatusCard();

        for ($i = 0; $i < $this->cardCount; $i++){
            $cardAttributes[] = $this->series;
            $cardAttributes[] = $this->NumberCard();
            $cardAttributes[] = $this->date_begin;
            $cardAttributes[] = $this->date_end;
            $cardAttributes[] = $this->date_use;
            $cardAttributes[] = $this->sum;
            $cardAttributes[] = $this->status;
            $cards[] = $cardAttributes;
            $cardAttributes = [];
        }
        return $cards;
    }

    /**
     * Создает записи карт в БД
     */
    public function MakeCard(){
        $transaction = Yii::$app->db->beginTransaction();
        try{
            Yii::$app->db->createCommand()->batchInsert('card', [
                'series',
                'number',
                'date_begin',
                'date_end',
                'date_use',
                'sum',
                'status',
            ], $this->MakeArray())->execute();

            $transaction->commit();
        } catch(\Throwable $e) {
            $transaction->rollBack();
          }
    }



    /**
     * Изменяет статус катрты на выбраный
     */
    public function ChangeStatus(){
        $this->status = $this->cardStatus;
    }

    /**
     * Проверяет карту на просроченность
     */
    public function ExpiredCard(){
        if($this->date_end < time()){
            return false;
        }
        return true;
    }

    /**
     * Меняет статус на STATUS_EXPIRED у всех просроченых карт
     */
    public function ExpiredAllCards(){
        Card::updateAll(['status' => self::STATUS_EXPIRED], ['<', 'date_end', time()]);
    }



    /**
     * Связь с Purchases
     */
    public function getPurchases(){
        return $this->hasMany(Purchases::class, ['id_card' => 'id']);
    }

    public function Purchase($model){;
        return $model->purchases->purchase;
    }
}
