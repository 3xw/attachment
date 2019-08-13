<?php
namespace Attachment\Model\Table;

use Attachment\Model\Entity\Attachment;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Search\Manager;
use Cake\Core\Configure;
use Attachment\Http\Exception\UploadException;

class AttachmentsTable extends Table
{

  public function initialize(array $config):void
  {
    parent::initialize($config);

    $this->setTable('attachments');
    $this->setDisplayField('name');
    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');

    // custom behaviors
    $this->addBehavior('Attachment.External');

    $this->addBehavior('Attachment.Embed', [
      'embed_field' => 'embed',
      'file_field' => 'path'
    ]);
    $this->addBehavior('Attachment.Fly', [
      'file_field' => 'path'
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
      $this->addBehavior('Trois\Utils\ORM\Behavior\TranslateBehavior', ['fields' => ['title','description']]);
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
        ->leftJoin(['AAtags' => 'attachments_atags'],['AAtags.attachment_id = Attachments.id'])
        ->leftJoin(['Atags' => 'atags'],['Atags.id = AAtags.atag_id'])
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

  /**
  * Default validation rules.
  *
  * @param \Cake\Validation\Validator $validator Validator instance.
  * @return \Cake\Validation\Validator
  */
  public function validationDefault(Validator $validator): Validator
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
    ->allowEmpty('path')
    ->add('path', 'externalUrlValide', [
      'rule' => 'externalUrlIsValide',
      'message' => __('You need to provide a valid url'),
      'provider' => 'table',
    ])
    ->add('path', 'uploadValide', [
      'rule' => 'uploadIsValide',
      'provider' => 'table',
    ]);

    $validator
    ->allowEmpty('embed');

    return $validator;
  }

  public function externalUrlIsValide($value, array $context)
  {
    if(!empty($context['data']['type']) && $context['data']['type'] == 'embed') return true;
    if (!empty($value) && !is_array($value) && substr($value, 0, 4) == 'http')
    {
      $headers = get_headers($value,1);
      if(substr($headers[0], 9, 3) != 200) return false;
    }
    return true;
  }

  public function uploadIsValide($value, array $context)
  {
    if (!empty($value) && is_array($value) && $value['error'] !== UPLOAD_ERR_OK) throw new UploadException($value['error']);
    return true;
  }
}
