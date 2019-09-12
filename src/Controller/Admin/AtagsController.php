<?php
namespace Attachment\Controller\Admin;

use Attachment\Controller\AppController;

/**
 * Atags Controller
 *
 * @property \Attachment\Model\Table\AtagsTable $Atags
 *
 * @method \Attachment\Model\Entity\Atag[] paginate($object = null, array $settings = [])
 */
class AtagsController extends AppController
{
  public function initialize():void
  {
    parent::initialize();
    $this->loadComponent('Search.Prg', [
      'actions' => ['index']
    ]);
  }

    /**
     * Index method
     *
     * @return \Cake\Http\Response|void
     */
    public function index()
    {
        $query = $this->Atags->find('search', ['search' => $this->request->query])->contain(['Users', 'AtagTypes']);
        if (!empty($this->request->getQuery('q'))) {
          if (!$query->count()) {
            $this->Flash->error(__('No result.'));
          }else{
            $this->Flash->success($query->count()." ".__('result(s).'));
          }
          $this->set('q',$this->request->getQuery('q'));
        }
        $atags = $this->paginate($query);
        $this->set(compact('atags'));
    }

    /**
     * View method
     *
     * @param string|null $id Atag id.
     * @return \Cake\Http\Response|void
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function view($id = null)
    {
        $atag = $this->Atags->get($id, [
            'contain' => ['Users', 'AtagTypes', 'Attachments']
        ]);

        $this->set('atag', $atag);
        $this->set('_serialize', ['atag']);
    }

    /**
     * Add method
     *
     * @return \Cake\Http\Response|null Redirects on successful add, renders view otherwise.
     */
    public function add()
    {
        $atag = $this->Atags->newEntity();
        if ($this->request->is('post')) {
            $atag = $this->Atags->patchEntity($atag, $this->request->getData());
            if ($this->Atags->save($atag)) {
                $this->Flash->success(__('The atag has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The atag could not be saved. Please, try again.'));
        }
        $users = $this->Atags->Users->find('list', ['limit' => 200]);
        $atagTypes = $this->Atags->AtagTypes->find('list', ['limit' => 200]);
        $attachments = $this->Atags->Attachments->find('list', ['limit' => 200]);
        $this->set(compact('atag', 'users', 'atagTypes', 'attachments'));
        $this->set('_serialize', ['atag']);
    }

    /**
     * Edit method
     *
     * @param string|null $id Atag id.
     * @return \Cake\Http\Response|null Redirects on successful edit, renders view otherwise.
     * @throws \Cake\Network\Exception\NotFoundException When record not found.
     */
    public function edit($id = null)
    {
        $atag = $this->Atags->get($id, [
            'contain' => ['Attachments']
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $atag = $this->Atags->patchEntity($atag, $this->request->getData());
            if ($this->Atags->save($atag)) {
                $this->Flash->success(__('The atag has been saved.'));

                return $this->redirect(['action' => 'index']);
            }
            $this->Flash->error(__('The atag could not be saved. Please, try again.'));
        }
        $users = $this->Atags->Users->find('list', ['limit' => 200]);
        $atagTypes = $this->Atags->AtagTypes->find('list', ['limit' => 200]);
        $attachments = $this->Atags->Attachments->find('list', ['limit' => 200]);
        $this->set(compact('atag', 'users', 'atagTypes', 'attachments'));
        $this->set('_serialize', ['atag']);
    }

    /**
     * Delete method
     *
     * @param string|null $id Atag id.
     * @return \Cake\Http\Response|null Redirects to index.
     * @throws \Cake\Datasource\Exception\RecordNotFoundException When record not found.
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $atag = $this->Atags->get($id);
        if ($this->Atags->delete($atag)) {
            $this->Flash->success(__('The atag has been deleted.'));
        } else {
            $this->Flash->error(__('The atag could not be deleted. Please, try again.'));
        }

        return $this->redirect(['action' => 'index']);
    }
}
