<?php
namespace common\db;

use Yii;
use yii\db\ActiveRecord AS YiiRecord;
use yii\data\ActiveDataProvider;

/**
 * Class ActiveRecord
 * Надстройка над ActiveRecord фпеймворка.
 * @package common\db
 * @author Churkin Anton <webadmin87@gmail.com>
 */

abstract class ActiveRecord extends YiiRecord {

    /**
     * Сценарии валидации
     */

    const SCENARIO_INSERT = "insert";

    const SCENARIO_UPDATE = "update";

    const SCENARIO_SEARCH = "search";

    /**
     * @var array значение сортировки по умолчанию
     */

    protected $_defaultSearchOrder = ["id"=>"desc"];

    /**
     * Базовые сценарии
     * @var array
     */
    protected $_baseScenarios = [self::SCENARIO_INSERT, self::SCENARIO_UPDATE, self::SCENARIO_SEARCH];

    /**
     * @var \common\db\MetaFields объект с описанием полей модели
     */
    protected $metaFields;

    /**
     * Сченари валидации
     * @return array
     *
     */
    public function  scenarios() {

        $scenarios = parent::scenarios();

        foreach($this->_baseScenarios AS $scenario) {

            if(!isset($scenarios[$scenario])) {

                $scenarios[$scenario] = $scenarios[YiiRecord::SCENARIO_DEFAULT];
            }

        }

        return $scenarios;

    }

    /**
     * Возвращает объект с описанием полей модели
     * @return MetaFields
     */

    public function getMetaFields() {

        if($this->metaFields === null) {

            $class = $this->metaClass();

            $this->metaFields =  Yii::createObject($class, [$this]);

        }

        return $this->metaFields;

    }

    /**
     * Правила валидации Формируем из полей
     * @return array
     */

    public function rules() {

        $fields = $this->getMetaFields()->getFields();

        $rules = [];

        foreach($fields AS $field) {

            if($field->rules())
                $rules = array_merge($rules, $field->rules());

        }

        return $rules;

    }

    /**
     * Подписи атрибутов
     * @return array
     */

    public function attributeLabels() {

        $fields = $this->getMetaFields()->getFields();

        $labels = [];

        foreach($fields AS $field) {

            $labels[$field->attr] = $field->title;

        }

        return $labels;

    }

    /**
     * Поведения
     * @return array
     */

    public function behaviors() {

        $fields = $this->getMetaFields()->getFields();

        $behaviors = [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];

        foreach($fields AS $field) {

            if($field->behaviors())
                $behaviors = array_merge($behaviors, $field->behaviors());

        }

        return $behaviors;

    }

    /**
     * @inheritdoc
     * @return \common\db\ActiveQuery
     */
    public static function find()
    {
        return Yii::createObject(\common\db\ActiveQuery::className(), [get_called_class()]);
    }

    /**
     * Возвращает провайдер данных для поиска
     * @param array $params массив значений атрибутов модели
     * @param array $dataProviderConfig параметры провайдера данных
     * @return \yii\data\ActiveDataProvider
     */

    public function search($params, $dataProviderConfig=[]) {

        $fields = $this->getMetaFields()->getFields();

        $query = $this->find();

        $config = array_merge([
            'query' => $query,
        ], $dataProviderConfig);

        $dataProvider = Yii::createObject(ActiveDataProvider::className(), [$config]);

        $dataProvider->getSort()->defaultOrder = $this->_defaultSearchOrder;

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        foreach($fields AS $field)
            $field->search($query);


        return $dataProvider;

    }


    /**
     * Возвращает имя класса содержащего описание полей модели
     * @return string
     */

    public abstract function metaClass();

}