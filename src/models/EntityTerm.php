<?php

namespace macfly\taxonomy\models;

use yii\helpers\ArrayHelper;

class EntityTerm extends \mhndev\yii2TaxonomyTerm\models\EntityTerm
{

	public function rules()
	{
		return [
			[['entity'], 'required'],
			[['entity_id'], 'required'],
			[['term_id'], 'required'],
			[['entity', 'entity_id', 'term_id'], 'unique', 'targetAttribute' => ['entity', 'entity_id', 'term_id']],
		];
	}

	//return term model assigned to this entity
  public function getTerm()
  {
    return $this->hasOne(Term::className(), ['id' => 'term_id']);
  }

	//return Entity model related to this Term
	public function getChildEntity()
	{
		$model	= $this->entity;
		return $this->hasOne($model::className(), ['id' => 'entity_id']);
	}

	public function afterSave($insert, $changedAttributes)
	{
		// Increase Term usage Counter;
		return  Term::findOne($this->term_id)->updateCounters(['usage_count' => 1]);
	}

	public function afterDelete()
	{
		parent::afterDelete();
		// Decrease Term usage Counter
		return Term::findOne($this->term_id)->updateCounters(['usage_count' => -1]);
	}

  public function __toString()
	{
    return sprintf("[entityterm][id:%d] termid:%d => %s(%d)", $this->id, $this->term_id, $this->entity, $this->entity_id);
  }

}
