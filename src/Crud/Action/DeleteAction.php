<?php
namespace Attachment\Crud\Action;

use \Crud\Event\Subject;

class DeleteAction extends \Crud\Action\DeleteAction
{
  /**
  * HTTP POST handler
  *
  * @param string $id Record id
  * @return \Cake\Network\Response
  */
  protected function _post($id = null)
  {
    $subject = $this->_subject();
    $subject->set(['id' => $id]);

    $entity = $this->_findRecord($id, $subject);

    $event = $this->_trigger('beforeDelete', $subject);
    if ($event->isStopped()) {
      return $this->_stopped($subject);
    }

    try
    {
      if ($this->_table()->delete($entity)) {
        $this->_success($subject);
      } else {
        $this->_error($subject);
      }
    }
    catch (\PDOException $e)
    {
      /*$subject->set(['data',[
        'code' => 400,
        'exception' => $e,
        'message' => __('unable to delete this Attachment. This attachment looks beeing in use by an other record. Please detatch the attachment to related record an then try to delete it again.')
      ]]);*/
      $subject->set(['error',[
        'code' => 400,
        'exception' => $e,
        'message' => __('unable to delete this Attachment. This attachment looks beeing in use by an other record. Please detatch the attachment to related record an then try to delete it again.')
      ]]);
      $this->_error($subject);
    }

    return $this->_redirect($subject, ['action' => 'index']);
  }
}
