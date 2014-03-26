<?php

namespace ContaoCommunityAlliance\Contao\RootRelations;

/**
 * @author Oliver Hoff <oliver@hofff.com>
 */
final class ControllerProxy extends \Controller {

	/** @var ControllerProxy */
	private static $instance = null;

	/**
	 * @return ControllerProxy
	 */
	public static function getInstance() {
		isset(self::$instance) || self::$instance = new self;
		return self::$instance;
	}

	protected function __construct() {
		parent::__construct();
		$this->import('Database');
	}

	protected function __clone() {
	}

	public function __call($method, $args) {
		if(method_exists($this, $method)) {
			return call_user_func_array(array($this, $method), $args);
		}
		throw new \Exception('Undefined method: Controller::' . $method);
	}

	public static function __callStatic($method, $args) {
		if(method_exists(__CLASS__, $method)) {
			return call_user_func_array(array(self::getInstance(), $method), $args);
		}
		throw new \Exception('Undefined method: Controller::' . $method);
	}

}
