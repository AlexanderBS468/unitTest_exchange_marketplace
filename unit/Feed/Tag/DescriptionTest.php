<?php
namespace Tests\Unit\Feed\Tag\Description;

use Bitrix\Catalog;
use Avito\Export\Feed;
use PHPUnit\Framework\TestCase;

class DescriptionTest extends TestCase
{
	/** @dataProvider replaceTagsDataProvider */
	public function testReplaceTags($input, $expected) : void
	{
		$tag = new Feed\Tag\Description();
		$reflection = new \ReflectionMethod($tag, 'formatReplaceTags');
		$reflection->setAccessible(true);

		$this->assertSame($expected, $reflection->invoke($tag, $input));
	}

	public function replaceTagsDataProvider() : array
	{
		return [
			[
				1,
				1,
			],
			[
				'{=ELEMENT.NAME}<br> <br> <i><b>��� ������ �����, ������������ � ��������� ��������� �������������.</b></i><br> ����������� ��������� ������ �� �����.<br> <br> �� ���� ������������ �������� �������, ������.<br> <br> <i>������ ������:</i><br> <i>��-��&nbsp; 09:00-18.00</i><br> <br> ������� ��� ���������� ������� ������� (Grohe, Kludi, Geberit, Hansgrohe, Grohe, Aquanet, Keuco, Omoikiri, Aqwella) � ��.<br> _____________________________________________________________<br> <br> ���������: �������� ������ 12��5.<br> ��������: 300 ��� � �������� ����, �� ���� +50 ��� �� ��.<br> ������ �������������� �� ���������������� ������, �������� ������� � ���� ������<br> ______________________________________________________________<br> <br> {=ELEMENT.DETAIL_TEXT}<br>',
				'{=ELEMENT.NAME}<br> <br> <em><strong>��� ������ �����, ������������ � ��������� ��������� �������������.</strong></em><br> ����������� ��������� ������ �� �����.<br> <br> �� ���� ������������ �������� �������, ������.<br> <br> <em>������ ������:</em><br> <em>��-��&nbsp; 09:00-18.00</em><br> <br> ������� ��� ���������� ������� ������� (Grohe, Kludi, Geberit, Hansgrohe, Grohe, Aquanet, Keuco, Omoikiri, Aqwella) � ��.<br> _____________________________________________________________<br> <br> ���������: �������� ������ 12��5.<br> ��������: 300 ��� � �������� ����, �� ���� +50 ��� �� ��.<br> ������ �������������� �� ���������������� ������, �������� ������� � ���� ������<br> ______________________________________________________________<br> <br> {=ELEMENT.DETAIL_TEXT}<br>',
			],
			[
				'<p>Dummy</p>',
				'<p>Dummy</p>',
			],
			[
				'<h1>Dummy</h1>',
				'<p><strong>Dummy</strong></p>',
			],
			[
				'<h2 class="dummy">Dummy</h2>',
				'<p class="dummy"><strong>Dummy</strong></p>',
			],
			[
				'<h3 class="dummy">Dummy</h3>',
				'<p class="dummy"><strong>Dummy</strong></p>',
			],
			[
				'<h4 class="dummy">Dummy</h4>',
				'<p class="dummy"><strong>Dummy</strong></p>',
			],
			[
				'<h5 class="dummy">Dummy</h5>',
				'<p class="dummy"><strong>Dummy</strong></p>',
			],
			[
				'<h6 class="dummy">Dummy</h6>',
				'<p class="dummy"><strong>Dummy</strong></p>',
			],
			[
				'<b>Dummy</b>',
				'<strong>Dummy</strong>',
			],
			[
				'<b class="dummy">Dummy</b>',
				'<strong class="dummy">Dummy</strong>',
			],
			[
				'<i>Dummy</i>',
				'<em>Dummy</em>',
			],
			[
				'<i class="dummy">Dummy</i>',
				'<em class="dummy">Dummy</em>',
			],
		];
	}
}
