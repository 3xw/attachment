<?php
declare(strict_types=1);

namespace Attachment\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Utility\Inflector;
use ArrayObject;
use Cake\Event\Event;
use Cake\Core\Configure;

/**
* Atags Model
*
* @property \Attachment\Model\Table\AtagTypesTable|\Cake\ORM\Association\BelongsTo $AtagTypes
* @property \Attachment\Model\Table\UsersTable|\Cake\ORM\Association\BelongsTo $Users
* @property \Attachment\Model\Table\AttachmentsTable|\Cake\ORM\Association\BelongsToMany $Attachments
*
* @method \Attachment\Model\Entity\Atag get($primaryKey, $options = [])
* @method \Attachment\Model\Entity\Atag newEntity($data = null, array $options = [])
* @method \Attachment\Model\Entity\Atag[] newEntities(array $data, array $options = [])
* @method \Attachment\Model\Entity\Atag|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
* @method \Attachment\Model\Entity\Atag saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
* @method \Attachment\Model\Entity\Atag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
* @method \Attachment\Model\Entity\Atag[] patchEntities($entities, array $data, array $options = [])
* @method \Attachment\Model\Entity\Atag findOrCreate($search, callable $callback = null, $options = [])
*/
class AtagsTable extends Table
{
  /**
  * Initialize method
  *
  * @param array $config The configuration for the Table.
  * @return void
  */
  public function initialize(array $config): void
  {
    parent::initialize($config);

    $this->setTable('atags');
    $this->setDisplayField('name');
    $this->setPrimaryKey('id');

    $this->belongsTo('Users', [
      'type' => 'LEFT',
      'foreignKey' => 'user_id',
      'className' => 'Users',
    ]);
    $this->belongsTo('AtagTypes', [
      'type' => 'LEFT',
      'foreignKey' => 'atag_type_id',
      'className' => 'Attachment.AtagTypes',
    ]);
    $this->belongsToMany('Attachments', [
      'foreignKey' => 'atag_id',
      'targetForeignKey' => 'attachment_id',
      'joinTable' => 'attachments_atags',
      'className' => 'Attachment.Attachments',
    ]);

    if(Configure::read('Attachment.translate'))
    {
      $this->addBehavior('Trois\Utils\ORM\Behavior\TranslateBehavior', ['fields' => ['name','slug']]);
    }
  }

  public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
  {
    if(empty($data['slug']) && !empty($data['name']))
    {
      $data['slug'] = Inflector::slug($data['name']);
    }
  }

  /**
  * Default validation rules.
  *
  * @param \Cake\Validation\Validator $validator Validator instance.
  * @return \Cake\Validation\Validator
  */
  public function validationDefault(Validator $validator): Validator
  {
    $validator
    ->nonNegativeInteger('id')
    ->allowEmptyString('id', 'create');

    $validator
    ->scalar('name')
    ->maxLength('name', 255)
    ->requirePresence('name', 'create')
    ->notEmptyString('name')
    ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

    $validator
    ->scalar('slug')
    ->maxLength('slug', 255)
    ->requirePresence('slug', 'create')
    ->notEmptyString('slug')
    ->add('slug', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

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
    $rules->add($rules->isUnique(['name']));
    $rules->add($rules->isUnique(['slug']));

    return $rules;
  }
}
