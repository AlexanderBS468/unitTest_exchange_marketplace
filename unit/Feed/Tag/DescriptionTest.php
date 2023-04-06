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
				'{=ELEMENT.NAME}<br> <br> <i><b>Все товары новые, оригинальные с заводской гарантией производителя.</b></i><br> Возможность проверить товары на месте.<br> <br> По всем интересующих вопросам звоните, пишите.<br> <br> <i>График работы:</i><br> <i>пн-пт&nbsp; 09:00-18.00</i><br> <br> Продаем всю сантехнику мировых Брендов (Grohe, Kludi, Geberit, Hansgrohe, Grohe, Aquanet, Keuco, Omoikiri, Aqwella) и др.<br> _____________________________________________________________<br> <br> Самовывоз: Нагорный проезд 12БС5.<br> Доставка: 300 руб в пределах МКАД, за МКАД +50 руб за км.<br> Выдача осуществляется по предварительному заказу, возможно забрать в день заказа<br> ______________________________________________________________<br> <br> {=ELEMENT.DETAIL_TEXT}<br>',
				'{=ELEMENT.NAME}<br> <br> <em><strong>Все товары новые, оригинальные с заводской гарантией производителя.</strong></em><br> Возможность проверить товары на месте.<br> <br> По всем интересующих вопросам звоните, пишите.<br> <br> <em>График работы:</em><br> <em>пн-пт&nbsp; 09:00-18.00</em><br> <br> Продаем всю сантехнику мировых Брендов (Grohe, Kludi, Geberit, Hansgrohe, Grohe, Aquanet, Keuco, Omoikiri, Aqwella) и др.<br> _____________________________________________________________<br> <br> Самовывоз: Нагорный проезд 12БС5.<br> Доставка: 300 руб в пределах МКАД, за МКАД +50 руб за км.<br> Выдача осуществляется по предварительному заказу, возможно забрать в день заказа<br> ______________________________________________________________<br> <br> {=ELEMENT.DETAIL_TEXT}<br>',
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
