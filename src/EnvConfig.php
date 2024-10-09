<?php

namespace q4ev\simpleEnvConfig;


use yii\base\InvalidCallException;
use yii\base\InvalidConfigException;
use yii\base\UnknownPropertyException;

class EnvConfig
{
	protected static ?self $_instance = null;

	public array $envData = [];

	public function __construct ()
	{
		if (!file_exists(ROOT_DIR . '/.env'))
			throw new InvalidConfigException('.env not exists');

		$data = \parse_ini_file(ROOT_DIR . '/.env', false, INI_SCANNER_TYPED);

		if (!is_array($data))
			throw new InvalidConfigException('.env cannot be parsed');

		if (!$this->envData = $data)
			throw new InvalidConfigException('envData must be set');
	}

	public function __get ($name)
	{
		$getter = 'get' . $name;
		if (method_exists($this, $getter))
			return $this->$getter();
		elseif (array_key_exists($name, $this->envData))
			return $this->envData[$name];
		elseif (method_exists($this, 'set' . $name))
			throw new InvalidCallException('Getting write-only property: ' . get_class($this) . '::' . $name);

		throw new UnknownPropertyException('Getting unknown property: ' . get_class($this) . '::' . $name);
	}

	public function __isset ($name)
	{
		$getter = 'get' . $name;
		if (method_exists($this, $getter))
			return null !== $this->$getter();

		return array_key_exists($name, $this->envData);
	}

	public function __set ($name, $value)
	{
		$setter = 'set' . $name;
		if (method_exists($this, $setter))
			$this->$setter($value);
		elseif (array_key_exists($name, $this->envData))
			$this->envData[$name] = $value;
		elseif (method_exists($this, 'get' . $name))
			throw new InvalidCallException('Setting read-only property: ' . get_class($this) . '::' . $name);

		throw new UnknownPropertyException('Setting unknown property: ' . get_class($this) . '::' . $name);
	}

	public function __unset ($name)
	{
		$setter = 'set' . $name;
		if (method_exists($this, $setter))
			$this->$setter(null);
		elseif (array_key_exists($name, $this->envData))
			unset($this->envData[$name]);
		elseif (method_exists($this, 'get' . $name))
			throw new InvalidCallException('Unsetting read-only property: ' . get_class($this) . '::' . $name);
	}

	public static function get ($key, $default = null)
	{
		if (null === static::$_instance)
			static::$_instance = new static;

		/** @see static::__get() */
		return static::$_instance->$key ?? $default;
	}
}