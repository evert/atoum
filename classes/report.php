<?php

namespace mageekguy\atoum;

class report implements observers\runner, observers\test
{
	protected $runnerFields = array();
	protected $testFields = array();
	protected $decorators = array();

	public function __construct()
	{
		$this->runnerFields = array(
			runner::runStart => array(),
			runner::runStop => array()
		);

		$this->testFields = array(
			test::runStart => array(),
			test::beforeSetUp => array(),
			test::afterSetUp => array(),
			test::beforeTestMethod => array(),
			test::success => array(),
			test::fail => array(),
			test::error => array(),
			test::exception => array(),
			test::afterTestMethod => array(),
			test::beforeTearDown => array(),
			test::afterTearDown => array(),
			test::runStop => array(),
		);
	}

	public function addRunnerField(report\fields\runner $field, array $events = array())
	{
		return $this->addField($field, $events, 'runnerFields');
	}

	public function addTestField(report\fields\test $field, array $events = array())
	{
		return $this->addField($field, $events, 'testFields');
	}

	public function addDecorator(report\decorator $decorator)
	{
		$this->decorators[] = $decorator;

		return $this;
	}

	public function getRunnerFields($event = null)
	{
		$fields = array();

		if ($event === null)
		{
			$fields = $this->runnerFields;
		}
		else
		{
			if (in_array($event, runner::getObserverEvents()) === false)
			{
				throw new exceptions\logic\invalidArgument('\'' . $event . '\' is not a runner event');
			}

			$fields = $this->runnerFields[$event];
		}

		return $fields;
	}

	public function getTestFields($event = null)
	{
		$fields = array();

		if ($event === null)
		{
			$fields = $this->testFields;
		}
		else
		{
			if (in_array($event, test::getObserverEvents()) === false)
			{
				throw new exceptions\logic\invalidArgument('\'' . $event . '\' is not a test event');
			}

			$fields = $this->testFields[$event];
		}

		return $fields;
	}

	public function getDecorators()
	{
		return $this->decorators;
	}

	public function runnerStart(runner $runner)
	{
		return $this->decorateRunner($runner, __FUNCTION__, 'write');
	}

	public function testRunStart(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function beforeTestSetup(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function afterTestSetup(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function beforeTestMethod(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function testAssertionSuccess(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function testAssertionFail(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function testError(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function testException(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function afterTestMethod(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function testRunStop(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function beforeTestTearDown(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function afterTestTearDown(test $test)
	{
		return $this->decorateTest($test, __FUNCTION__);
	}

	public function runnerStop(runner $runner)
	{
		return $this->decorateRunner($runner, __FUNCTION__, 'flush');
	}

	protected function decorateRunner(runner $runner, $event, $writeMethod)
	{
		return $this->decorate($runner, $event, 'runnerFields', 'setWithRunner', $writeMethod);
	}

	protected function decorateTest(test $test, $event)
	{
		return $this->decorate($test, $event, 'testFields', 'setWithTest', 'write');
	}

	protected function decorate($entity, $event, $propertyName, $setMethod, $writeMethod)
	{
		if (isset($this->{$propertyName}[$event]) === true)
		{
			foreach ($this->{$propertyName}[$event] as $field)
			{
				$field->{$setMethod}($entity, $event);

				foreach ($this->decorators as $decorator)
				{
					$decorator->{$writeMethod}($field);
				}
			}
		}

		return $this;
	}

	protected function addField(report\field $field, array $events, $propertyName)
	{
		if (sizeof($events) <= 0)
		{
			foreach ($this->{$propertyName} as & $fields)
			{
				$fields[] = $field;
			}
		}
		else
		{
			foreach ($events as $event)
			{
				if (isset($this->{$propertyName}[$event]) === false)
				{
					throw new exceptions\runtime('Event \'' . $event . '\' does not exist');
				}

				$this->{$propertyName}[$event][] = $field;
			}
		}

		return $this;
	}
}

?>
