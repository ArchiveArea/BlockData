<?php

declare(strict_types=1);

namespace NhanAZ\BlockData;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\nbt\tag\CompoundTag;

class BlockDataEventHandler implements Listener {

	public function __construct(
		private BlockData $blockData
	) {
	}

	/**
	 * @param BlockBreakEvent $event
	 * @priority MONITOR
	 */
	public function onBlockBreak(BlockBreakEvent $event): void {
		if (!$event->isCancelled()) {
			$block = $event->getBlock();
			$blockData = $this->blockData->getData($block);
			if ($blockData !== null) {
				$drops = $event->getDrops();
				foreach ($drops as $drop) {
					$compoundTag = new CompoundTag();
					$compoundTag->setString("blockdata", $blockData);
					$drop->setCustomBlockData($compoundTag);
				}
				$this->blockData->removeData($block);
			}
		}
	}

	/**
	 * @param BlockPlaceEvent $event
	 * @priority MONITOR
	 */
	public function onBlockPlace(BlockPlaceEvent $event): void {
		if (!$event->isCancelled()) {
			$item = $event->getItem();
			$block = $event->getBlock();
			$customBlockData = $item->getCustomBlockData();
			if ($customBlockData !== null) {
				if ($item->getNamedTag()->getTag("blockdata") !== null) {
					$blockData = $customBlockData->getString("blockdata");
					if ($blockData !== null) {
						$this->blockData->setData($block, $blockData);
					}
				}
			}
		}
	}
}
