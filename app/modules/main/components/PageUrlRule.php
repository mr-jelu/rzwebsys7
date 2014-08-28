<?php

namespace app\modules\main\components;

use yii\web\UrlRule;
use \app\modules\main\models\Pages;

/**
 * Class PageUrlRule
 * Правило для роутинга текстовых страниц
 * @package app\modules\main\components
 * @author Churkin Anton <webadmin87@gmail.com>
 */
class PageUrlRule extends UrlRule
{
	/**
	 * @inheritdoc
	 */
	public $connectionID = 'db';

	/**
	 * @inheritdoc
	 */
	public $route = 'main/pages/index';

	/**
	 * @inheritdoc
	 */
	public $pattern = '[A-z0-9_-]+';

	/**
	 * @inheritdoc
	 */
	public function createUrl($manager, $route, $params)
	{
		if ($route === $this->route AND isset($params["model"]) AND $params["model"] instanceof Pages) {

			$url = [];

			$ancestors = $params["model"]->ancestors()->all();

			foreach ($ancestors as $model) {

				if($model->isRoot())
					continue;

				$url[] = $model->code;

			}

			$url[] = $params["model"]->code;

			unset($params["model"]);

			$str = implode("/", $url);

			if ($str !== '') {
				$str .= ($this->suffix === null ? $manager->suffix : $this->suffix);
			}

			if (!empty($params) && ($query = http_build_query($params)) !== '') {
				$str .= '?' . $query;
			}

			return $str;

		}
		return false;
	}

	/**
	 * @inheritdoc
	 */
	public function parseRequest($manager, $request)
	{
		$pathInfo = trim($request->getPathInfo(),'/');

		if(empty($pathInfo))
			return false;

		$sections = explode("/", $pathInfo);

		foreach($sections AS $section) {

			if(!isset($model))
				$model = Pages::find()->published()->andWhere(["code"=>$section])->one();
			else {
				$model = $model->children()->published()->andWhere(["code" => $section])->one();
			}

			if(!$model)
				return false;

		}

		if(!empty($model)) {

			return [$this->route, ['code'=>$model->code]];

		}

		return false;  // this rule does not apply
	}
}