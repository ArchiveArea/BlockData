<?php

declare(strict_types=1);

namespace NhanAZ\BlockData;

use pocketmine\block\Block;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;
use pocketmine\world\Position;

class BlockData implements Listener {

	private string $blockDataFolderPath;

	public function __construct(
		private PluginBase $plugin
	) {
		$this->blockDataFolderPath = $plugin->getDataFolder() . "BlockData/";
		self::ensureDirectoryExists(self::getBlockDataPath());
		Server::getInstance()->getPluginManager()->registerEvents($this, $plugin);
	}

	/**
	 * @param BlockBreakEvent $event
	 * @priority MONITOR
	 */
	public function onBlockUpdate(BlockBreakEvent $event): void {
		if (!$event->isCancelled()) {
			self::removeData($event->getBlock());
		}
	}

	/**
	 * Ensures that the specified directory exists.
	 *
	 * If the directory does not exist, it will be created.
	 *
	 * @param string $path The path to the directory to ensure.
	 */
	private function ensureDirectoryExists(string $path): void {
		if (!is_dir($path)) {
			@mkdir($path);
		}
	}

	/**
	 * Gets the path to the block data folder.
	 *
	 * @return string The path to the block data folder.
	 */
	private function getBlockDataPath(): string {
		return $this->blockDataFolderPath;
	}

	/**
	 * Converts a `Position` object to a string.
	 *
	 * @param Position $position The position to convert.
	 *
	 * @return string The string representation of the position.
	 */
	private function positionToString(Position $position): string {
		$x = $position->getX();
		$y = $position->getY();
		$z = $position->getZ();
		return "$x $y $z";
	}

	/**
	 * Sets the data for the specified block.
	 *
	 * @param Block $block The block to set the data for.
	 * @param string $data The data to set for the block.
	 */
	public function setData(Block $block, string $data): void {
		$position = $block->getPosition();
		$positionString = self::positionToString($position);

		$world = $position->getWorld();
		$worldName = $world->getFolderName();
		$worldNamePath = self::getBlockDataPath() . $worldName . "/";
		self::ensureDirectoryExists($worldNamePath);

		$this->plugin->saveResource($worldNamePath . $positionString);

		$config = new Config($worldNamePath . $positionString . ".yml", Config::YAML);
		$config->set($positionString, $data);
		$config->save();
	}

	/**
	 * Removes the data for the specified block.
	 *
	 * @param Block $block The block to remove the data for.
	 */
	public function removeData(Block $block): void {
		$position = $block->getPosition();
		$positionString = self::positionToString($position);

		$world = $position->getWorld();
		$worldName = $world->getFolderName();
		$worldNamePath = self::getBlockDataPath() . $worldName . "/";
		$positionPath = $worldNamePath . $positionString . ".yml";
		if (is_file($positionPath)) {
			@unlink($positionPath);
		}
	}

	/**
	 * Gets the data for the specified block.
	 *
	 * @param Block $block The block to get the data for.
	 *
	 * @return string|null The data for the block, or null if the block does not have any data.
	 */
	public function getData(Block $block): ?string {
		$position = $block->getPosition();
		$positionString = self::positionToString($position);

		$world = $position->getWorld();
		$worldName = $world->getFolderName();
		$worldNamePath = self::getBlockDataPath() . $worldName . "/";
		$positionPath = $worldNamePath . $positionString . ".yml";
		if (!is_file($positionPath)) {
			return null;
		}
		$config = new Config($positionPath, Config::YAML);
		return strval($config->get($positionString));
	}
}
