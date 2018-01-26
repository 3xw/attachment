<?php
namespace Attachment\Model\Table;

use Attachment\Model\Entity\Attachment;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;
use Cake\Core\Configure;
use Attachment\Model\Rule\ExternalUrlIsValideRule;

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

  //use \Attachment\Model\Behavior\CustomTranslateTrait;

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
    $this->addBehavior('Attachment.External');

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

    if(Configure::read('Attachment.translate'))
    {
      $this->addBehavior('Translate', ['fields' => ['title','description']]);
    }

    // Add the behaviour to your table
    $this->addBehavior('Search.Search');

    // Setup search filter using search manager
    $this->searchManager()
    ->add('index', 'Attachment.SessionIndex', [])
    ->add('search', 'Attachment.SessionCallback',[
      'callback' => function ($query, $args, $filter) {
        $needle = '%'.$args['search'].'%';
        return $query
        ->distinct($this->aliasField('id'))
        ->leftJoin(['AA' => 'attachments_atags'],['AA.attachment_id = Attachments.id'])
        ->leftJoin(['Atags' => 'atags'],['Atags.id = AA.atag_id'])
        ->where([
          'OR' => [
            'Atags.name LIKE' => $needle,
            'Atags.slug LIKE' => $needle,
            'Attachments.title LIKE' => $needle,
            'Attachments.description LIKE' => $needle,
            'Attachments.name LIKE' => $needle,
          ]
        ])
        ;
      }
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

  public function patchEntity(EntityInterface $entity, array $data, array $options = [])
  {
    if(!Configure::read('Attachment.translate')) return parent::patchEntity($entity, $data, $options);

    if (!isset($options['associated']))
    {
      $options['associated'] = $this->_associations->keys();
    }
    $marshaller = $this->marshaller();
    $entity = $marshaller->merge($entity, $data, $options);

    if(!empty($entity['_translations']))
    {
      foreach ($entity['_translations'] as $locale => $fields)
      {
        foreach($fields as $key => $value)
        {
          if(!empty($value)){
            $entity->translation($locale)->set([$key => $value], ['guard' => false]);
          }else{
            $entity->translation($locale)->set([$key => $entity[$key]], ['guard' => false]);
          }
        }
        unset($data['_translations'][$locale]);
      }
    }

    return $entity;
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

    $validator
    ->integer('width')
    ->allowEmpty('width');

    $validator
    ->integer('height')
    ->allowEmpty('height');

    // MD5 Uique
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
    ->allowEmpty('path')
    ->add('path', 'externalUrlValide', [
      'rule' => 'externalUrlIsValide',
      'message' => __('You need to provide a valid url'),
      'provider' => 'table',
    ]);

    $validator
    ->allowEmpty('embed');

    return $validator;
  }

  public function externalUrlIsValide($value, array $context)
  {
    if (!empty($value) && substr($value, 0, 4) == 'http')
    {
      $headers = get_headers($value,1);
      if(substr($headers[0], 9, 3) != 200) return false;
    }
    return true;
  }
}
