<?php
namespace Tests\Unit\Source\Template\Engine\Functions;

use Bitrix\Main;
use Avito\Export\Feed\Source\Template\Engine\Functions\FunctionWeekday;
use PHPUnit\Framework\TestCase;

class FunctionWeekdayTest extends TestCase
{
	private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';

	/** @dataProvider dateBeginDataProvider */
	public function testDateBegin(array $parameters, string $today, string $expected) : void
	{
		$this->runFunction($parameters, $today, $expected);
	}

	/** @dataProvider dateEndDataProvider */
	public function testDateEnd(array $parameters, string $today, string $expected) : void
	{
		$this->runFunction($parameters, $today, $expected);
	}

	private function runFunction(array $parameters, string $today, string $expected) : void
	{
		$function = new FunctionWeekday();
		$function->setToday(new Main\Type\DateTime($today, self::DATE_TIME_FORMAT));
		$date = $function->calculate($parameters);

		$this->assertInstanceOf(Main\Type\DateTime::class, $date);
		$this->assertSame($expected, $date->format(self::DATE_TIME_FORMAT));
	}

	public function dateBeginDataProvider() : array
	{
		return [
			[
				[0, "23:00", 5, "18:00"],
				'2023-05-15 00:00:00',
				'2023-05-14 23:00:00'
			],
			[
				[0, "23:00", 5, "18:00"],
				'2023-05-16 00:00:00',
				'2023-05-14 23:00:00'
			],
			[
				[0, "23:00", 5, "18:00"],
				'2023-05-17 00:00:00',
				'2023-05-14 23:00:00'
			],
			[
				[0, "23:00", 5, "18:00"],
				'2023-05-18 00:00:00',
				'2023-05-14 23:00:00'
			],
			[
				[0, "23:00", 5, "18:00"],
				'2023-05-19 00:00:00',
				'2023-05-14 23:00:00'
			],
			[
				[0, "23:00", 5, "18:00"],
				'2023-05-19 18:00:00',
				'2023-05-21 23:00:00'
			],
			[
				[0, "23:00", 5, "18:00"],
				'2023-05-19 19:00:00',
				'2023-05-21 23:00:00'
			],
			[
				[0, "23:00", 5, "18:00"],
				'2023-05-20 00:00:00',
				'2023-05-21 23:00:00'
			],
			[
				[6, "23:00", 5, "18:00"],
				'2023-05-18 00:00:00',
				'2023-05-13 23:00:00'
			],
			[
				[6, "23:00", 5, "18:00"],
				'2023-05-19 18:00:01',
				'2023-05-20 23:00:00'
			],
			[
				[2, "23:00", 5, "18:00"],
				'2023-05-18 00:00:00',
				'2023-05-16 23:00:00'
			],
			[
				[0, "23:00", 5, "18:00"],
				'2023-05-26 18:00:00',
				'2023-05-28 23:00:00'
			],
		];
	}

	public function dateEndDataProvider() : array
	{
		return [
			[
				[5, "18:00"],
				'2023-05-18 00:00:00',
				'2023-05-19 18:00:00'
			],
			[
				[5, "18:00"],
				'2023-05-19 18:00:00',
				'2023-05-26 18:00:00'
			],
			[
				[5, "18:00"],
				'2023-05-20 00:00:00',
				'2023-05-26 18:00:00'
			],
			[
				[5, "17:00"],
				'2023-05-25 16:00:00',
				'2023-05-26 17:00:00'
			],
			[
				[5, "17:00"],
				'2023-05-26 16:00:00',
				'2023-05-26 17:00:00'
			],
			[
				[5, "17:00"],
				'2023-05-26 18:00:00',
				'2023-06-02 17:00:00'
			],
		];
	}
}