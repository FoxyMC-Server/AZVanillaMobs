<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\spawner;

use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\world\Position;
use pocketmine\world\World;
use pocketmine\entity\Location;

class SpawnerTask extends Task {

    private int $tickCounter = 0;
    private \BeeAZ\AZVanillaMobs\Main $plugin;

    public function __construct(\BeeAZ\AZVanillaMobs\Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onRun(): void {
        $this->tickCounter++;

        if ($this->tickCounter % 5 !== 0) return;

        $config = $this->plugin->getConfig();
        $worldsConfig = $config->get("worlds", ["world" => "overworld"]);
        $globalCap = (int) $config->get("global-mob-cap", 200);
        $perWorldCap = (int) $config->get("per-world-mob-cap", 70);

        $totalMobs = 0;
        foreach (Server::getInstance()->getWorldManager()->getWorlds() as $world) {
            $totalMobs += count($world->getEntities());
        }

        if ($totalMobs >= $globalCap) return;

        foreach (Server::getInstance()->getWorldManager()->getWorlds() as $world) {
            $folderName = $world->getFolderName();
            if (!isset($worldsConfig[$folderName])) continue;

            $dimensionType = $worldsConfig[$folderName];

            $players = $world->getPlayers();
            if (empty($players)) continue;

            if (count($world->getEntities()) >= $perWorldCap) continue;

            foreach ($players as $player) {

                $this->attemptSpawn($player->getPosition(), $dimensionType);
            }
        }
    }

    private function attemptSpawn(Position $pos, string $dimensionType): void {
        $world = $pos->getWorld();
        $x = $pos->getFloorX() + mt_rand(-24, 24);
        $z = $pos->getFloorZ() + mt_rand(-24, 24);

        if (abs($x - $pos->getFloorX()) < 10 && abs($z - $pos->getFloorZ()) < 10) {
            return;
        }

        $y = $world->getHighestBlockAt($x, $z);
        if ($y === null || $y < 0) return;

        $block = $world->getBlockAt($x, $y, $z);
        $isWater = $block instanceof \pocketmine\block\Water;

        $light = $world->getBlockLightAt($x, $y + 1, $z);
        $time = $world->getTimeOfDay();
        $isNight = $time >= World::TIME_NIGHT && $time < World::TIME_SUNRISE;

        $list = [];
        $category = '';
        if ($dimensionType === "nether") {
            $list = $this->plugin->spawnerLists['nether'] ?? [];
            $category = 'nether';
        } elseif ($dimensionType === "the_end") {
            $list = $this->plugin->spawnerLists['the_end'] ?? [];
            $category = 'the_end';
        } else {

            if ($isWater) {
                if ($isNight && $light <= 7) {
                    $list = $this->plugin->spawnerLists['overworld_hostile'] ?? [];
                    $category = 'overworld_hostile';
                } else {
                    $list = $this->plugin->spawnerLists['overworld_passive'] ?? [];
                    $category = 'overworld_passive';
                }
            } else {
                if ($isNight && $light <= 7) {
                    $list = $this->plugin->spawnerLists['overworld_hostile'] ?? [];
                    $category = 'overworld_hostile';
                } else {
                    if ($block->getTypeId() === \pocketmine\block\VanillaBlocks::GRASS()->getTypeId()) {
                        if (mt_rand(1, 10) === 1) {
                            $list = $this->plugin->spawnerLists['overworld_passive'] ?? [];
                            $category = 'overworld_passive';
                        }
                    }
                }
            }
        }

        $aquaticClasses = [
            \BeeAZ\AZVanillaMobs\entity\overworld\Axolotl::class,
            \BeeAZ\AZVanillaMobs\entity\overworld\Dolphin::class,
            \BeeAZ\AZVanillaMobs\entity\overworld\GlowSquid::class,
            \BeeAZ\AZVanillaMobs\entity\overworld\Squid::class,
            \BeeAZ\AZVanillaMobs\entity\overworld\Tadpole::class,
            \BeeAZ\AZVanillaMobs\entity\overworld\Turtle::class,
            \BeeAZ\AZVanillaMobs\entity\overworld\Guardian::class,
            \BeeAZ\AZVanillaMobs\entity\overworld\ElderGuardian::class,
        ];

        if (!empty($list)) {

            $filteredList = [];
            foreach ($list as $c) {
                $isAquaticMob = in_array($c, $aquaticClasses);

                if ($isWater && !$isAquaticMob) {
                    continue;
                }
                if (!$isWater && $isAquaticMob) {
                    continue;
                }

                $parts = explode('\\', $c);
                $name = strtolower(array_pop($parts));
                if (in_array($name, ['stray', 'husk', 'drowned']) && $category === 'overworld_hostile') {
                    continue;
                }
                $filteredList[] = $c;
            }

            if (empty($filteredList)) return;
            $class = $filteredList[array_rand($filteredList)];

            $spawnY = $isWater ? $y : $y + 1;
            $spawnPos = new Position($x + 0.5, $spawnY, $z + 0.5, $world);

            $entity = new $class(Location::fromObject($spawnPos, $world, mt_rand(0, 360), 0));
            $entity->spawnToAll();
        }
    }
}
