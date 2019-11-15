<?php
declare(strict_types=1);

namespace Attachment\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use ArrayObject;
use Cake\Event\Event;

class AarchivesTable extends Table
{

  public function initialize(array $config): void
  {
    parent::initialize($config);

    $this->setTable('aarchives');
    $this->setDisplayField('id');
    $this->setPrimaryKey('id');
    $this->addBehavior('Search.Search');
    $this->searchManager()
    ->add('q', 'Search.Like', [
      'before' => true,
      'after' => true,
      'mode' => 'or',
      'comparison' => 'LIKE',
      'wildcardAny' => '*',
      'wildcardOne' => '?',
      'field' => ['id']
    ]);
    $this->addBehavior('Timestamp');

    $this->belongsTo('Users', [
      'type' => 'LEFT',
      'foreignKey' => 'user_id',
      'className' => 'Attachment.Users',
    ]);
    $this->belongsTo('Attachments', [
      'type' => 'LEFT',
      'foreignKey' => 'attachment_id',
      'className' => 'Attachment.Attachments',
    ]);

    // custom behaviors
    $this->addBehavior('Attachment\ORM\Behavior\UserIDBehavior');

  }

  public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
  {
    if(!empty($data['aids']) && is_array($data['aids'])) $data['aids'] = json_encode($data['aids']);
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
    ->scalar('aids')
    ->requirePresence('aids', 'create')
    ->notEmptyString('aids');

    $validator
    ->scalar('failure_message')
    ->allowEmptyString('failure_message');

    return $validator;
  }

  /**
  * Returns a rules checker object that will be used for validating
  * application integrity.
  *
  * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
  * @return \Cake\ORM\RulesChecker
  */
  public function buildRules(RulesChecker $rules): RulesChecker
  {
    $rules->add($rules->existsIn(['user_id'], 'Users'));
    $rules->add($rules->existsIn(['attachment_id'], 'Attachments'));

    return $rules;
  }
}
