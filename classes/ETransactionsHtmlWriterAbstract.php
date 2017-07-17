<?php
/**
 * E-Transactions PrestaShop Module
 *
 * Feel free to contact E-Transactions at support@e-transactions.fr for any
 * question.
 *
 * LICENSE: This source file is subject to the version 3.0 of the Open
 * Software License (OSL-3.0) that is available through the world-wide-web
 * at the following URI: http://opensource.org/licenses/OSL-3.0. If
 * you did not receive a copy of the OSL-3.0 license and are unable 
 * to obtain it through the web, please send a note to
 * support@e-transactions.fr so we can mail you a copy immediately.
 *
 * @author Olivier - BM Services (http://www.bm-services.com)
 * @copyright 2012-2015 E-Transactions
 * @license http://opensource.org/licenses/OSL-3.0
 * @link http://www.e-transactions.fr/
 * @since 2
 **/

if (!defined('_PS_VERSION_')) {
  exit;
}

/**
 * Base class of HTML writers
 */
abstract class ETransactionsHtmlWriterAbstract {
	private $_html = '';
	private $_js = '';

	public function __toString() {
		// Adding JavaScript
		$tpl = '%s<script type="text/javascript">'
			.'(function($){$(document).ready(function(){%s});})(jQuery);'
			.'</script>';
		return sprintf($tpl, $this->_html, $this->_js);
	}

	public function escape($text) {
		return Tools::htmlentitiesUTF8($text);
	}

	public function html($html) {
		$this->_html .= $html;
	}

	public function js($js) {
		$this->_js .= $js;
	}

	protected abstract function _alert($type, $content, $id, $show);
	public abstract function alertConf($content, $id = null, $show = true);
	public abstract function alertError($content, $id = null, $show = true);
	public abstract function alertWarn($content, $id = null, $show = true);
	public abstract function blockEnd();
	public abstract function blockStart($id, $label, $image = null);
	public abstract function button($label, $type = 'submit');
	public abstract function formAlert($id, $content, $show = true, $marginTop = '-50px');
	public abstract function formButton($name, $label);
	public abstract function formCheckbox($name, $label, $checked = false, $value = '1', $description = null, $show = true);
	public abstract function formDescription($description);
	public abstract function formElementEnd();
	public abstract function formElementStart($name, $label, $show = true);
	public abstract function formEnd();
	public abstract function formFile($name, $label, $description = null, $show = true);
	public abstract function formLabel($name, $label);
	public abstract function formSelect($name, $label, array $options, $current = null, $default = null, $description = null, $show = true, $sortOptions = true);
	public abstract function formStart($id, $action);
	public abstract function formText($name, $label, $current = '', $description = null, $size = null, $more = null, $show = true);
	public abstract function select($name, array $options, $current = null);
}
