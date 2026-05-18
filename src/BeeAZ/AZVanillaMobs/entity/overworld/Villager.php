<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\entity\overworld;

use BeeAZ\AZVanillaMobs\entity\Animal;
use pocketmine\item\VanillaItems;

class Villager extends Animal {
    public static function getNetworkTypeId(): string {
        return "minecraft:villager_v2";
    }

    protected ?array $tradeRecipes = null;

    public function getTradeRecipes(): array {
        if ($this->tradeRecipes !== null) {
            return $this->tradeRecipes;
        }

        $possibleTrades = [
            [
                'buyA' => VanillaItems::EMERALD()->setCount(5),
                'buyB' => VanillaItems::WHEAT()->setCount(32),
                'sell' => VanillaItems::BREAD()->setCount(64)
            ],
            [
                'buyA' => VanillaItems::EMERALD()->setCount(10),
                'buyB' => VanillaItems::CARROT()->setCount(32),
                'sell' => VanillaItems::GOLDEN_CARROT()->setCount(16)
            ],
            [
                'buyA' => VanillaItems::EMERALD()->setCount(15),
                'buyB' => VanillaItems::IRON_INGOT()->setCount(10),
                'sell' => VanillaItems::IRON_SWORD()->setCount(1)
            ],
            [
                'buyA' => VanillaItems::EMERALD()->setCount(24),
                'buyB' => VanillaItems::DIAMOND()->setCount(3),
                'sell' => VanillaItems::DIAMOND_PICKAXE()->setCount(1)
            ],
            [
                'buyA' => VanillaItems::EMERALD()->setCount(8),
                'buyB' => VanillaItems::BOOK()->setCount(1),
                'sell' => VanillaItems::ENCHANTED_BOOK()->setCount(1)
            ],
            [
                'buyA' => VanillaItems::EMERALD()->setCount(12),
                'buyB' => VanillaItems::MELON()->setCount(64),
                'sell' => VanillaItems::GLISTERING_MELON()->setCount(8)
            ],
            [
                'buyA' => VanillaItems::EMERALD()->setCount(6),
                'buyB' => VanillaItems::POTATO()->setCount(32),
                'sell' => VanillaItems::BAKED_POTATO()->setCount(64)
            ],
            [
                'buyA' => VanillaItems::EMERALD()->setCount(18),
                'buyB' => VanillaItems::GOLD_INGOT()->setCount(8),
                'sell' => VanillaItems::GOLDEN_APPLE()->setCount(4)
            ]
        ];

        shuffle($possibleTrades);
        $numTrades = mt_rand(5, 7);
        $selectedTrades = array_slice($possibleTrades, 0, $numTrades);

        $this->tradeRecipes = [];
        foreach ($selectedTrades as $trade) {
            $this->tradeRecipes[] = [
                'buyA' => $trade['buyA'],
                'buyB' => $trade['buyB'],
                'sell' => $trade['sell'],
                'maxUses' => 9999,
                'tier' => 1
            ];
        }

        return $this->tradeRecipes;
    }
}
