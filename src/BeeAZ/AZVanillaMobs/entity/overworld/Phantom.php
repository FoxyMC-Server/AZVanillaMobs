<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\entity\overworld;

use BeeAZ\AZVanillaMobs\entity\Monster;

class Phantom extends Monster {
    public static function getNetworkTypeId(): string {
        return "minecraft:phantom";
    }

    public function isFlying(): bool {
        return true;
    }
}
