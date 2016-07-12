<?php
namespace App\Model\Table;

use App\Model\Entity\Attachment;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Attachments Model
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
        $this->table('attachments');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');

        // custom behaviors
        $this->addBehavior('Embed', [
            'embed_field' => 'embed',
            'file_field' => 'path'
        ]);
        $this->addBehavior('Storage', [
            'file_field' => 'path'
        ]);
        $this->addBehavior('ATag', [
            'file_field' => 'path'
        ]);
        $this->belongsToMany('Atags', [
            'foreignKey' => 'attachment_id',
            'targetForeignKey' => 'atag_id',
            'joinTable' => 'attachments_atags'
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
            ->allowEmpty('id', 'create')
            ->requirePresence('name', 'create')
            ->notEmpty('name')
            ->requirePresence('type', 'create')
            ->notEmpty('type')
            ->requirePresence('subtype', 'create')
            ->notEmpty('subtype')
            ->add('size', 'valid', ['rule' => 'numeric'])
            ->requirePresence('size', 'create')
            ->notEmpty('size')
            ->add('md5', 'unique', ['rule' => 'validateUnique','provider' => 'table','message' => 'Attachment already exists'])
            ->allowEmpty('title')
            ->add('date', 'valid', ['rule' => 'datetime'])
            ->allowEmpty('date')
            ->allowEmpty('description')
            ->allowEmpty('author')
            ->allowEmpty('copyright')
            ->allowEmpty('path')
            ->allowEmpty('embed');

        return $validator;
    }
}
