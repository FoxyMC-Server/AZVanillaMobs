<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\entity\overworld;

use BeeAZ\AZVanillaMobs\entity\Monster;

class Zombie extends Monster {
    public static function getNetworkTypeId(): string {
        return "minecraft:zombie";
    }

    public function isUndead(): bool {
        return true;
    }
}
