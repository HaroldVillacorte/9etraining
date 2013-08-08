<?php

namespace ZfcUserExtend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcUserExtend\Form\UserAdminForm;
use ZfcUserExtend\Form\ApiKeyForm;
use ZfcUserExtend\Entity\User;
use ZfcUserExtend\Entity\ApiKey;
use Zend\Crypt\Password\Bcrypt;

class UserAdminController extends AbstractActionController
{
    protected $em;

    public function indexAction()
    {
        $users = $this->getEntityManager()->getRepository('ZfcUserExtend\Entity\User')->findAll();

        return new ViewModel(array(
            'users' => $users,
        ));
    }

    public function addAction()
    {
        $id = (int) $this->params('id');
        $roles = $this->getEntityManager()
            ->getRepository('ZfcUserExtend\Entity\Role')->findAll();
        $roles_array = array();
        foreach ($roles as $role)
        {
            $roles_array[$role->getId()] = $role->getRoleId();
        }

        $user_roles_values = ($id) ? array($id) : array();

        $form = new UserAdminForm('user', $roles_array, 1, $user_roles_values, false);
        $form->setValidationGroup('csrf', 'username', 'password', 'passconf',
            'displayName', 'email', 'state', 'user_roles');

        if ($this->getRequest()->isPost())
        {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid())
            {
                $data = $form->getData();
                $user = $this->saveUser($data);
                if (!$user) {
                    return $this->redirect()->toRoute('useradmin');
                }
                else {
                    return $this->redirect()
                        ->toRoute('role', array('action' => 'view', 'id' => $user->getRoles()->getId()));
                }

            }
        }

        return new ViewModel(array(
            'form' => $form,
        ));
    }

    public function editAction()
    {
        $id = (int) $this->params('id');
        $user = $this->getEntityManager()->find('ZfcUserExtend\Entity\User', $id);
        if (!$id || !$user)
        {
            return $this->redirect()->toRoute('useradmin');
        }

        $roles = $this->getEntityManager()
            ->getRepository('ZfcUserExtend\Entity\Role')->findAll();
        $roles_array = array();
        foreach ($roles as $_role)
        {
            $roles_array[$_role->getId()] = $_role->getRoleId();
        }
        $user_roles_value = $user->getRoles()->getId();

        $form = new UserAdminForm('user', $roles_array, $user->getState(), $user_roles_value, false);
        $form->setValidationGroup('csrf', 'id','username', 'password', 'passconf','displayName', 'email', 'state', 'user_roles');
        $form->setBindOnValidate(false);
        $form->bind($user);
        $form->get('submit')->setAttribute('value', 'Save');

        if ($this->getRequest()->isPost())
        {
            $form->setData($this->getRequest()->getPost());

            if ($form->isValid())
            {
                $data = $this->getRequest()->getPost();
                $user = $this->saveUser($data);
                if (!$user) {
                    return $this->redirect()->toRoute('useradmin');
                }
                else {
                    return $this->redirect()
                        ->toRoute('role', array('action' => 'view', 'id' => $user->getRoles()->getId()));
                }
            }
        }

        return new ViewModel(array(
            'form' => $form,
            'id' => $id,
        ));
    }

    public function deleteAction()
    {
        $id = (int) $this->params('id');
        $user = $this->getEntityManager()->find('ZfcUserExtend\Entity\User', $id);
        if (!$id || !$user) {
            return $this->redirect()->toRoute('useradmin');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $this->deleteUser($user);
            }

            return $this->redirect()
                        ->toRoute('useradmin');
        }

        return new ViewModel(array(
            'id' => $id,
            'user' => $user,
        ));
    }

    public function viewAction()
    {
        $id = (int) $this->params('id');
        $user = $this->getEntityManager()->find('ZfcUserExtend\Entity\User', $id);

        $form = new ApiKeyForm('apiKey');

        $request = $this->getRequest();
        if ($request->isPost()) {

            $form->setData($this->getRequest()->getPost());

            if ($form->isValid())
            {
                if ($user->getApiKey()) {
                $apiKey = $user->getApiKey();
                $apiKey->setKeyValue();
                }
                else {
                    $apiKey = new ApiKey();
                    $apiKey->setKeyValue();
                    $apiKey->setUser($user);
                }
                $this->getEntityManager()->persist($apiKey);
                $this->getEntityManager()->persist($user);

                try {
                    $this->getEntityManager()->flush();
                    $this->flashMessenger()->setNamespace('success')
                        ->addMessage('Api key was successfully saved.');
                }
                catch (Exception $e) {
                    $this->flashMessenger()->setNamespace('error')
                        ->addMessage('There was problem saving the api key.');
                }
            }

            return $this->redirect()
                ->toRoute('useradmin', array('action' => 'view', 'id' => $user->getId()));
        }

        return new ViewModel(array(
            'user' => $user,
            'form' => $form,
        ));
    }

    public function saveUser($data = array())
    {
        $user = (isset($data['id']) && $data['id'] != '') ? $this->getEntityManager()
            ->find('ZfcUserExtend\Entity\User', (int) $data['id']) : new User();
        $user->setUsername($data['username']);

        // Hash the password.
        if (isset($data['password']) && $data['password'] != '')
        {
            $crypt = new Bcrypt();
            $password = $crypt->create($data['password']);
            $user->setPassword($password);
        }

        $user->setDisplayName($data['displayName']);
        $user->setEmail($data['email']);
        $user->setState($data['state']);
        $role = $this->getEntityManager()
                ->find('ZfcUserExtend\Entity\Role', (int) $data['user_roles']);
        $user->setRoles($role);

        $this->getEntityManager()->persist($user);
        try {
            $this->getEntityManager()->flush();
            $this->flashMessenger()->setNamespace('success')
                ->addMessage('User was successfully saved.');
            return $user;
        }
        catch (Exception $e) {
            $this->flashMessenger()->setNamespace('error')
                ->addMessage('There was problem saving the user.');
            return false;;
        }
    }

    public function deleteUser(User $user)
    {
        $this->getEntityManager()->remove($user);
        try {
            $this->getEntityManager()->flush();
            $this->flashMessenger()->setNamespace('success')
                ->addMessage('User was successfully deleted.');
        }
        catch (Exception $e) {
            $this->flashMessenger()->setNamespace('error')
                ->addMessage('There was a deleting the user');
        }
    }

    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getEntityManager()
    {
        if ($this->em === null)
        {
            $this->em = $this->getServiceLocator()
                ->get('doctrine.entitymanager.orm_default');
        }
        return $this->em;
    }

}
