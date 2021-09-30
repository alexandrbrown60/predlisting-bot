<?php 

class KeyboardButton {
	public $text;
	public $data;
	public function __construct($text, $data) {
		$this->text = $text;
		$this->data = $data;
	}
}