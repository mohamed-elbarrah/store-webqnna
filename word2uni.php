<?php
/**
 * word2uni
 * This code is a part of aCAPTCHA project, This copyright notice MUST stay intact for use
 * @package aCAPTCHA 
 * @author Abd Allatif Eymsh
 * @copyright (c) 2012
 * @param string $word
 * @license http://opensource.org/licenses/gpl-license.php GNU General Public License v2
 */
function word2uni($word)
{
	$word = flip_arabic_text($word);
	$new_string = '';
	$isolated_beginning = array('ء');
	$isolated_end = array('ا', 'د', 'ذ', 'أ', 'آ', 'ر', 'ؤ', 'ء', 'ز', 'و', 'ى', 'ة');

	$arabic_chars = array
	(
		'ا' => array(
			'beginning' => '&#xFE8E;',
			'middle' => '&#xFE8E;',
			'end' => '&#xFE8E;',
			'isolated' => '&#xFE8D;'
		),

		'ؤ' => array(
			'beginning' => '&#xFE85;',
			'middle' => '&#xFE85;',
			'end' => '&#xFE85;',
			'isolated' => '&#xFE86;'
		),
		'ء' => array(
			'beginning' => '&#xFE80;',
			'middle' => '&#xFE80;',
			'end' => '&#xFE80;',
			'isolated' => '&#xFE80;'
		),
		'أ' => array(
			'beginning' => '&#xFE84;',
			'middle' => '&#xFE84;',
			'end' => '&#xFE84;',
			'isolated' => '&#xFE83;'
		),
		'آ' => array(
			'beginning' => '&#xFE82;',
			'middle' => '&#xFE82;',
			'end' => '&#xFE82;',
			'isolated' => '&#xFE81;'
		),
		'ى' => array(
			'beginning' => '&#xFEF0;',
			'middle' => '&#xFEF0;',
			'end' => '&#xFEF0;',
			'isolated' => '&#xFEEF;'
		),
		'ب' => array(
			'beginning' => '&#xFE91;',
			'middle' => '&#xFE92;',
			'end' => '&#xFE90;',
			'isolated' => '&#xFE8F;'
		),
		'ت' => array(
			'beginning' => '&#xFE97;',
			'middle' => '&#xFE98;',
			'end' => '&#xFE96;',
			'isolated' => '&#xFE95;'
		),
		'ث' => array(
			'beginning' => '&#xFE9B;',
			'middle' => '&#xFE9C;',
			'end' => '&#xFE9A;',
			'isolated' => '&#xFE99;'
		),
		'ج' => array(
			'beginning' => '&#xFE9F;',
			'middle' => '&#xFEA0;',
			'end' => '&#xFE9E;',
			'isolated' => '&#xFE9D;'
		),
		'ح' => array(
			'beginning' => '&#xFEA3;',
			'middle' => '&#xFEA4;',
			'end' => '&#xFEA2;',
			'isolated' => '&#xFEA1;'
		),
		'خ' => array(
			'beginning' => '&#xFEA7;',
			'middle' => '&#xFEA8;',
			'end' => '&#xFEA6;',
			'isolated' => '&#xFEA5;'
		),
		'د' => array(
			'beginning' => '&#xFEAA;',
			'middle' => '&#xFEAA;',
			'end' => '&#xFEAA;',
			'isolated' => '&#xFEA9;'
		),
		'ذ' => array(
			'beginning' => '&#xFEAC;',
			'middle' => '&#xFEAC;',
			'end' => '&#xFEAC;',
			'isolated' => '&#xFEAB;'
		),
		'ر' => array(
			'beginning' => '&#xFEAE;',
			'middle' => '&#xFEAE;',
			'end' => '&#xFEAE;',
			'isolated' => '&#xFEAD;'
		),
		'ز' => array(
			'beginning' => '&#xFEB0;',
			'middle' => '&#xFEB0;',
			'end' => '&#xFEB0;',
			'isolated' => '&#xFEAF;'
		),
		'س' => array(
			'beginning' => '&#xFEB3;',
			'middle' => '&#xFEB4;',
			'end' => '&#xFEB2;',
			'isolated' => '&#xFEB1;'
		),
		'ش' => array(
			'beginning' => '&#xFEB7;',
			'middle' => '&#xFEB8;',
			'end' => '&#xFEB6;',
			'isolated' => '&#xFEB5;'
		),
		'ص' => array(
			'beginning' => '&#xFEBB;',
			'middle' => '&#xFEBC;',
			'end' => '&#xFEBA;',
			'isolated' => '&#xFEB9;'
		),
		'ض' => array(
			'beginning' => '&#xFEBF;',
			'middle' => '&#xFEC0;',
			'end' => '&#xFEBE;',
			'isolated' => '&#xFEBD;'
		),
		'ط' => array(
			'beginning' => '&#xFEC3;',
			'middle' => '&#xFEC4;',
			'end' => '&#xFEC2;',
			'isolated' => '&#xFEC1;'
		),
		'ظ' => array(
			'beginning' => '&#xFEC7;',
			'middle' => '&#xFEC8;',
			'end' => '&#xFEC6;',
			'isolated' => '&#xFEC5;'
		),
		'ع' => array(
			'beginning' => '&#xFECB;',
			'middle' => '&#xFECC;',
			'end' => '&#xFECA;',
			'isolated' => '&#xFEC9;'
		),
		'غ' => array(
			'beginning' => '&#xFECF;',
			'middle' => '&#xFED0;',
			'end' => '&#xFECE;',
			'isolated' => '&#xFECD;'
		),
		'ف' => array(
			'beginning' => '&#xFED3;',
			'middle' => '&#xFED4;',
			'end' => '&#xFED2;',
			'isolated' => '&#xFED1;'
		),
		'ق' => array(
			'beginning' => '&#xFED7;',
			'middle' => '&#xFED8;',
			'end' => '&#xFED6;',
			'isolated' => '&#xFED5;'
		),
		'ك' => array(
			'beginning' => '&#xFEDB;',
			'middle' => '&#xFEDC;',
			'end' => '&#xFEDA;',
			'isolated' => '&#xFED9;'
		),
		'ل' => array(
			'beginning' => '&#xFEDF;',
			'middle' => '&#xFEE0;',
			'end' => '&#xFEDE;',
			'isolated' => '&#xFEDD;'
		),
		'م' => array(
			'beginning' => '&#xFEE3;',
			'middle' => '&#xFEE4;',
			'end' => '&#xFEE2;',
			'isolated' => '&#xFEE1;'
		),
		'ن' => array(
			'beginning' => '&#xFEE7;',
			'middle' => '&#xFEE8;',
			'end' => '&#xFEE6;',
			'isolated' => '&#xFEE5;'
		),
		'ه' => array(
			'beginning' => '&#xFEEB;',
			'middle' => '&#xFEEC;',
			'end' => '&#xFEEA;',
			'isolated' => '&#xFEE9;'
		),
		'و' => array(
			'beginning' => '&#xFEEE;',
			'middle' => '&#xFEEE;',
			'end' => '&#xFEEE;',
			'isolated' => '&#xFEED;'
		),
		'ي' => array(
			'beginning' => '&#xFEF3;',
			'middle' => '&#xFEF4;',
			'end' => '&#xFEF2;',
			'isolated' => '&#xFEF1;'
		),
		'ئ' => array(
			'beginning' => '&#xFE8B;',
			'middle' => '&#xFE8C;',
			'end' => '&#xFE8A;',
			'isolated' => '&#xFE89;'
		),
		'ة' => array(
			'beginning' => '&#xFE94;',
			'middle' => '&#xFE94;',
			'end' => '&#xFE94;',
			'isolated' => '&#xFE93;'
		)
	);

	$word = flip_non_arabic_words($word, $arabic_chars);

	// Split the input string into an array of characters
	$chars = preg_split('//u', $word, null, PREG_SPLIT_NO_EMPTY);

	// Loop through the characters
	foreach ($chars as $key => $char) {
		$start_connected = false;
		$end_connected = false;
		$position = '';

		// Check if the character is a space
		if ($char == " ") {
			$position = "Space";
		}
		// Check if the character is an isolated character
		elseif (($key == 0 || $chars[$key - 1] == " ") && ($key == count($chars) - 1 || $chars[$key + 1] == " ")) {
			$position = "Isolated";
		}
		// Check if the character is at the beginning of a word
		elseif ($key == 0 || $chars[$key - 1] == " ") {
			if (!in_array($chars[$key + 1], $isolated_end) && !in_array($char, $isolated_beginning))
				$start_connected = true;
			$end_connected = false;
			$position = "Beginning";
		}
		// Check if the character is at the end of a word
		elseif ($key == count($chars) - 1 || $chars[$key + 1] == " ") {
			$start_connected = false;
			if (!in_array($chars[$key - 1], $isolated_beginning) && !in_array($char, $isolated_end))
				$end_connected = true;
			$position = "End";
		}
		// Otherwise, the character is in the middle of a word
		else {
			if (!in_array($chars[$key - 1], $isolated_beginning) && !in_array($char, $isolated_end))
				$end_connected = true;
			if (!in_array($chars[$key + 1], $isolated_end) && !in_array($char, $isolated_beginning))
				$start_connected = true;
			$position = "Middle";
		}

		if (isset($arabic_chars[$char])) {
			if ($start_connected && $end_connected)
				$new_string .= $arabic_chars[$char]['middle'];
			elseif ($start_connected && !$end_connected)
				$new_string .= $arabic_chars[$char]['end'];
			elseif (!$start_connected && $end_connected)
				$new_string .= $arabic_chars[$char]['beginning'];
			elseif (!$start_connected && !$end_connected)
				$new_string .= $arabic_chars[$char]['isolated'];
		} else {
			$new_string .= $char;
		}

	}
	return $new_string;
}

function flip_arabic_text($text)
{
	$length = mb_strlen($text);
	$flipped_text = '';
	while ($length-- > 0) {
		$flipped_text .= mb_substr($text, $length, 1);
	}
	return $flipped_text;
}



function flip_non_arabic_words($string, $arabic_chars)
{
	// Split the string into an array of words
	$words = explode(' ', $string);

	// Loop through each word and flip it if none of its characters are present in the given array
	foreach ($words as &$word) {
		$chars = mb_split('(?<!^)(?!$)', $word);
		if (count(array_intersect(array_keys($arabic_chars), $chars)) == 0) {
			$word = flip_arabic_text($word);
		}
	}

	// Join the array of words back into a string
	return implode(' ', $words);
}