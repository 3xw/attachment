<?php
namespace Attachment\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\Utility\Inflector;

use Attachment\Model\Entity\Atag;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
* Atags Model
*
* @property \Cake\ORM\Association\BelongsToMany $Attachments
*/
class AtagsTable extends Table
{

  /**
  * Initialize method
  *
  * @param array $config The configuration for the Table.
  * @return void
  */
  public function initialize(array $config)
  {
    parent::initialize($config);

    $this->setTable('atags');
    $this->setDisplayField('name');
    $this->setPrimaryKey('id');

    $this->belongsToMany('Attachment.Attachments', [
      'foreignKey' => 'atag_id',
      'targetForeignKey' => 'attachment_id',
      'joinTable' => 'attachments_atags'
    ]);

    // Add the behaviour to your table
    $this->addBehavior('Search.Search');

    // Setup search filter using search manager
    $this->searchManager()
    ->add('index', 'Attachment.SessionIndex', []);
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
  public function validationDefault(Validator $validator)
  {
    $validator
    ->add('id', 'valid', ['rule' => 'numeric'])
    ->allowEmpty('id', 'create');

    $validator
    ->requirePresence('name', 'create')
    ->notEmpty('name')
    ->add('name', 'unique', ['rule' => 'validateUnique', 'provider' => 'table']);

    $validator
    ->requirePresence('slug', 'create')
    ->notEmpty('slug')
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
  public function buildRules(RulesChecker $rules)
  {
    $rules->add($rules->isUnique(['name']));
    $rules->add($rules->isUnique(['slug']));
    return $rules;
  }
}
