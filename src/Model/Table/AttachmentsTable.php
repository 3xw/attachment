<?php
declare(strict_types=1);

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
  public function initialize(array $config): void
  {
    parent::initialize($config);

    $this->setTable('attachments');
    $this->setDisplayField('name');
    $this->setPrimaryKey('id');

    $this->addBehavior('Timestamp');

    // custom behaviors
    $this->addBehavior('Attachment\ORM\Behavior\UserIDBehavior');
    $this->addBehavior('Attachment\ORM\Behavior\ExternalBehavior');
    $this->addBehavior('Attachment\ORM\Behavior\EmbedBehavior', [
      'embed_field' => 'embed',
      'file_field' => 'path'
    ]);
    $this->addBehavior('Attachment\ORM\Behavior\FlyBehavior', [
      'file_field' => 'path'
    ]);
    $this->addBehavior('Attachment\ORM\Behavior\ATagBehavior', [
      'file_field' => 'path'
    ]);
    $this->addBehavior('Search.Search');
    if(Configure::read('Attachment.translate')) $this->addBehavior('Trois\Utils\ORM\Behavior\TranslateBehavior', ['fields' => ['title','description']]);

    // assoc
    $this->belongsTo('Users', [
      'type' => 'LEFT',
      'foreignKey' => 'user_id',
      'className' => 'Users',
    ]);
    $this->belongsToMany('Attachment.Atags', [
      'foreignKey' => 'attachment_id',
      'targetForeignKey' => 'atag_id',
      'joinTable' => 'attachments_atags',
      'className' => 'Attachment.Atags',
    ]);

    // settings
    $this->searchManager()
    ->add('uuid', 'Attachment.SessionQuerySettings') // wrapps query with session settings throught uuid
    ->add('search', 'Search.Callback',[
      'callback' => function ($query, $args, $filter) {
        $needle = '%'.$args['search'].'%';
        $query
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
        ]);

        return true;
      }
    ])
    ->add('type', 'Search.Callback', [
      'callback' => function ($query, $args, $filter)
      {
        $field = ($args['type'] == 'pdf')? 'Attachments.subtype' : 'Attachments.type' ;
        $query->where([$field => $args['type']]);
        if($args['type'] == 'application'){
          $query->where(['Attachments.subtype !=' => 'pdf']);
        }
        return true;
      }
    ])
    ->add('atags', 'Search.Callback',[
      'callback' => function ($query, $args, $filter)
      {
        $query
        ->matching('Atags', function (Query $q) use ($args) {
          return $q
          ->where([
            'OR' => [
              'Atags.name IN' => explode(',', $args['atags']),
              'Atags.slug IN' =>explode(',', $args['atags'])
            ]
          ]);
        });
        return true;
      }
    ])
    ->add('sort', 'Search.Callback',[
      'callback' => function ($query, $args, $filter)
      {
        $conditions = ($args['sort'] == 'created_desc')? ['Attachments.date' => 'DESC'] : ['Attachments.date' => 'ASC'];
        $query->order($conditions);
        return true;
      }
    ])
    ->add('filters', 'Search.Callback',[
      'callback' => function ($query, $args, $filter)
      {
        $conditions = [];
        $conditions['OR'] = [];
        foreach($args as $filter){
          switch($filter){
            case 'horizontal':
            array_push($conditions['OR'], ['Attachments.width > Attachments.height']);
            break;
            case 'vertical':
            array_push($conditions['OR'], ['Attachments.width < Attachments.height']);
            break;
            case 'square':
            array_push($conditions['OR'], ['Attachments.width = Attachments.height']);
            break;
          }
        }
        $query->where($conditions);

        return true;
      }
    ]);
  }

  public function find(string $type = 'all', array $options = []): Query
  {
    if ($type == 'all' && Configure::read('Attachment.translate')) $type = 'translations';
    return parent::find($type, $options);
  }

  public function validationDefault(Validator $validator): Validator
  {
    $validator
    ->uuid('id')
    ->allowEmptyString('id', 'create');

    $validator
    ->scalar('profile')
    ->maxLength('profile', 45)
    ->notEmptyFile('profile');

    $validator
    ->scalar('type')
    ->maxLength('type', 45)
    ->requirePresence('type', 'create')
    ->notEmptyString('type');

    $validator
    ->scalar('subtype')
    ->maxLength('subtype', 45)
    ->requirePresence('subtype', 'create')
    ->notEmptyString('subtype');

    $validator
    ->scalar('name')
    ->maxLength('name', 255)
    ->requirePresence('name', 'create')
    ->notEmptyString('name');

    $validator
    ->requirePresence('size', 'create')
    ->notEmptyString('size');

    // MD5 Uique
    if(Configure::check('Attachment.md5Unique') )
    {
      $validator
      ->requirePresence('md5', 'create')
      ->notEmpty('md5')
      ->add('md5', 'unique', ['rule' => 'validateUnique', 'provider' => 'table','message' => 'Attachment already exists']);
    }
    else
    {
      $validator
      ->requirePresence('md5', 'create')
      ->notEmpty('md5');
    }

    // PATH
    $validator
    ->allowEmpty('path')
    ->add('path', 'externalUrlValid', [
      'rule' => 'externalUrlIsValid',
      'message' => __('You need to provide a valid url'),
      'provider' => 'table',
    ])
    ->add('path', 'uploadValid', [
      'rule' => 'uploadIsValid',
      'provider' => 'table',
    ]);

    $validator
    ->scalar('embed')
    ->allowEmptyString('embed');

    $validator
    ->scalar('title')
    ->maxLength('title', 255)
    ->allowEmptyString('title');

    $validator
    ->dateTime('date')
    ->allowEmptyDateTime('date');

    $validator
    ->scalar('description')
    ->allowEmptyString('description');

    $validator
    ->scalar('author')
    ->maxLength('author', 255)
    ->allowEmptyString('author');

    $validator
    ->scalar('copyright')
    ->maxLength('copyright', 255)
    ->allowEmptyString('copyright');

    $validator
    ->nonNegativeInteger('width')
    ->allowEmptyString('width');

    $validator
    ->nonNegativeInteger('height')
    ->allowEmptyString('height');

    $validator
    ->nonNegativeInteger('duration')
    ->allowEmptyString('duration');

    $validator
    ->scalar('meta')
    ->allowEmptyString('meta');

    return $validator;
  }

  public function externalUrlIsValid($value, array $context)
  {
    if(!empty($context['data']['type']) && $context['data']['type'] == 'embed') return true;
    if (!empty($value) && is_string($value) && substr($value, 0, 4) == 'http')
    {
      $headers = get_headers($value,1);
      if(substr($headers[0], 9, 3) != 200) return false;
    }
    return true;
  }

  public function uploadIsValid($value, array $context)
  {
    if (!empty($value) && is_a($value, '\Zend\Diactoros\UploadedFile') && $value->getError() !== UPLOAD_ERR_OK) throw new UploadException($value->getError());
    return true;
  }

}
