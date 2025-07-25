<?php

function FindObjectInArrayByPropertyValue($array, $propertyName, $propertyValue)
{
	foreach ($array as $object)
	{
		if ($object->{$propertyName} == $propertyValue)
		{
			return $object;
		}
	}

	return null;
}

function FindAllObjectsInArrayByPropertyValue($array, $propertyName, $propertyValue, $operator = '==')
{
	$returnArray = [];
	foreach ($array as $object)
	{
		switch ($operator)
		{
			case '==':
				if ($object->{$propertyName} == $propertyValue)
				{
					$returnArray[] = $object;
				}
				break;
			case '>':
				if ($object->{$propertyName} > $propertyValue)
				{
					$returnArray[] = $object;
				}
				break;
			case '<':

				if ($object->{$propertyName} < $propertyValue)
				{
					$returnArray[] = $object;
				}
				break;
		}
	}

	return $returnArray;
}

function FindAllItemsInArrayByValue($array, $value, $operator = '==')
{
	$returnArray = [];
	foreach ($array as $item)
	{
		switch ($operator)
		{
			case '==':

				if ($item == $value)
				{
					$returnArray[] = $item;
				}
				break;
			case '>':

				if ($item > $value)
				{
					$returnArray[] = $item;
				}
				break;
			case '<':

				if ($item < $value)
				{
					$returnArray[] = $item;
				}
				break;
		}
	}

	return $returnArray;
}

function SumArrayValue($array, $propertyName)
{
	$sum = 0;
	foreach ($array as $object)
	{
		$sum += floatval($object->{$propertyName});
	}

	return $sum;
}

function GetClassConstants($className, $prefix = null)
{
	$r = new ReflectionClass($className);
	$constants = $r->getConstants();

	if ($prefix === null)
	{
		return $constants;
	}
	else
	{
		$matchingKeys = preg_grep('!^' . $prefix . '!', array_keys($constants));
		return array_intersect_key($constants, array_flip($matchingKeys));
	}
}

