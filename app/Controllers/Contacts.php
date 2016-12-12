<?php
/**
 *
 * Details:
 * PHP Messenger.
 *
 * Modified: 10-Dec-2016
 * Made Date: 05-Dec-2016
 * Author: Hosvir
 *
 */
namespace Messenger\Controllers;

use Messenger\Core\Database;

class Contacts extends Controller
{
    public function __construct()
    {
        $this->requiresLogin();
    }
    
    public function index()
    {
        $contact = $this->model('Contact');
        
        $this->view(
            'contacts',
            [
                'page' => 'contacts',
                'page_title' => 'Contacts - ' . SITE_NAME,
                'contacts' => $contact->getContacts(),
                'error' => ''
            ]
        );
    }
    
    public function edit($guid, $parameters = null)
    {
        $contact = $this->model('Contact');
        
        if ($parameters != null && $parameters['alias'] != '') {
            $error = $this->editContact($contact, $guid, $parameters);
            
            if ($error === 0) {
                $this->redirect('contacts');
            }
        } else {
            $error = '';
        }
        
        $this->view(
            'edit-contact',
            [
                'page' => 'edit-contact',
                'page_title' => 'Edit Contact - ' . SITE_NAME,
                'contact' => $contact->getByGuid($guid),
                'guid' => htmlspecialchars($guid),
                'token' => $_SESSION['token'],
                'error' => $error != '' ? $this->getErrorMessage($error) : $error
            ]
        );
    }

    public function delete($guid)
    {
        $contact = $this->model('Contact');
        $selected_contact = $contact->getContactByGuid($guid);
        $selected_contact->delete($guid);
        $this->redirect('contacts');
    }
    
    private function editContact($contact, $guid, $parameters)
    {
        if ($this->checkToken()) {
            $selected_contact = $contact->getByGuid($guid);
            return $selected_contact->setAlias($parameters['alias']);
        } else {
            return -1;
        }
    }
    
    private function getErrorMessage($code)
    {
        switch ($code) {
            case -1:
                return "Invalid token.";
            case 1:
                return "Alias must be less than 64 characters.";
            case 2:
                return "Failed to save contact, contact support.";
            default:
                return "Unknown error.";
        }
    }
}
