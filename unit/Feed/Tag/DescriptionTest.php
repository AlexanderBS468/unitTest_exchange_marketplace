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
