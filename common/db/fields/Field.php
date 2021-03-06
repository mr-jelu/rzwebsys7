<?php
namespace common\db\fields;

use yii\helpers\ArrayHelper;
use common\db\ActiveQuery;
use common\db\ActiveRecord;
use Yii;
use Yii\base\Object;
use Yii\widgets\ActiveForm;

/**
 * Class TextField
 * Базовый класс полей.
 * @package common\db\fields
 * @author Churkin Anton <webadmin87@gmail.com>
 */
class Field extends Object
{

    /**
     * @var \common\db\ActiveRecord модель
     */
    public $model;

    /**
     * @var string атрибут модели
     */
    public $attr;

    /**
     * @var string подпись атрибута
     */

    public $title;

    /**
     * @var mixed значение присваевоемое полю при создании модели с сценарием \common\db\ActiveRecord::SCENARIO_INSERT
     */
    public $initValue;

    /**
     * @var mixed значение поля присваевоемое модели перед сохранением, в случае если текущий атрибут не задан
     */
    public $defaultValue;

    /**
     * @var string вкладка формы на которой должно быть расположено поле
     */

    public $tab = \common\db\MetaFields::DEFAULT_TAB;

    /**
     * @var bool отображать в гриде
     */
    public $showInGrid = true;

    /**
     * @var bool отображать при детальном просмотре
     */
    public $showInView = true;

    /**
     * @var bool отображать в форме
     */
    public $showInForm = true;

    /**
     * @var bool отображать в фильтре грида
     */
    public $showInFilter = true;

    /**
     * @var bool отображать в расширенном фильре
     */
    public $showInExtendedFilter = true;

    /**
     * @var bool отображать поле при табличном вводе
     */

    public $showInTableInput = true;

    /**
     * @var bool использоваь ли валидатор safe
     */

    public $isSafe = true;

    /**
     * @var bool обязательно ли поле к заполнению
     */

    public $isRequired = false;

    /**
     * @var bool участвует ли поле при поиске
     */

    public $search = true;

    /**
     * @var bool возможность редактирования значения поля в гриде
     */

    public $editInGrid = false;

    /**
     * @var string действие для редактирования модели из грида
     */

    public $editableAction = "editable";

    /**
     * @var array опции по умолчанию при отображении в гриде
     */
    public $gridOptions = [];

    /**
     * @var array опции по умолчанию при детальном просмотре
     */
    public $viewOptions = [];

    /**
     * @var callable функция возвращающая данные ассоциированные с полем
     */
    public $data;

    /**
     * @var string|array имя класс, либо конфигурация компонента который рендерит поле ввыода формы
     */
    public $inputClass = "\\common\\inputs\\TextInput";

    /**
     * @var array параметры поля ввода
     */
    public $inputClassOptions = [];

    /**
     * @var string|array имя класса, либо конфигурация компонента который рендерит поле ввода расширенного фильтра
     */
    public $filterInputClass;

    /**
     * @var string шаблон для поля
     */
    public $formTemplate = '<div class="row"><div class="co-xs-12 col-md-6 col-lg-12">{input}</div></div>';

    /**
     * @var callable функция для применения ограничений при поиске по полю.
     * Принимает два аргумента \yii\db\ActiveQuery и \common\db\fields\Field
     */
    public $queryModifier;

    /**
     * @var array массив дополнительный правил валидации для поля
     * ["validatorName" => ["prop" => "value"] ... ]
     */
    public $rules = [];

    /**
     * @var array данные ассоциированные с полем (key=>value)
     */
    protected $_dataValue;

    /**
     * @var mixed значение фильтра грида установленное
     */
    protected $_gridFilter;

    /**
     * Конструктор
     * @param ActiveRecord $model модель
     * @param string $attr атрибут
     * @param array $config массив значений атрибутов
     */

    public function __construct(ActiveRecord $model, $attr, $config = [])
    {

        $this->model = $model;

        $this->attr = $attr;

        parent::__construct($config);

    }

    /**
     * Формирование Html кода поля для вывода в расширенном фильтре
     * @param ActiveForm $form объект форма
     * @param array $options массив html атрибутов поля
     * @return string
     */

    public function getExtendedFilterForm(ActiveForm $form, Array $options = [])
    {

        return $this->getForm($form, $options, false, $this->filterInputClass);

    }

    /**
     * Формирование Html кода поля для вывода в форме
     * @param ActiveForm $form объект форма
     * @param array $options массив html атрибутов поля
     * @param bool|int $index инднкс модели при табличном вводе
     * @param string|array $cls класс поля, либо конфигурационный массив
     * @return string
     */

    public function getForm(ActiveForm $form, Array $options = [], $index = false, $cls = null)
    {

        $cls = $cls?:$this->inputClass;

        $inputClass = is_array($cls)?$cls:["class"=>$cls];

        $input = Yii::createObject(ArrayHelper::merge([
            "modelField"=>$this,
        ], $inputClass, $this->inputClassOptions));

        return $input->renderInput($form, $options, $index);

    }

