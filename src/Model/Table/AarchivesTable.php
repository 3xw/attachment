<?php
declare(strict_types=1);

namespace Attachment\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class AarchivesTable extends Table
{
  public function initialize(array $config): void
  {
    parent::initialize($config);

    $this->setTable('aarchives');
    $this->setDisplayField('id');
    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');

    $this->belongsTo('Users', [
      'type' => 'LEFT',
      'foreignKey' => 'user_id',
      'className' => 'Attachment.Users',
    ]);
    
    // custom behaviors
    $this->addBehavior('Attachment\ORM\Behavior\UserIDBehavior');
  }

  public function validationDefault(Validator $validator): Validator
  {
    $validator
    ->nonNegativeInteger('id')
    ->allowEmptyString('id', null, 'create');

    $validator
    ->scalar('state')
    ->maxLength('state', 45)
    ->requirePresence('state', 'create')
    ->notEmptyString('state');

    $validator
    ->scalar('profile')
    ->maxLength('profile', 45)
    ->requirePresence('profile', 'create')
    ->notEmptyFile('profile');

    $validator
    ->requirePresence('size', 'create')
    ->notEmptyString('size');

    $validator
    ->scalar('md5')
    ->maxLength('md5', 32)
    ->requirePresence('md5', 'create')
    ->notEmptyString('md5');

    return $validator;
  }

  public function buildRules(RulesChecker $rules): RulesChecker
  {
    $rules->add($rules->existsIn(['user_id'], 'Users'));

    return $rules;
  }
}
