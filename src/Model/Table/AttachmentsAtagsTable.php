<?php
namespace Attachment\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * AttachmentsAtags Model
 *
 * @property \Cake\ORM\Association\BelongsTo $Attachments
 * @property \Cake\ORM\Association\BelongsTo $Atags
 *
 * @method \Attachment\Model\Entity\AttachmentsAtag get($primaryKey, $options = [])
 * @method \Attachment\Model\Entity\AttachmentsAtag newEntity($data = null, array $options = [])
 * @method \Attachment\Model\Entity\AttachmentsAtag[] newEntities(array $data, array $options = [])
 * @method \Attachment\Model\Entity\AttachmentsAtag|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Attachment\Model\Entity\AttachmentsAtag patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Attachment\Model\Entity\AttachmentsAtag[] patchEntities($entities, array $data, array $options = [])
 * @method \Attachment\Model\Entity\AttachmentsAtag findOrCreate($search, callable $callback = null)
 */
class AttachmentsAtagsTable extends Table
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

        $this->setTable('attachments_atags');
        $this->setDisplayField('attachment_id');
        $this->setPrimaryKey(['attachment_id', 'atag_id']);

        $this->belongsTo('Attachments', [
            'foreignKey' => 'attachment_id',
            'joinType' => 'INNER',
            'className' => 'Attachment.Attachments'
        ]);
        $this->belongsTo('Atags', [
            'foreignKey' => 'atag_id',
            'joinType' => 'INNER',
            'className' => 'Attachment.Atags'
        ]);
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
        $rules->add($rules->existsIn(['attachment_id'], 'Attachments'));
        $rules->add($rules->existsIn(['atag_id'], 'Atags'));

        return $rules;
    }
}
