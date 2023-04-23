<?php

declare(strict_types=1);

namespace NhanAZ\BlockData;

use pocketmine\world\Position;

class PositionToString {

	/**
	 * Converts a `Position` object to a string.
	 *
	 * @param Position $position The position to convert.
	 *
	 * @return string The string representation of the position.
	 */
	public static function parse(Position $position): string {
		$x = $position->getX();
		$y = $position->getY();
		$z = $position->getZ();
		return "$x $y $z";
	}
}
