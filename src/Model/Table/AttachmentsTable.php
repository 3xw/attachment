<?php
namespace Attachment\Model\Table;

use Attachment\Model\Entity\Attachment;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;
use Cake\Core\Configure;

/**
* Attachments Model
*
* @property \Cake\ORM\Association\BelongsToMany $Atags
* @property \Cake\ORM\Association\BelongsToMany $Highlights
* @property \Cake\ORM\Association\BelongsToMany $Paths
* @property \Cake\ORM\Association\BelongsToMany $Visits
*/
class AttachmentsTable extends Table
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

    $this->table('attachments');
    $this->displayField('name');
    $this->primaryKey('id');

    $this->addBehavior('Timestamp');

    // custom behaviors
    $this->addBehavior('Attachment.Embed', [
      'embed_field' => 'embed',
      'file_field' => 'path'
    ]);
    $this->addBehavior('Attachment.Fly', [
      'file_field' => 'path',
      'delete' => true
    ]);
    $this->addBehavior('Attachment.ATag', [
      'file_field' => 'path'
    ]);

    $this->belongsToMany('Atags', [
      'foreignKey' => 'attachment_id',
      'targetForeignKey' => 'atag_id',
      'joinTable' => 'attachments_atags',
      'className' => 'Attachment.Atags'
    ]);

    // Add the behaviour to your table
    $this->addBehavior('Search.Search');

    // Setup search filter using search manager
    $this->searchManager()
    ->add('index', 'Attachment.SessionIndex', [])
    ->add('search', 'Attachment.SessionLike', [
      'before' => true,
      'after' => true,
      'mode' => 'or',
      'comparison' => 'LIKE',
      'wildcardAny' => '*',
      'wildcardOne' => '?',
      'field' => [$this->aliasField('title'), $this->aliasField('description'), $this->aliasField('name')]
    ])
    ->add('type', 'Attachment.SessionLike', [
      'before' => true,
      'after' => true,
      'mode' => 'or',
      'comparison' => 'LIKE',
      'wildcardAny' => '*',
      'wildcardOne' => '?',
      'field' => [$this->aliasField('type')]
    ])
    ->add('tag', 'Attachment.SessionCallback',[
      'callback' => function ($query, $args, $filter) {
        return $query
        ->distinct($this->aliasField('id'))
        ->matching('Atags', function (Query $query) use ($args) {
          return $query
          ->where([
            'OR' => [
              'Atags.name' => $args['tag'],
              'Atags.slug' => $args['tag']
            ]
          ]);
        });
      }
    ]);
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
    ->allowEmpty('id', 'create');

    $validator
    ->requirePresence('name', 'create')
    ->notEmpty('name');

    $validator
    ->requirePresence('type', 'create')
    ->notEmpty('type');

    $validator
    ->requirePresence('subtype', 'create')
    ->notEmpty('subtype');

    $validator
    ->integer('size')
    ->requirePresence('size', 'create')
    ->notEmpty('size');

    // MD% Uique
    if(Configure::check('Attachment.md5Unique') )
    {
      $validator
      ->requirePresence('md5', 'create')
      ->notEmpty('md5')
      ->add('md5', 'unique', ['rule' => 'validateUnique', 'provider' => 'table','message' => 'Attachment already exists']);
    }else
    {
      $validator
      ->requirePresence('md5', 'create')
      ->notEmpty('md5');
    }


    $validator
    ->allowEmpty('profile');

    $validator
    ->dateTime('date')
    ->allowEmpty('date');

    $validator
    ->allowEmpty('title');

    $validator
    ->allowEmpty('description');

    $validator
    ->allowEmpty('author');

    $validator
    ->allowEmpty('copyright');

    $validator
    ->allowEmpty('path');

    $validator
    ->allowEmpty('embed');

    return $validator;
  }
}
