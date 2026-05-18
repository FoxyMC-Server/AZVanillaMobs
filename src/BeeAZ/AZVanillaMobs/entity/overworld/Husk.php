<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\entity\overworld;

use BeeAZ\AZVanillaMobs\entity\Monster;

class Husk extends Monster {
    public static function getNetworkTypeId(): string {
        return "minecraft:husk";
    }
}
