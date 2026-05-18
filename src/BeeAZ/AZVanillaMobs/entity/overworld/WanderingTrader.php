<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\entity\overworld;

use BeeAZ\AZVanillaMobs\entity\Animal;

class WanderingTrader extends Animal {
    public static function getNetworkTypeId(): string {
        return "minecraft:wandering_trader";
    }
}
