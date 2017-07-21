<?php

function escape($string) {
	return htmlentities($string, ENT_QUOTES, 'UTF-8');
}

function unescape($string) {
	return html_entity_decode($string, ENT_QUOTES, 'UTF-8');
}