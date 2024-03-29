<?php

declare(strict_types=1);

namespace NhanAZ\BlockData;

use NhanAZ\BlockData\utils\PositionToString;
use pocketmine\block\Block;
use pocketmine\plugin\PluginBase;
use pocketmine\Server;
use pocketmine\utils\Config;

class BlockData {

	private string $blockDataFolderPath;

	/**
	 * @method __construct(PluginBase $plugin, bool $handleEvent = true)
	 *
	 * @param PluginBase $plugin The plugin instance.
	 * @param bool $handleEvent Whether or not to handle events. If true, the item block data will be saved when the player places and the data will be written to the item when the player breaks the block. If false, vice versa.
	 */
	public function __construct(protected PluginBase $plugin, protected bool $handleEvent = false) {
		$this->blockDataFolderPath = $plugin->getDataFolder() . 'BlockData/';
		self::ensureDirectoryExists($this->blockDataFolderPath);
		if ($handleEvent) {
			Server::getInstance()->getPluginManager()->registerEvents(new BlockDataEventHandler($this), $plugin);
		}
	}

	/**
	 * Ensures that the specified directory exists.
	 *
	 * If the directory does not exist, it will be created.
	 *
	 * @param string $directory The path to the directory to ensure.
	 */
	private function ensureDirectoryExists(string $directory): void {
		if (!file_exists($directory)) {
			@mkdir($directory, 0777, true);
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
	 * Sets the data for the specified block.
	 *
	 * @param Block $block The block to set the data for.
	 * @param string $data The data to set for the block.
	 */
	public function setData(Block $block, string $data): void {
		$position = $block->getPosition();
		$positionString = PositionToString::parse($position);

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
		$positionString = PositionToString::parse($position);

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
		$positionString = PositionToString::parse($position);

		$world = $position->getWorld();
		$worldName = $world->getFolderName();
		$worldNamePath = self::getBlockDataPath() . $worldName . "/";
		$positionPath = $worldNamePath . $positionString . ".yml";
		if (!is_file($positionPath)) {
			return null;
		}
		$config = new Config($positionPath, Config::YAML);

		$return = $config->get($positionString);
		if (!is_string($return)) {
			throw new \TypeError();
		}
		return $return;
	}
}
