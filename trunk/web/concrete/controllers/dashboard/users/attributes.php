<?php
defined('C5_EXECUTE') or die(_("Access Denied."));

Loader::model('attribute/categories/user');

class DashboardUsersAttributesController extends Controller {
	
	public $helpers = array('form');
	
	public function __construct() {
		parent::__construct();
		$otypes = AttributeType::getList('user');
		$types = array();
		foreach($otypes as $at) {
			$types[$at->getAttributeTypeID()] = $at->getAttributeTypeName();
		}
		$this->set('types', $types);
	}
	
	public function delete($akID, $token = null){
		try {
			$ak = UserAttributeKey::getByID($akID); 
				
			if(!($ak instanceof UserAttributeKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('delete_attribute', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->delete();
			
			$this->redirect("/dashboard/users/attributes", 'attribute_deleted');
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}
	
	public function activate($akID, $token = null) {
		try {
			$ak = UserAttributeKey::getByID($akID); 
				
			if(!($ak instanceof UserAttributeKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('attribute_activate', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->activate();
			
			$this->redirect("/dashboard/users/attributes", 'edit', $akID);
			
		} catch (Exception $e) {
			$this->set('error', $e);
		}
	}
	
	public function deactivate($akID, $token = null) {
			$ak = UserAttributeKey::getByID($akID); 
				
			if(!($ak instanceof UserAttributeKey)) {
				throw new Exception(t('Invalid attribute ID.'));
			}
	
			$valt = Loader::helper('validation/token');
			if (!$valt->validate('attribute_deactivate', $token)) {
				throw new Exception($valt->getErrorMessage());
			}
			
			$ak->deactivate();
			
			$this->redirect("/dashboard/users/attributes", 'edit', $akID);
	}
	
	public function select_type() {
		$atID = $this->request('atID');
		$at = AttributeType::getByID($atID);
		$this->set('type', $at);
		$this->set('category', AttributeKeyCategory::getByHandle('user'));
	}
	
	public function view() {
		$attribs = UserAttributeKey::getList();
		$this->set('attribs', $attribs);
	}
	
	public function add() {
		$this->select_type();
		$type = $this->get('type');
		$cnt = $type->getController();
		$e = $cnt->validateKey($this->post());
		if ($e->has()) {
			$this->set('error', $e);
		} else {
			$type = AttributeType::getByID($this->post('atID'));
			$args = array(
				'akHandle' => $this->post('akHandle'),
				'akName' => $this->post('akName'),
				'akIsSearchable' => $this->post('akIsSearchable'),
				'akIsSearchableIndexed' => $this->post('akIsSearchableIndexed'),
				'uakProfileDisplay' => $this->post('uakProfileDisplay'),
				'uakMemberListDisplay' => $this->post('uakMemberListDisplay'),
				'uakProfileEdit' => $this->post('uakProfileEdit'),
				'uakProfileEditRequired' => $this->post('uakProfileEditRequired'),
				'uakRegisterEdit' => $this->post('uakRegisterEdit'),
				'uakRegisterEditRequired' => $this->post('uakRegisterEditRequired'),				
				'akIsAutoCreated' => 0,
				'akIsEditable' => 1
			);
			$ak = UserAttributeKey::add($type, $this->post());
			$this->redirect('/dashboard/users/attributes/', 'attribute_created');
		}
	}

	public function attribute_deleted() {
		$this->set('message', t('User Attribute Deleted.'));
	}
	
	public function attribute_created() {
		$this->set('message', t('User Attribute Created.'));
	}

	public function attribute_updated() {
		$this->set('message', t('User Attribute Updated.'));
	}
	
	public function edit($akID = 0) {
		if ($this->post('akID')) {
			$akID = $this->post('akID');
		}
		$key = UserAttributeKey::getByID($akID);
		$type = $key->getAttributeType();
		$this->set('key', $key);
		$this->set('type', $type);
		$this->set('category', AttributeKeyCategory::getByHandle('user'));
		
		if ($this->isPost()) {
			$cnt = $type->getController();
			$cnt->setAttributeKey($key);
			$e = $cnt->validateKey($this->post());
			if ($e->has()) {
				$this->set('error', $e);
			} else {
				$args = array(
					'akHandle' => $this->post('akHandle'),
					'akName' => $this->post('akName'),
					'akIsSearchable' => $this->post('akIsSearchable'),
					'akIsSearchableIndexed' => $this->post('akIsSearchableIndexed'),
					'uakProfileDisplay' => $this->post('uakProfileDisplay'),
					'uakMemberListDisplay' => $this->post('uakMemberListDisplay'),
					'uakProfileEdit' => $this->post('uakProfileEdit'),
					'uakProfileEditRequired' => $this->post('uakProfileEditRequired'),
					'uakRegisterEdit' => $this->post('uakRegisterEdit'),
					'uakRegisterEditRequired' => $this->post('uakRegisterEditRequired'),				
					'akIsAutoCreated' => 0,
					'akIsEditable' => 1
				);
	
				$key->update($this->post());
				$this->redirect('/dashboard/users/attributes', 'attribute_updated');
			}
		}
	}
	
}