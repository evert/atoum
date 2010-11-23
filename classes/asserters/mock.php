<?php

namespace mageekguy\atoum\asserters;

use \mageekguy\atoum\exceptions;

class mock extends \mageekguy\atoum\asserter
{
	protected $mock = null;

	public function setWith($mock)
	{
		$this->mock = $mock;

		if ($this->mock instanceof \mageekguy\atoum\mock\aggregator === false)
		{
			$this->fail(sprintf($this->locale->_('%s is not a mock'), $this->toString($this->mock)));
		}
		else
		{
			$this->pass();

			return $this;
		}
	}

	public function getMock()
	{
		return $this->mock;
	}

	public function wasCalled($failMessage = null)
	{
		if (sizeof($this->mockIsSet()->mock->getMockController()->getCalls()) > 0)
		{
			return $this->pass();
		}
		else
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('%s is not called'), get_class($this->mock)));
		}
	}

	public function call($method, array $args = null, $failMessage = null)
	{
		$calls = $this->mockIsSet()->mock->getMockController()->getCalls($method);

		if (sizeof($calls) <= 0)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->_('method %s::%s() is not called'), get_class($this->mock), $method));
		}
		else if ($args !== null && in_array($args, $calls) === false)
		{
			$this->fail($failMessage !== null ? $failMessage : sprintf($this->locale->__('method %s::%s() is not called with this argument', 'method %s::%s() is not called with these arguments', sizeof($args)), get_class($this->mock), $method));
		}
		else
		{
			$this->pass();
		}

		return $this;
	}

	protected function setWithArguments(array $arguments)
	{
		if (array_key_exists(0, $arguments) === false)
		{
			throw new exceptions\logic\invalidArgument('Argument must be set at index 0');
		}

		return $this->setWith($arguments[0]);
	}

	protected function mockIsSet()
	{
		if ($this->mock === null)
		{
			throw new exceptions\logic('Mock is undefined');
		}

		return $this;
	}
}

?>
