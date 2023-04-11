<?php
namespace Tests\Unit\Admin\Property;

use Avito\Export\Admin\Property\CategoryProvider;
use Avito\Export\Feed;
use Bitrix\Main\Application;
use Bitrix\Main\Text\Encoding;
use PHPUnit\Framework\TestCase;

class CategoryProviderTest extends TestCase
{
	/** @dataProvider replaceTagsDataProvider */
	public function testReplaceOldName($input, $expected) : void
	{
		if (Application::isUtfMode())
		{
			$input = Encoding::convertEncoding($input, 'windows-1251', 'utf-8');
			$expected = Encoding::convertEncoding($expected, 'windows-1251', 'utf-8');
		}

		$reflection = new \ReflectionMethod(CategoryProvider::class, 'replaceOldName');
		$reflection->setAccessible(true);

		$result = $reflection->invoke(null, explode(CategoryProvider::VALUE_GLUE, $input));

		$this->assertSame($expected, implode(CategoryProvider::VALUE_GLUE, $result));
	}

	public function replaceTagsDataProvider() : array
	{
		return [
			[
				'Для дома и дачи / Ремонт и строительство / Сантехника, водоснабжение и сауна',
				'Для дома и дачи / Ремонт и строительство / Сантехника, водоснабжение и сауна',
			],
			[
				'Для дома и дачи / Ремонт и строительство / Сантехника и сауна',
				'Для дома и дачи / Ремонт и строительство / Сантехника, водоснабжение и сауна',
			],
			[
				'Unknown',
				'Unknown',
			],
			[
				'Для дома и дачи / Unknown',
				'Для дома и дачи / Unknown',
			],
			[
				'Готовый бизнес и оборудование / Оборудование для бизнеса / Ресепшены и офисная мебель',
				'Готовый бизнес и оборудование / Оборудование для бизнеса / Ресепшены и офисная мебель',
			],
			[
				'Для бизнеса / Оборудование для бизнеса / Для офиса',
				'Готовый бизнес и оборудование / Оборудование для бизнеса / Ресепшены и офисная мебель',
			],
		];
	}
}