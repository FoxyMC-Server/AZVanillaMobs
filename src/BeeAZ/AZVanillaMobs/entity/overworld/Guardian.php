<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\entity\overworld;

use BeeAZ\AZVanillaMobs\entity\Monster;

class Guardian extends Monster {
    public static function getNetworkTypeId(): string {
        return "minecraft:guardian";
    }

    public function isAquatic(): bool {
        return true;
    }

    public function getXpDropAmount(): int {
        return 10;
    }
}
