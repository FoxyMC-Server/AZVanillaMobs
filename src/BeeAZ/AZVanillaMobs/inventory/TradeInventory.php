<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\inventory;

use pocketmine\inventory\SimpleInventory;

class TradeInventory extends SimpleInventory {

    public function __construct() {
        parent::__construct(5); // Slot 0: Input A, Slot 1: Input B, Slot 2: Output, Slot 3 & 4: Dummy consume slots
    }
}
