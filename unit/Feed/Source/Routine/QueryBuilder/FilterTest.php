<?php
namespace Tests\Unit\Feed\Source\Routine\QueryBuilder;

use Bitrix\Catalog;
use Avito\Export\Feed;
use PHPUnit\Framework\TestCase;

class FilterTest extends TestCase
{
	protected $filter;
	protected $contextWithOffers;

	/** @noinspection PhpUndefinedConstantInspection */
	protected function setUp() : void
	{
		parent::setUp();

		$iblockId = defined('CATALOG_IBLOCK_ID') ? CATALOG_IBLOCK_ID : 2;

		$this->filter = new Feed\Source\Routine\QueryBuilder\Filter(
			new Feed\Source\FetcherPool()
		);
		$this->contextWithOffers = new Feed\Source\Context($iblockId);
	}

	public function testElement() : void
	{
		$filterMap = new Feed\Setup\FilterMap([
			[
				'FIELD' => 'ELEMENT.NAME',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 'dummy',
			],
		]);
		$filters = $this->filter->compile($filterMap, $this->contextWithOffers);

		$this->assertCount(1, $filters);

		$this->assertSame(
			array_merge(
				$this->defaultFilter($this->contextWithOffers->iblockId()),
				[
					'=NAME' => 'dummy',
				]
			),
			$filters[0]['ELEMENT']
		);
	}

