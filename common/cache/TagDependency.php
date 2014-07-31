<?php
namespace common\cache;

use Yii;
use \yii\caching\Dependency;

/**
 * Class TagDependency
 * Зависимость тегтрованного кеша
 * @package common\cache
 * @author Churkin Anton <webadmin87@gmail.com>
 */
class TagDependency extends Dependency {

    protected $_tags = [];

    /**
     * @inheritdoc
     */
    protected function generateDependencyData($cache)
    {
        return microtime(true);
    }

    /**
     * @inheritdoc
     */
    public function getHasChanged($cache)
    {

        $tags = Yii::$app->cache->mget($this->tags);

        if(count($tags) != count($this->tags))
            return true;

        foreach($tags AS $val) {

            if((double) $val > (double) $this->data) {
                return true;
            }

        }

        return false;

    }

    /**
     * Добавляет к зависимости тег
     * @param string $tag
     */

    public function addTag($tag) {

        $this->_tags[] = $tag;

    }

    /**
     * Устанавливает теги
     * @param array $tags
     */

    public function setTags(array $tags) {

        $this->_tags = $tags;

    }

    /**
     * Возвращает установленные теги
     * @return array
     */

    public function getTags() {
        return $this->_tags;
    }

    /**
     * Устанавливает теги из массива моделей
     * @param \common\db\ActiveRecord[] $models
     */

    public function setTagsFromModels($models) {

        foreach($models AS $model) {

            $this->addTag($model->setItemTag());

        }

        if(!empty($model))
            $this->addTag($model->setClassTag());

    }

}