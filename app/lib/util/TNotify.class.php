<?php

class TNotify
{
	function __construct($type, $message, $title = "Aviso", $time = 5000)
	{
		switch ($type)
		{
			case 'success':
				# code...
				break;
			case 'info':
				break;
			case 'error':
				break;
			default:
				# code...
				break;
		}

		TScript::create("notify('{$title}', '{$message}', $time)");
	}
}