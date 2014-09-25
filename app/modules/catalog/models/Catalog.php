<?php
namespace app\modules\catalog\models;

use common\db\ActiveRecord;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class Catalog
 * Модель каталога товаров
 * @package app\modules\catalog\models
 * @author Churkin Anton <webadmin87@gmail.com>
 */
class Catalog extends ActiveRecord
{

    use \app\modules\main\components\PermissionTrait;

    /**
     * @var array массив идентификаторов связанных категорий
     */

    protected $_sectionsIds;

    /**
     * Получение идентификаторов связанных категорий
     * @return array
     */
    public function getSectionsIds()
    {

        if ($this->_sectionsIds === null) {

            $this->_sectionsIds = $this->getManyManyIds("sections");
        }

        return $this->_sectionsIds;
    }

    /**
     * Установка идентификаторов связанных категорий
     * @param array $sectionsIds
     */
    public function setSectionsIds($sectionsIds)
    {
        $this->_sectionsIds = $sectionsIds;
    }

    /**
     * @inheritdoc
     */

    public function behaviors()
    {
        $arr = parent::behaviors();

        $arr["manyManySaver"] = [
            'class' => \common\behaviors\ManyManySaver::className(),
            'names' => ['sections'],
        ];
        return $arr;
    }

    /**
     * @inheritdoc
     */
    public function metaClass()
    {
        return meta\CatalogMeta::className();
    }

    /**
     * Связь с категориями
     * @return \yii\db\ActiveQueryInterface
     */

    public function getSections()
    {

        return $this->hasMany(CatalogSection::className(), ['id' => 'section_id'])->viaTable('news_to_sections', ['news_id' => 'id']);

    }

    /**
     * @inheritdoc
     * @return \app\modules\catalog\db\CatalogQuery
     */
    public static function find()
    {
        return Yii::createObject(\app\modules\catalog\db\CatalogQuery::className(), [get_called_class()]);
    }

    /**
     * Поиск элементов по категориям. Если идентификаторы категорий не заданы выбираются все элементы.
     * @param null|array $ids массив идентификаторов категорий
     * @return \yii\data\ActiveDataProvider провайдер данных
     * @throws \yii\base\InvalidConfigException
     */

    public function searchBySection($ids = null)
    {

        $query = $this->find()->bySections($ids);

        $dataProvider = Yii::createObject([
            'class' => ActiveDataProvider::className(),
            "query" => $query,
        ]);

        return $dataProvider;

    }

    /**
     * @inheritdoc
     */

    public static function tableName()
    {
        return "catalog_catalog";
    }

	/**
	 * Связь с производителями
	 * @return \yii\db\ActiveQuery
	 */
	public function getProducer()
	{
		return $this->hasOne(Producer::className(), ["id"=>"producer_id"]);
	}

}