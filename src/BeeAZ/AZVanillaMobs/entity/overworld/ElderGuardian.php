<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\entity\overworld;

use BeeAZ\AZVanillaMobs\entity\Monster;

class ElderGuardian extends Monster {
    public static function getNetworkTypeId(): string {
        return "minecraft:elder_guardian";
    }

    public function isAquatic(): bool {
        return true;
    }

    public function getXpDropAmount(): int {
        return 10;
    }
}