    /**
     * Формирует html код поля формы обернутый в шаблон
     * @param ActiveForm $form объект форма
     * @param array $options массив html атрибутов поля
     * @param bool|int $index инднкс модели при табличном вводе
     * @return string
     */
    public function getWrappedForm(ActiveForm $form, Array $options = [], $index = false)
    {
        $html = $this->getForm($form, $options, $index);

        return str_replace("{input}", $html, $this->formTemplate);

    }

    /**
     * Возвращает имя атрибута для поля формы
     * @param bool|int $index индекс модели при табличном вводе
     * @return string
     */

    protected function getFormAttrName($index)
    {

        return ($index !== false) ? "[$index]{$this->attr}" : $this->attr;

    }

	/**
	 * Конфигурация грида по умолчанию
	 * @return array
	 */
	protected function defaultGrid() {

		$grid = ['attribute' => $this->attr, 'label'=>$this->title];

		if ($this->showInFilter)
			$grid['filter'] = $this->getGridFilter();
        else
            $grid['filter'] = false;

		if ($this->editInGrid) {

			$grid = array_merge($grid, $this->xEditable());

		}

		return $grid;

	}

    /**
     * Конфигурация поля для грида (GridView)
     * @return array
     */
    protected function grid()
    {

    	return $this->defaultGrid();

    }

    /**
     * Результурующая конфигурация поля грида (GridView)
     * @return array
     */
    public final function getGrid()
    {
        return ArrayHelper::merge($this->grid(), $this->gridOptions);
    }

    /**
     * Возвращает значение фильтра для грида
     * @return mixed
     */

    public function getGridFilter()
    {

        if ($this->_gridFilter !== null) {
            return $this->_gridFilter;
        } else {
            return $this->defaultGridFilter();
        }

    }

    /**
     * @param $value mixed установка значения фильтра
     */

    public function setGridFilter($value)
    {

        $this->_gridFilter = $value;

    }

    /**
     * Возвращает значение фильтра для по умолчанию
     * @return mixed
     */

    protected function defaultGridFilter()
    {

        return true;

    }

    /**
     * Конфтгурация редактируемой колонки
     * @return array
     */
    protected function xEditable()
    {

        return [

            'class' => \common\xeditable\XEditableColumn::className(),
            'url' => $this->getEditableUrl(),
            'format' => 'raw',
        ];

    }

    /**
     * Создает url для x-editable
     * @return string
     */

    public function getEditableUrl()
    {

        return Yii::$app->urlManager->createUrl(Yii::$app->controller->uniqueId . "/" . $this->editableAction);

    }

	/**
	 * Конфигурация детального просмотра по умолчанию
	 * @return array
	 */
	protected function defaultView() {

		$view = ['attribute' => $this->attr, 'label'=>$this->title];

		return $view;

	}

    /**
     * Конфигурация поля для детального просмотра
     * @return array
     */
    protected function view()
    {

        return $this->defaultView();

    }

    /**
     * Результирующая конфигурация поля для детального просмотра
     * @return array
     */
    public final function getView()
    {

        return ArrayHelper::merge($this->view(), $this->viewOptions);

    }

    /**
     * Правила валидации
     * @return array
     */

    public function rules()
    {

        $rules = [];

        if ($this->isSafe)
            $rules[] = [$this->attr, 'safe'];

        if ($this->isRequired)
            $rules[] = [$this->attr, 'required', 'except' => ActiveRecord::SCENARIO_SEARCH];

        if($this->defaultValue !== null)
            $rules[] = [$this->attr, 'default', 'value'=>$this->defaultValue, 'except'=>[ActiveRecord::SCENARIO_SEARCH]];

        foreach($this->rules AS  $name => $options) {

            $options[0] = $this->attr;

            $options[1] = $name;

            $rules[] = $options;

        }

        return $rules;

    }

    /**
     * Поведения
     * @return array
     */

    public function behaviors()
    {

        return [];

    }

    /**
     * Возвращает массив данных ассоциированных с полем
     * @return array
     */

    public function getDataValue()
    {

        if ($this->_dataValue === null) {

            $func = $this->data;

            $this->_dataValue = is_callable($func) ? call_user_func($func) : [];

        }

        return $this->_dataValue;
    }

    /**
     * Поиск
     * @param ActiveQuery $query запрос
     */
    protected function search(ActiveQuery $query)
    {

        if($this->model->hasAttribute($this->attr)) {

            $table = $this->model->tableName();

            $attr = $this->attr;

            $query->andFilterWhere(["{{%$table}}.{{%$attr}}" => $this->model->{$this->attr}]);

        }

    }

    /**
     * Накладывает ограничение на поиск
     * @param ActiveQuery $query
     */
    public function applySearch(ActiveQuery $query) {

        if($this->queryModifier) {

            call_user_func($this->queryModifier, $query, $this);

        } else {

            $this->search($query);

        }

    }

    /**
     * Возвращает подпись атрибута
     * @return array
     */
    public function getAttributeLabel()
    {
        return [$this->attr => $this->title];
    }

}
