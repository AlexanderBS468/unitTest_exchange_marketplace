<?php
namespace Tests\Unit\Feed\Engine\Command;

use Bitrix\Main;
use Bitrix\Catalog;
use Avito\Export\Feed;
use PHPUnit\Framework\TestCase;

class PrimaryCollisionTest extends TestCase
{
	private $command;
	/** @var int */
	private $simpleCatalogId;
	/** @var int */
	private $fullCatalogId;
	private $siteId;

	protected function setUp() : void
	{
		parent::setUp();

		$this->command = new Feed\Engine\Command\PrimaryCollision(
			Feed\Engine\Steps\Offer\Table::getEntity(),
			new Feed\Logger\Logger(1)
		);
		[$this->simpleCatalogId, $this->fullCatalogId] = $this->catalogTypes();
		$this->siteId = \CSite::GetDefSite();
	}

	public function testNeedWithSimpleCatalog() : void
	{
		if ($this->simpleCatalogId === null)
		{
			trigger_error('has not simple catalog', E_USER_WARNING);
			return;
		}

		$context = new Feed\Source\Context($this->simpleCatalogId, $this->siteId);
		$expectedFalse = [
			[ 'TYPE' => Feed\Source\Registry::IBLOCK_FIELD, 'FIELD' => 'ID' ],
			[ 'TYPE' => Feed\Source\Registry::OFFER_FIELD, 'FIELD' => 'ID' ],
		];
		$expectedTrue = [
			[ 'TYPE' => Feed\Source\Registry::IBLOCK_FIELD, 'FIELD' => 'XML_ID' ],
			[ 'TYPE' => Feed\Source\Registry::IBLOCK_PROPERTY, 'FIELD' => '7' ],
		];

		foreach ($expectedFalse as $xmlLink)
		{
			$need = $this->command->need($xmlLink, $context);

			$this->assertFalse($need);
		}

		foreach ($expectedTrue as $xmlLink)
		{
			$need = $this->command->need($xmlLink, $context);

			$this->assertTrue($need);
		}
	}

	public function testNeedWithFullCatalog() : void
	{
		if ($this->fullCatalogId === null)
		{
			trigger_error('has not full catalog', E_USER_WARNING);
			return;
		}

		$context = new Feed\Source\Context($this->fullCatalogId, $this->siteId);
		$expectedFalse = [
			[ 'TYPE' => Feed\Source\Registry::OFFER_FIELD, 'FIELD' => 'ID' ],
		];
		$expectedTrue = [
			[ 'TYPE' => Feed\Source\Registry::IBLOCK_FIELD, 'FIELD' => 'ID' ],
			[ 'TYPE' => Feed\Source\Registry::IBLOCK_FIELD, 'FIELD' => 'XML_ID' ],
			[ 'TYPE' => Feed\Source\Registry::IBLOCK_PROPERTY, 'FIELD' => '7' ],
		];

		foreach ($expectedFalse as $xmlLink)
		{
			$need = $this->command->need($xmlLink, $context);

			$this->assertFalse($need);
		}

		foreach ($expectedTrue as $xmlLink)
		{
			$need = $this->command->need($xmlLink, $context);

			$this->assertTrue($need);
		}
	}

	protected function catalogTypes() : array
	{
		$simpleIblockId = null;
		$fullIblockId = null;

		if (!Main\Loader::includeModule('catalog')) { return [ $simpleIblockId, $fullIblockId ]; }

		$query = Catalog\CatalogIblockTable::getList([
			'select' => [ 'IBLOCK_ID' ],
		]);

		while ($row = $query->fetch())
		{
			$catalogInfo = \CCatalogSku::GetInfoByIBlock($row['IBLOCK_ID']);

			if ($catalogInfo['CATALOG_TYPE'] === \CCatalogSku::TYPE_FULL)
			{
				$fullIblockId = (int)$row['IBLOCK_ID'];
			}
			else if ($catalogInfo['CATALOG_TYPE'] === \CCatalogSku::TYPE_CATALOG)
			{
				$simpleIblockId = (int)$row['IBLOCK_ID'];
			}
		}

		return [ $simpleIblockId, $fullIblockId ];
	}
}