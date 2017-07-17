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

require_once dirname(__FILE__).'/ETransactionsHtmlWriterAbstract.php';

/**
 * HTML write for PrestaShop 1.6
 */
class ETransactionsHtmlWriter extends ETransactionsHtmlWriterAbstract {
	protected function _alert($type, $content, $id, $show) {
		$attrs = '';
		if (!empty($id)) {
			$attrs .= sprintf(' id="%s"', $id);
		}
		if (!$show) {
			$attrs .= ' style="display: none"';
		}
		$tpl = '<div class="alert alert-%s"%s>%s</div>';
		$content = $content;
		$this->html(sprintf($tpl, $type, $attrs, $content));
	}

	public function alertConf($content, $id = null, $show = true) {
		$this->_alert('success', $content, $id, $show);
	}

	public function alertError($content, $id = null, $show = true) {
		$this->_alert('danger', $content, $id, $show);
	}

	public function alertWarn($content, $id = null, $show = true) {
		$this->_alert('warning', $content, $id, $show);
	}

	public function blockEnd() {
		$this->html('</div>');		
	}

	public function blockStart($id, $label, $image = null) {
		$tpl = '<div class="bootstrap panel pbx_panel" id="%s"><h3>%s</h3>';
		if (!empty($image)) {
			$label = sprintf('<img src="%s" alt="%s"/> %s', $this->escape($image), $label, $label);
		}
		$this->html(sprintf($tpl, $id, $label));
	}

	public function button($label, $type = 'submit') {
		$this->html(sprintf(
			'<button class="btn btn-default" type="%s">%s</button>',
			$type,
			$label
		));
	}

	public function checkbox($name, $checked = false, $value = '1') {
		$attrs = '';
		if ($checked) {
			$attrs .= ' checked="checked"';
		}
		$this->html(sprintf('<input type="checkbox" id="%s" name="%s" value="%s"%s/>',
			$this->escape($name), $this->escape($name), $this->escape($value), $attrs));
	}

	public function formAlert($id, $content, $show = true, $marginTop = '-50px') {
		$styles = array(
			'width: 300px',
			'position: absolute',
			'margin-left: 560px',
			'margin-top: '.$marginTop,
			'font-weight: normal',
		);
		$styles = array();
		if (!$show) {
			$styles[] = 'display: none';
		}
		$this->html('<div class="row"><div class="col-lg-5 col-lg-offset-3">');
		$tpl = '<div class="alert alert-danger" style="%s" id="%s">%s</div>';
		$this->html(sprintf($tpl, implode('; ', $styles), $this->escape($id), $content));
		$this->html('</div></div>');
	}

	public function formButton($name, $label) {
		$this->html('<div class="row"><div class="col-lg-5 col-lg-offset-3">');
		if (empty($name)) {
			$tpl = '<button class="btn btn-default" type="submit">%s</button>';
			$this->html(sprintf($tpl, $label));
		}
		else {
			$tpl = '<input class="button" class="btn btn-default" type="submit" name="%s" value="%s" />';
			$this->html(sprintf($tpl, $this->escape($name), $label));
		}
		$this->html('</div></div>');
	}

	public function formCheckbox($name, $label, $checked = false, $value = '1', $description = null, $show = true) {
		$styles = $show ? '' : ' style="display:none;"';
		$tpl = '<div class="form-group" id="%s_container"%s><div class="checkbox col-lg-5 col-lg-offset-3"><label>';
		$this->html(sprintf($tpl, $this->escape($name), $styles));

		$attrs = '';
		if ($checked) {
			$attrs .= ' checked="checked"';
		}
		$this->html(sprintf('<input type="checkbox" id="%s" name="%s" value="%s"%s/>',
			$this->escape($name), $this->escape($name), $this->escape($value), $attrs));
		$this->html($label);
		$this->html('</label>');
		$this->formDescription($description);
		$this->html('</div></div>');
	}

	public function formDescription($description) {
		if (!empty($description)) {
			$tpl = '<span class="help-block">%s</span>';
			$this->html(sprintf($tpl, $description));
		}
	}

	public function formElementEnd() {
		$this->html('</div></div>');
	}

	public function formElementStart($name, $label, $show = true) {
		$styles = $show ? '' : ' style="display:none;"';
		$tpl = '<div class="form-group" id="%s_container"%s>';
		$this->html(sprintf($tpl, $this->escape($name), $styles));
		$this->formLabel($name, $label);
		$this->html('<div class="col-lg-5">');
	}

	public function formEnd() {
		$this->html('</form>');
	}

	public function formFile($name, $label, $description = null, $show = true) {
		$this->formElementStart($name, $label, $show);
		$this->html(sprintf('<input type="file" id="%s" name="%s"/>',
			$this->escape($name), $this->escape($name)));
		$this->formDescription($description);
		$this->formElementEnd();
	}

	public function formLabel($name, $label) {
		$tpl = '<label for="%s" class="control-label col-lg-3">%s</label>';
		$this->html(sprintf($tpl, $this->escape($name), $label));
	}

	public function formSelect($name, $label, array $options, $current = null, $default = null, $description = null, $show = true, $sortOptions = true) {
		// Sort options if needed
		if ($sortOptions) {
			asort($options);
		}

		// Check current value
		if (is_null($current) || ($current === false) || !array_key_exists($current, $options)) {
			$current = $default;
		}

		$this->formElementStart($name, $label, $show);
		$this->select($name, $options, $current);
		$this->formDescription($description);
		$this->formElementEnd();
	}

	public function formStart($id, $action) {
		$tpl = '<form id="%s" class="defaultForm form-horizontal" action="%s" method="post" enctype="multipart/form-data">';
		$this->html(sprintf($tpl, $this->escape($id), $this->escape($action)));
	}

	public function formText($name, $label, $current = '', $description = null, $size = null, $more = null, $show = true) {
		$this->formElementStart($name, $label, $show);
		$attrs = '';
		if (!empty($size)) {
			$attrs = sprintf(' size="%d"', intval($size));
		}
		$this->html(sprintf('<input type="text" id="%s" name="%s" value="%s"%s/>', $this->escape($name), $this->escape($name), $this->escape($current), $attrs));
		if($more) {
			$this->html(sprintf('<b>&nbsp;&nbsp;%s</b>', $more));
		}
		$this->formDescription($description);
		$this->formElementEnd();
	}

	public function text($name, $current = '', $size = null) {
		$attrs = '';
		if (!empty($size)) {
			$attrs = sprintf(' size="%d"', intval($size));
		}
		$this->html(sprintf('<input type="text" id="%s" name="%s" value="%s"%s/>', $this->escape($name), $this->escape($name), $this->escape($current), $attrs));
	}

	public function rawRowStart() {
		$this->html('<div class="row"><div class="col-xs-12">');
	}

	public function rawRowEnd() {
		$this->html('</div></div>');
	}

	public function select($name, array $options, $current = null) {
		$this->html(sprintf('<select name="%s" id="%s">', $this->escape($name), $this->escape($name)));

		// For each option
		$optionTpl = '<option value="%s"%s>%s</option>';
		foreach($options as $value => $label) {
			$attrs = $current == $value ? ' selected="selected"' : '';
			$this->html(sprintf($optionTpl, $this->escape($value), $attrs, $label));
		}

		$this->html('</select>');
	}
}