	public function testCatalog() : void
	{
		$filterMap = new Feed\Setup\FilterMap([
			[
				'FIELD' => 'PRODUCT.QUANTITY',
				'COMPARE' => Feed\Source\Field\Condition::MORE_OR_EQUAL,
				'VALUE' => 0,
			],
		]);
		$filters = $this->filter->compile($filterMap, $this->contextWithOffers);

		$this->assertCount(2, $filters);

		// element

		$this->assertSame(
			array_merge(
				$this->defaultFilter($this->contextWithOffers->iblockId()),
				[
					'>=QUANTITY' => 0,
					'!=TYPE' => Catalog\ProductTable::TYPE_SKU,
				]
			),
			$filters[0]['ELEMENT']
		);

		// offer

		$offerFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->offerIblockId()),
			[ '>=QUANTITY' => 0 ]
		);
		$elementFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->iblockId()),
			[
				[ '=TYPE' => Catalog\ProductTable::TYPE_SKU ],
			],
			[
				[ 'ID' => $offerFilter ],
			]
		);

		$this->assertSame($elementFilter, $this->filterToArray($filters[1]['ELEMENT']));
		$this->assertSame($offerFilter, $filters[1]['OFFER']);
	}

	public function testProductAvailableYes() : void
	{
		$filterMap = new Feed\Setup\FilterMap([
			[
				'FIELD' => 'PRODUCT.AVAILABLE',
				'COMPARE' => Feed\Source\Field\Condition::AT_LIST,
				'VALUE' => 'Y',
			],
		]);
		$filters = $this->filter->compile($filterMap, $this->contextWithOffers);

		$this->assertCount(1, $filters);
		$this->assertSame(
			$this->defaultFilter($this->contextWithOffers->iblockId()),
			$filters[0]['ELEMENT']
		);
		$this->assertSame(
			$this->defaultFilter($this->contextWithOffers->offerIblockId()),
			$filters[0]['OFFER']
		);
	}

	public function testProductAvailableAny() : void
	{
		$filterMap = new Feed\Setup\FilterMap([
			[
				'FIELD' => 'PRODUCT.AVAILABLE',
				'COMPARE' => Feed\Source\Field\Condition::AT_LIST,
				'VALUE' => 'ANY',
			],
		]);
		$filters = $this->filter->compile($filterMap, $this->contextWithOffers);

		$this->assertCount(1, $filters);
		$this->assertSame(
			array_diff_key($this->defaultFilter($this->contextWithOffers->iblockId()), [
				'=AVAILABLE' => true,
			]),
			$filters[0]['ELEMENT']
		);
	}

	public function testOfferRequired() : void
	{
		$filterMap = new Feed\Setup\FilterMap([
			[
				'FIELD' => 'ELEMENT.NAME',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 'dummy',
			],
			[
				'FIELD' => 'OFFER.NAME',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 'dummy',
			],
		]);
		$filters = $this->filter->compile($filterMap, $this->contextWithOffers);

		$this->assertCount(1, $filters);

		$offerFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->offerIblockId()),
			[ '=NAME' => 'dummy' ]
		);
		$elementFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->iblockId()),
			[ '=NAME' => 'dummy' ],
			[
				[ 'ID' => $offerFilter ],
			]
		);

		$this->assertSame($elementFilter, $this->filterToArray($filters[0]['ELEMENT']));
		$this->assertSame($offerFilter, $filters[0]['OFFER']);
	}

	public function testLogicElementOr() : void
	{
		$filterMap = new Feed\Setup\FilterMap([
			[
				'FIELD' => 'ELEMENT.ID',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 100,
				'GLUE' => 'AND',
			],
			[
				'FIELD' => 'ELEMENT.NAME',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 'dummy',
				'GLUE' => 'AND',
			],
			[
				'FIELD' => 'ELEMENT.XML_ID',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 'dummy',
				'GLUE' => 'OR',
			],
		]);
		$filters = $this->filter->compile($filterMap, $this->contextWithOffers);

		$this->assertCount(1, $filters);

		// offer

		$offerFilter = $this->defaultFilter($this->contextWithOffers->offerIblockId());
		$elementFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->iblockId()),
			[ '=ID' => 100 ],
			[
				[
					[
						'LOGIC' => 'OR',
						'=NAME' => 'dummy',
						'=XML_ID' => 'dummy',
					],
				],
			]
		);

		$this->assertSame($elementFilter, $filters[0]['ELEMENT']);
		$this->assertSame($offerFilter, $filters[0]['OFFER']);
	}

	public function testLogicOfferOr() : void
	{
		$filterMap = new Feed\Setup\FilterMap([
			[
				'FIELD' => 'ELEMENT.ID',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 100,
				'GLUE' => 'AND',
			],
			[
				'FIELD' => 'OFFER.NAME',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 'dummy',
				'GLUE' => 'AND',
			],
			[
				'FIELD' => 'OFFER.XML_ID',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 'dummy',
				'GLUE' => 'OR',
			],
		]);
		$filters = $this->filter->compile($filterMap, $this->contextWithOffers);

		$this->assertCount(1, $filters);

		// offer

		$offerFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->offerIblockId()),
			[
				[
					[
						'LOGIC' => 'OR',
						'=NAME' => 'dummy',
						'=XML_ID' => 'dummy',
					],
				],
			]
		);
		$elementFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->iblockId()),
			[ '=ID' => 100 ],
			[
				[ 'ID' => $offerFilter ],
			]
		);

		$this->assertSame($elementFilter, $this->filterToArray($filters[0]['ELEMENT']));
		$this->assertSame($offerFilter, $filters[0]['OFFER']);
	}

	public function testLogicCatalogOr() : void
	{
		$filterMap = new Feed\Setup\FilterMap([
			[
				'FIELD' => 'ELEMENT.ID',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 100,
				'GLUE' => 'AND',
			],
			[
				'FIELD' => 'PRODUCT.QUANTITY',
				'COMPARE' => Feed\Source\Field\Condition::MORE_OR_EQUAL,
				'VALUE' => 1,
				'GLUE' => 'AND',
			],
			[
				'FIELD' => 'STORE.STORE_AMOUNT_1',
				'COMPARE' => Feed\Source\Field\Condition::MORE_OR_EQUAL,
				'VALUE' => 1,
				'GLUE' => 'OR',
			],
		]);
		$filters = $this->filter->compile($filterMap, $this->contextWithOffers);

		$this->assertCount(2, $filters);

		// element

		$this->assertSame(
			array_merge(
				$this->defaultFilter($this->contextWithOffers->iblockId()),
				[
					'=ID' => 100,
					'!=TYPE' => Catalog\ProductTable::TYPE_SKU,
				],
				[
					[
						[
							'LOGIC' => 'OR',
							'>=QUANTITY' => 1,
							'>=STORE_AMOUNT_1' => 1,
						]
					]
				]
			),
			$filters[0]['ELEMENT']
		);

		// offer

		$offerFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->offerIblockId()),
			[
				[
					[
						'LOGIC' => 'OR',
						'>=QUANTITY' => 1,
						'>=STORE_AMOUNT_1' => 1,
					]
				]
			]
		);
		$elementFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->iblockId()),
			[
				'=ID' => 100,
				[ '=TYPE' => Catalog\ProductTable::TYPE_SKU ],
			],
			[
				[ 'ID' => $offerFilter ],
			]
		);

		$this->assertSame($elementFilter, $this->filterToArray($filters[1]['ELEMENT']));
		$this->assertSame($offerFilter, $filters[1]['OFFER']);
	}

	public function testLogicElementOrOffer() : void
	{
		$filterMap = new Feed\Setup\FilterMap([
			[
				'FIELD' => 'ELEMENT.ID',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 100,
				'GLUE' => 'AND',
			],
			[
				'FIELD' => 'ELEMENT.NAME',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 'dummy',
				'GLUE' => 'AND',
			],
			[
				'FIELD' => 'OFFER.NAME',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 'dummy',
				'GLUE' => 'OR',
			],
		]);
		$filters = $this->filter->compile($filterMap, $this->contextWithOffers);

		$this->assertCount(2, $filters);

		// element

		$this->assertSame(
			array_merge(
				$this->defaultFilter($this->contextWithOffers->iblockId()),
				[ '=ID' => 100 ],
				[
					[
						[ '=NAME' => 'dummy' ],
					],
				]
			),
			$filters[0]['ELEMENT']
		);

		// offer

		$offerFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->offerIblockId()),
			[
				[
					[ '=NAME' => 'dummy' ],
				],
			]
		);
		$elementFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->iblockId()),
			[ '=ID' => 100 ],
			[
				[ 'ID' => $offerFilter ],
			]
		);

		$this->assertSame($elementFilter, $this->filterToArray($filters[1]['ELEMENT']));
		$this->assertSame($offerFilter, $filters[1]['OFFER']);
	}

	public function testLogicElementAndOfferWithOr() : void
	{
		$filterMap = new Feed\Setup\FilterMap([
			[
				'FIELD' => 'ELEMENT.ID',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 100,
				'GLUE' => 'AND',
			],
			[
				'FIELD' => 'ELEMENT.NAME',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 'dummy',
				'GLUE' => 'OR',
			],
			[
				'FIELD' => 'OFFER.ID',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 100,
				'GLUE' => 'AND',
			],
			[
				'FIELD' => 'OFFER.NAME',
				'COMPARE' => Feed\Source\Field\Condition::EQUAL,
				'VALUE' => 'dummy',
				'GLUE' => 'OR',
			],
		]);
		$filters = $this->filter->compile($filterMap, $this->contextWithOffers);

		$this->assertCount(1, $filters);

		$offerFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->offerIblockId()),
			[
				[
					[
						'LOGIC' => 'OR',
						'=ID' => 100,
						'=NAME' => 'dummy'
					],
				],
			]
		);
		$elementFilter = array_merge(
			$this->defaultFilter($this->contextWithOffers->iblockId()),
			[
				[
					[
						'LOGIC' => 'OR',
						'=ID' => 100,
						'=NAME' => 'dummy'
					],
				],
			],
			[
				[ 'ID' => $offerFilter ],
			]
		);

		$this->assertSame($elementFilter, $this->filterToArray($filters[0]['ELEMENT']));
		$this->assertSame($offerFilter, $filters[0]['OFFER']);
	}

	private function defaultFilter(int $iblockId) : array
	{
		return [
			'IBLOCK_ID' => $iblockId,
			'=ACTIVE' => 'Y',
			'=ACTIVE_DATE' => 'Y',
			'=AVAILABLE' => 'Y',
		];
	}

	private function filterToArray(array $filter) : array
	{
		foreach ($filter as $key => $value)
		{
			if ($value instanceof \CIBlockElement)
			{
				$filter[$key] = $value->arFilter;
			}
			else if (is_array($value) && is_numeric($key))
			{
				$filter[$key] = $this->filterToArray($value);
			}
		}

		return $filter;
	}
}