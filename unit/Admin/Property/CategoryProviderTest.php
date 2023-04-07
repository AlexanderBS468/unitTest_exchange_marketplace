<?php
namespace Tests\Unit\Admin\Property;

use Avito\Export\Admin\Property\CategoryProvider;
use Avito\Export\Feed;
use PHPUnit\Framework\TestCase;

class CategoryProviderTest extends TestCase
{
	/** @dataProvider replaceTagsDataProvider */
	public function testReplaceOldName($input, $expected) : void
	{
		$reflection = new \ReflectionMethod(CategoryProvider::class, 'replaceOldName');
		$reflection->setAccessible(true);

		$result = $reflection->invoke(null, explode(CategoryProvider::VALUE_GLUE, $input));

		$this->assertSame($expected, implode(CategoryProvider::VALUE_GLUE, $result));
	}

	public function replaceTagsDataProvider() : array
	{
		return [
			[
				'��� ���� � ���� / ������ � ������������� / ����������, ������������� � �����',
				'��� ���� � ���� / ������ � ������������� / ����������, ������������� � �����',
			],
			[
				'��� ���� � ���� / ������ � ������������� / ���������� � �����',
				'��� ���� � ���� / ������ � ������������� / ����������, ������������� � �����',
			],
			[
				'Unknown',
				'Unknown',
			],
			[
				'��� ���� � ���� / Unknown',
				'��� ���� � ���� / Unknown',
			],
			[
				'������� ������ � ������������ / ������������ ��� ������� / ��������� � ������� ������',
				'������� ������ � ������������ / ������������ ��� ������� / ��������� � ������� ������',
			],
			[
				'��� ������� / ������������ ��� ������� / ��� �����',
				'������� ������ � ������������ / ������������ ��� ������� / ��������� � ������� ������',
			],
		];
	}
}