function RandomString($length, $allowedChars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ')
{
	$randomString = '';
	for ($i = 0; $i < $length; $i++)
	{
		$randomString .= $allowedChars[rand(0, strlen($allowedChars) - 1)];
	}

	return $randomString;
}

function IsAssociativeArray(array $array)
{
	$keys = array_keys($array);
	return array_keys($keys) !== $keys;
}

function IsIsoDate($dateString)
{
	$d = DateTime::createFromFormat('Y-m-d', $dateString);
	return $d && $d->format('Y-m-d') === $dateString;
}

function IsIsoDateTime($dateTimeString)
{
	$d = DateTime::createFromFormat('Y-m-d H:i:s', $dateTimeString);
	return $d && $d->format('Y-m-d H:i:s') === $dateTimeString;
}

function BoolToString(bool $bool)
{
	return $bool ? 'true' : 'false';
}

function BoolToInt(bool $bool)
{
	return $bool ? 1 : 0;
}

function ExternalSettingValue(string $value)
{
	$tvalue = rtrim($value, "\r\n");
	$lvalue = strtolower($tvalue);

	if ($lvalue === 'true')
	{
		return true;
	}
	elseif ($lvalue === 'false')
	{
		return false;
	}

	return $tvalue;
}

function Setting(string $name, $value)
{
	if (!defined('GROCY_' . $name))
	{
		// The content of a $name.txt file in /data/settingoverrides can overwrite the given setting (for embedded mode)
		$settingOverrideFile = GROCY_DATAPATH . '/settingoverrides/' . $name . '.txt';

		if (file_exists($settingOverrideFile))
		{
			define('GROCY_' . $name, ExternalSettingValue(file_get_contents($settingOverrideFile)));
		}
		elseif (getenv('GROCY_' . $name) !== false)
		{
			// An environment variable with the same name and prefix GROCY_ overwrites the given setting
			define('GROCY_' . $name, ExternalSettingValue(getenv('GROCY_' . $name)));
		}
		else
		{
			define('GROCY_' . $name, $value);
		}
	}
}

global $GROCY_DEFAULT_USER_SETTINGS;
$GROCY_DEFAULT_USER_SETTINGS = [];
function DefaultUserSetting(string $name, $value)
{
	global $GROCY_DEFAULT_USER_SETTINGS;

	if (!array_key_exists($name, $GROCY_DEFAULT_USER_SETTINGS))
	{
		$GROCY_DEFAULT_USER_SETTINGS[$name] = $value;
	}
}

function GetUserDisplayName($user)
{
	$displayName = '';

	if (empty($user->first_name) && !empty($user->last_name))
	{
		$displayName = $user->last_name;
	}
	elseif (empty($user->last_name) && !empty($user->first_name))
	{
		$displayName = $user->first_name;
	}
	elseif (!empty($user->last_name) && !empty($user->first_name))
	{
		$displayName = $user->first_name . ' ' . $user->last_name;
	}
	else
	{
		$displayName = $user->username;
	}

	return $displayName;
}

function IsValidFileName($fileName)
{
	if (preg_match('=^[^/?*;:{}\\\\]+\.[^/?*;:{}\\\\]+$=', $fileName))
	{
		return true;
	}

	return false;
}

function IsJsonString($text)
{
	json_decode($text);
	return (json_last_error() == JSON_ERROR_NONE);
}

function string_starts_with($haystack, $needle)
{
	return (substr($haystack, 0, strlen($needle)) === $needle);
}

function string_ends_with($haystack, $needle)
{
	$length = strlen($needle);

	if ($length == 0)
	{
		return true;
	}

	return (substr($haystack, -$length) === $needle);
}

global $GROCY_REQUIRED_FRONTEND_PACKAGES;
$GROCY_REQUIRED_FRONTEND_PACKAGES = [];
function require_frontend_packages(array $packages)
{
	global $GROCY_REQUIRED_FRONTEND_PACKAGES;

	$GROCY_REQUIRED_FRONTEND_PACKAGES = array_unique(array_merge($GROCY_REQUIRED_FRONTEND_PACKAGES, $packages));
}

function EmptyFolder($folderPath)
{
	foreach(glob("{$folderPath}/*") as $item)
	{
		if(is_dir($item))
		{
			EmptyFolder($item);
			rmdir($item);
		}
		else
		{
			unlink($item);
		}
	}
}

function FormatFraction($number) {
	$integerPart = (int) $number;
	$decimalPart = $number - $integerPart;
	$fractionPart = "";
	
	// Check if the fraction is 3rds
	$thirdsDec = round($decimalPart * 3, 3);
	$thirdsInt = (int) $thirdsDec;
	if ($thirdsDec == $thirdsInt) {
		if ($thirdsInt == 1) {
			$fractionPart = "1/3";
		} elseif ($thirdsInt == 2) {
			$fractionPart = "2/3";
		} elseif ($thirdsInt == 3) {
			$integerPart += 1;
		}
	} else {
		// Otherwise assume it's in 8ths
		
		// Multiply the decimal part by 8 and round to the nearest integer
		$numerator = round($decimalPart * 8);
		$denominator = 8;

		if ($numerator == 1) {
			$fractionPart = "1/8";
		} elseif ($numerator == 2) {
			$fractionPart = "1/4";
		} elseif ($numerator == 3) {
			$fractionPart = "3/8";
		} elseif ($numerator == 4) {
			$fractionPart = "1/2";
		} elseif ($numerator == 5) {
			$fractionPart = "5/8";
		} elseif ($numerator == 6) {
			$fractionPart = "3/4";
		} elseif ($numerator == 7) {
			$fractionPart = "7/8";
		} elseif ($numerator == 8) {
			$integerPart += 1;
		}
	}
	
	if ($integerPart == 0) {
		return "$fractionPart";
	} elseif (strlen($fractionPart) === 0){
		return "$integerPart";
	} else {
		return "$integerPart $fractionPart";
	}
}
