<?php
class validatorRules
{
	const EMAIL    = '/^[_a-zA-Z0-9-]+(\.[_a-zA-Z0-9-]+)*@[a-zA-Z0-9-]+(\.[a-zA-Z0-9-]+)*(\.[a-zA-Z]{2,4})$/';
	const NAME		= '/^[а-яА-ЯёЁЇїІіЄєҐґ’\- ]{2,15}$/u';
	const URL		 = '/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_~]+\\.+[A-Za-z0-9\.\/%&=\?\-_~*]+$/i';
}
?>