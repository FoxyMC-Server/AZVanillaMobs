<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs;

use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\plugin\PluginBase;
use pocketmine\nbt\tag\CompoundTag;
use BeeAZ\AZVanillaMobs\loot\LootManager;
use BeeAZ\AZVanillaMobs\spawner\SpawnerTask;
use pocketmine\data\bedrock\item\SavedItemData;
use BeeAZ\AZVanillaMobs\item\{Lead, FishBucket};
use pocketmine\world\format\io\GlobalItemDataHandlers;
use BeeAZ\AZVanillaMobs\command\{SummonCommand, KillCommand};
use pocketmine\inventory\{CreativeInventory, CreativeCategory};
use pocketmine\entity\{Entity, EntityFactory, EntityDataHelper, Location};
use BeeAZ\AZVanillaMobs\entity\the_end\{EnderDragon, Enderman, Endermite, Shulker};
use BeeAZ\AZVanillaMobs\entity\projectile\{GhastFireball, BlazeFireball, WitchPotion};
use pocketmine\item\{Item, StringToItemParser, ItemTypeIds, ItemIdentifier, SpawnEgg};
use BeeAZ\AZVanillaMobs\listener\{EventListener, TradeListener, LeashListener, RidingListener};
use BeeAZ\AZVanillaMobs\entity\nether\{Blaze, Ghast, Hoglin, MagmaCube, Piglin, PiglinBrute, Strider, WitherSkeleton, Zoglin, ZombifiedPiglin};
use BeeAZ\AZVanillaMobs\entity\overworld\{Allay, Axolotl, Bat, Bee, Camel, Cat, CaveSpider, Chicken, Cod, Cow, Creeper, Dolphin, Donkey, Drowned, ElderGuardian, Evoker, Fox, Frog, GlowSquid, Goat, Guardian, Horse, Husk, IronGolem, Llama, Mule, Ocelot, Panda, Phantom, Pig, Pillager, Pufferfish, Ravager, Salmon, Sheep, Silverfish, Skeleton, Slime, Sniffer, SnowGolem, Spider, Squid, Stray, Tadpole, TraderLlama, TropicalFish, Turtle, Vex, Villager, Vindicator, WanderingTrader, Warden, Witch, Wolf, Zombie, ZombieVillager};

class Main extends PluginBase {

    public array $spawnerLists = [
        'nether' => [],
        'the_end' => [],
        'overworld_hostile' => [],
        'overworld_passive' => []
    ];

    protected function onEnable(): void {
        $server = $this->getServer();

        $this->saveDefaultConfig();
        $server->getPluginManager()->registerEvents(new LootManager(), $this);
        $server->getPluginManager()->registerEvents(new EventListener($this), $this);
        $server->getPluginManager()->registerEvents(new TradeListener($this), $this);
        $server->getPluginManager()->registerEvents(new LeashListener($this), $this);
        $server->getPluginManager()->registerEvents(new RidingListener($this), $this);

        $map = $this->getServer()->getCommandMap();
        $cmd = $map->getCommand("summon");
        if ($cmd !== null) $map->unregister($cmd);
        $map->register("AZVanillaMobs", new SummonCommand($this));
        $map->register("AZVanillaMobs", new KillCommand($this));

        $this->registerProjectileEntities();
        $this->registerMobEntities();
        $this->registerSaddleItem();
        $this->registerLeadItem();
        $this->registerFishBuckets();

        $this->getScheduler()->scheduleRepeatingTask(new SpawnerTask($this), 20);
        $this->getScheduler()->scheduleRepeatingTask(new class($this) extends Task {
            public function onRun(): void {
                LeashListener::tickLeashes();
            }
        }, 2);
    }
    
    /**
     * Helper function to register a custom item.
     * It handles serialization, deserialization, adding to the creative inventory
     * and the registration in the StringToItemParser.
     *
     * @param  mixed $vanillaId
     * @param  mixed $item
     * @param  mixed $category
     * @return void
     */
    private function registerItemMapping(string $vanillaId, Item $item, ?CreativeCategory $category = null): void {
        try {
            GlobalItemDataHandlers::getSerializer()->map($item, fn() => new SavedItemData($vanillaId));
            GlobalItemDataHandlers::getDeserializer()->map($vanillaId, fn() => clone $item);

            if ($category !== null) {
                CreativeInventory::getInstance()->add($item, $category);
            } else {
                CreativeInventory::getInstance()->add($item);
            }

            $parser = StringToItemParser::getInstance();
            if (method_exists($parser, 'override')) {
                $parser->override($vanillaId, fn() => clone $item);
            } else {
                $parser->register($vanillaId, fn() => clone $item);
            }
        } catch (\Exception $e) {
        }
    }
   
    /**
     * createSpawnEggItem
     *
     * @param  mixed $class
     * @param  mixed $name
     * @param  mixed $parseId
     * @return Item
     */
    private function createSpawnEggItem(string $class, string $name, string $parseId): Item {
        $typeId = ItemTypeIds::newId();
        $identifier = new ItemIdentifier($typeId);

        $eggItem = new class($identifier, "§r§e" . $name . " Spawn Egg") extends SpawnEgg {
            public string $entityClass;
            protected function createEntity(World $world, Vector3 $pos, float $yaw, float $pitch): Entity {
                $c = $this->entityClass;
                return new $c(Location::fromObject($pos, $world, $yaw, $pitch));
            }
        };
        $eggItem->entityClass = $class;

        return $eggItem;
    }

    private function registerProjectileEntities(): void {
        $factory = EntityFactory::getInstance();

        $factory->register(GhastFireball::class, function(World $world, CompoundTag $nbt) : Entity {
            return new GhastFireball(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['GhastFireball', 'minecraft:fireball']);

        $factory->register(BlazeFireball::class, function(World $world, CompoundTag $nbt) : Entity {
            return new BlazeFireball(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['BlazeFireball', 'minecraft:small_fireball']);

        $factory->register(WitchPotion::class, function(World $world, CompoundTag $nbt) : Entity {
            return new WitchPotion(EntityDataHelper::parseLocation($nbt, $world), null, $nbt);
        }, ['WitchPotion', 'minecraft:splash_potion']);
    }

    private function registerMobEntities(): void {
        $this->registerMob(Skeleton::class, 'Skeleton', 'minecraft:skeleton', 'overworld_hostile');
        $this->registerMob(Zombie::class, 'Zombie', 'minecraft:zombie', 'overworld_hostile');
        $this->registerMob(Creeper::class, 'Creeper', 'minecraft:creeper', 'overworld_hostile');
        $this->registerMob(Spider::class, 'Spider', 'minecraft:spider', 'overworld_hostile');
        $this->registerMob(CaveSpider::class, 'CaveSpider', 'minecraft:cave_spider', 'overworld_hostile');
        $this->registerMob(Slime::class, 'Slime', 'minecraft:slime', 'overworld_hostile');
        $this->registerMob(Silverfish::class, 'Silverfish', 'minecraft:silverfish', 'overworld_hostile');
        $this->registerMob(Witch::class, 'Witch', 'minecraft:witch', 'overworld_hostile');
        $this->registerMob(ZombieVillager::class, 'ZombieVillager', 'minecraft:zombie_villager', 'overworld_hostile');
        $this->registerMob(Drowned::class, 'Drowned', 'minecraft:drowned', 'overworld_hostile');
        $this->registerMob(Husk::class, 'Husk', 'minecraft:husk', 'overworld_hostile');
        $this->registerMob(Stray::class, 'Stray', 'minecraft:stray', 'overworld_hostile');
        $this->registerMob(Phantom::class, 'Phantom', 'minecraft:phantom', 'overworld_hostile');
        $this->registerMob(Vindicator::class, 'Vindicator', 'minecraft:vindicator', 'overworld_hostile');
        $this->registerMob(Evoker::class, 'Evoker', 'minecraft:evoker', 'overworld_hostile');
        $this->registerMob(Pillager::class, 'Pillager', 'minecraft:pillager', 'overworld_hostile');
        $this->registerMob(Ravager::class, 'Ravager', 'minecraft:ravager', 'overworld_hostile');
        $this->registerMob(Vex::class, 'Vex', 'minecraft:vex', 'overworld_hostile');
        $this->registerMob(Guardian::class, 'Guardian', 'minecraft:guardian', 'overworld_hostile');
        $this->registerMob(ElderGuardian::class, 'ElderGuardian', 'minecraft:elder_guardian', 'overworld_hostile');
        $this->registerMob(Warden::class, 'Warden', 'minecraft:warden', 'overworld_hostile');

        $this->registerMob(Cow::class, 'Cow', 'minecraft:cow', 'overworld_passive');
        $this->registerMob(Pig::class, 'Pig', 'minecraft:pig', 'overworld_passive');
        $this->registerMob(Sheep::class, 'Sheep', 'minecraft:sheep', 'overworld_passive');
        $this->registerMob(Chicken::class, 'Chicken', 'minecraft:chicken', 'overworld_passive');
        $this->registerMob(Wolf::class, 'Wolf', 'minecraft:wolf', 'overworld_passive');
        $this->registerMob(Ocelot::class, 'Ocelot', 'minecraft:ocelot', 'overworld_passive');
        $this->registerMob(Cat::class, 'Cat', 'minecraft:cat', 'overworld_passive');
        $this->registerMob(Horse::class, 'Horse', 'minecraft:horse', 'overworld_passive');
        $this->registerMob(Donkey::class, 'Donkey', 'minecraft:donkey', 'overworld_passive');
        $this->registerMob(Mule::class, 'Mule', 'minecraft:mule', 'overworld_passive');
        $this->registerMob(Llama::class, 'Llama', 'minecraft:llama', 'overworld_passive');
        $this->registerMob(TraderLlama::class, 'TraderLlama', 'minecraft:trader_llama', 'overworld_passive');
        $this->registerMob(Fox::class, 'Fox', 'minecraft:fox', 'overworld_passive');
        $this->registerMob(Panda::class, 'Panda', 'minecraft:panda', 'overworld_passive');
        $this->registerMob(Turtle::class, 'Turtle', 'minecraft:turtle', 'overworld_passive');
        $this->registerMob(Dolphin::class, 'Dolphin', 'minecraft:dolphin', 'overworld_passive');
        $this->registerMob(Squid::class, 'Squid', 'minecraft:squid', 'overworld_passive');
        $this->registerMob(GlowSquid::class, 'GlowSquid', 'minecraft:glow_squid', 'overworld_passive');
        $this->registerMob(Bat::class, 'Bat', 'minecraft:bat', 'overworld_passive');
        $this->registerMob(Villager::class, 'Villager', 'minecraft:villager_v2', 'overworld_passive');
        $this->registerMob(WanderingTrader::class, 'WanderingTrader', 'minecraft:wandering_trader', 'overworld_passive');
        $this->registerMob(IronGolem::class, 'IronGolem', 'minecraft:iron_golem', 'overworld_passive');
        $this->registerMob(SnowGolem::class, 'SnowGolem', 'minecraft:snow_golem', 'overworld_passive');
        $this->registerMob(Axolotl::class, 'Axolotl', 'minecraft:axolotl', 'overworld_passive');
        $this->registerMob(Goat::class, 'Goat', 'minecraft:goat', 'overworld_passive');
        $this->registerMob(Frog::class, 'Frog', 'minecraft:frog', 'overworld_passive');
        $this->registerMob(Tadpole::class, 'Tadpole', 'minecraft:tadpole', 'overworld_passive');
        $this->registerMob(Cod::class, 'Cod', 'minecraft:cod', 'overworld_passive');
        $this->registerMob(Salmon::class, 'Salmon', 'minecraft:salmon', 'overworld_passive');
        $this->registerMob(Pufferfish::class, 'Pufferfish', 'minecraft:pufferfish', 'overworld_passive');
        $this->registerMob(TropicalFish::class, 'TropicalFish', 'minecraft:tropicalfish', 'overworld_passive');
        $this->registerMob(Camel::class, 'Camel', 'minecraft:camel', 'overworld_passive');
        $this->registerMob(Sniffer::class, 'Sniffer', 'minecraft:sniffer', 'overworld_passive');
        $this->registerMob(Allay::class, 'Allay', 'minecraft:allay', 'overworld_passive');
        $this->registerMob(Bee::class, 'Bee', 'minecraft:bee', 'overworld_passive');

        $this->registerMob(ZombifiedPiglin::class, 'ZombifiedPiglin', 'minecraft:zombie_pigman', 'nether');
        $this->registerMob(Piglin::class, 'Piglin', 'minecraft:piglin', 'nether');
        $this->registerMob(PiglinBrute::class, 'PiglinBrute', 'minecraft:piglin_brute', 'nether');
        $this->registerMob(Hoglin::class, 'Hoglin', 'minecraft:hoglin', 'nether');
        $this->registerMob(Zoglin::class, 'Zoglin', 'minecraft:zoglin', 'nether');
        $this->registerMob(Ghast::class, 'Ghast', 'minecraft:ghast', 'nether');
        $this->registerMob(Blaze::class, 'Blaze', 'minecraft:blaze', 'nether');
        $this->registerMob(MagmaCube::class, 'MagmaCube', 'minecraft:magma_cube', 'nether');
        $this->registerMob(WitherSkeleton::class, 'WitherSkeleton', 'minecraft:wither_skeleton', 'nether');
        $this->registerMob(Strider::class, 'Strider', 'minecraft:strider', 'nether');

        $this->registerMob(Enderman::class, 'Enderman', 'minecraft:enderman', 'overworld_hostile');
        $this->spawnerLists['the_end'][] = Enderman::class;
        $this->registerMob(Endermite::class, 'Endermite', 'minecraft:endermite', 'the_end');
        $this->registerMob(Shulker::class, 'Shulker', 'minecraft:shulker', 'the_end');

        EntityFactory::getInstance()->register(EnderDragon::class, function(World $world, CompoundTag $nbt) : Entity {
            return new EnderDragon(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, ['EnderDragon', 'minecraft:ender_dragon']);
    }

    private function registerMob(string $class, string $name, string $id, string $category): void {
        EntityFactory::getInstance()->register($class, function(World $world, CompoundTag $nbt) use ($class) : Entity {
            return new $class(EntityDataHelper::parseLocation($nbt, $world), $nbt);
        }, [$name, $id]);

        $this->spawnerLists[$category][] = $class;

        $eggMapping = [
            'minecraft:skeleton' => 'skeleton_spawn_egg',
            'minecraft:zombie' => 'zombie_spawn_egg',
            'minecraft:creeper' => 'creeper_spawn_egg',
            'minecraft:spider' => 'spider_spawn_egg',
            'minecraft:cave_spider' => 'cave_spider_spawn_egg',
            'minecraft:slime' => 'slime_spawn_egg',
            'minecraft:silverfish' => 'silverfish_spawn_egg',
            'minecraft:witch' => 'witch_spawn_egg',
            'minecraft:zombie_villager' => 'zombie_villager_spawn_egg',
            'minecraft:drowned' => 'drowned_spawn_egg',
            'minecraft:husk' => 'husk_spawn_egg',
            'minecraft:stray' => 'stray_spawn_egg',
            'minecraft:phantom' => 'phantom_spawn_egg',
            'minecraft:vindicator' => 'vindicator_spawn_egg',
            'minecraft:evoker' => 'evoker_spawn_egg',
            'minecraft:pillager' => 'pillager_spawn_egg',
            'minecraft:ravager' => 'ravager_spawn_egg',
            'minecraft:vex' => 'vex_spawn_egg',
            'minecraft:guardian' => 'guardian_spawn_egg',
            'minecraft:elder_guardian' => 'elder_guardian_spawn_egg',
            'minecraft:cow' => 'cow_spawn_egg',
            'minecraft:pig' => 'pig_spawn_egg',
            'minecraft:sheep' => 'sheep_spawn_egg',
            'minecraft:chicken' => 'chicken_spawn_egg',
            'minecraft:wolf' => 'wolf_spawn_egg',
            'minecraft:ocelot' => 'ocelot_spawn_egg',
            'minecraft:cat' => 'cat_spawn_egg',
            'minecraft:horse' => 'horse_spawn_egg',
            'minecraft:donkey' => 'donkey_spawn_egg',
            'minecraft:mule' => 'mule_spawn_egg',
            'minecraft:llama' => 'llama_spawn_egg',
            'minecraft:trader_llama' => 'trader_llama_spawn_egg',
            'minecraft:fox' => 'fox_spawn_egg',
            'minecraft:panda' => 'panda_spawn_egg',
            'minecraft:turtle' => 'turtle_spawn_egg',
            'minecraft:dolphin' => 'dolphin_spawn_egg',
            'minecraft:squid' => 'squid_spawn_egg',
            'minecraft:glow_squid' => 'glow_squid_spawn_egg',
            'minecraft:bat' => 'bat_spawn_egg',
            'minecraft:villager_v2' => 'villager_spawn_egg',
            'minecraft:wandering_trader' => 'wandering_trader_spawn_egg',
            'minecraft:axolotl' => 'axolotl_spawn_egg',
            'minecraft:goat' => 'goat_spawn_egg',
            'minecraft:frog' => 'frog_spawn_egg',
            'minecraft:tadpole' => 'tadpole_spawn_egg',
            'minecraft:cod' => 'cod_spawn_egg',
            'minecraft:salmon' => 'salmon_spawn_egg',
            'minecraft:pufferfish' => 'pufferfish_spawn_egg',
            'minecraft:tropicalfish' => 'tropical_fish_spawn_egg',
            'minecraft:camel' => 'camel_spawn_egg',
            'minecraft:sniffer' => 'sniffer_spawn_egg',
            'minecraft:allay' => 'allay_spawn_egg',
            'minecraft:bee' => 'bee_spawn_egg',
            'minecraft:zombie_pigman' => 'zombie_pigman_spawn_egg',
            'minecraft:piglin' => 'piglin_spawn_egg',
            'minecraft:piglin_brute' => 'piglin_brute_spawn_egg',
            'minecraft:hoglin' => 'hoglin_spawn_egg',
            'minecraft:zoglin' => 'zoglin_spawn_egg',
            'minecraft:ghast' => 'ghast_spawn_egg',
            'minecraft:blaze' => 'blaze_spawn_egg',
            'minecraft:magma_cube' => 'magma_cube_spawn_egg',
            'minecraft:wither_skeleton' => 'wither_skeleton_spawn_egg',
            'minecraft:strider' => 'strider_spawn_egg',
            'minecraft:enderman' => 'enderman_spawn_egg',
            'minecraft:endermite' => 'endermite_spawn_egg',
            'minecraft:shulker' => 'shulker_spawn_egg',
            'minecraft:warden' => 'warden_spawn_egg',
        ];

        if (!isset($eggMapping[$id])) {
            return;
        }

        $parseId = $eggMapping[$id];
        $vanillaId = "minecraft:" . $parseId;

        try {
            $oldEgg = StringToItemParser::getInstance()->parse($parseId);
            if ($oldEgg !== null) {
                CreativeInventory::getInstance()->remove($oldEgg);
            }
        } catch (\Exception $e) {}

        $eggItem = $this->createSpawnEggItem($class, $name, $parseId);
        $this->registerItemMapping($vanillaId, $eggItem, CreativeCategory::NATURE);
    }

    private function registerSaddleItem(): void {
        $saddleId = ItemTypeIds::newId();
        $saddleItem = new Item(new ItemIdentifier($saddleId), "Saddle");
        $this->registerItemMapping("minecraft:saddle", $saddleItem, CreativeCategory::EQUIPMENT);
    }

    private function registerLeadItem(): void {
        $leadId = ItemTypeIds::newId();
        $leadItem = new Lead(new ItemIdentifier($leadId), "Lead");
        $this->registerItemMapping("minecraft:lead", $leadItem);
    }

    private function registerFishBuckets(): void {
        $registerBucket = function(string $class, string $name, string $id, string $parseId) {
            $bucketId = ItemTypeIds::newId();
            $bucketItem = new FishBucket(new ItemIdentifier($bucketId), $name, $class);
            $this->registerItemMapping("minecraft:" . $id, $bucketItem, CreativeCategory::NATURE);
        };

        $registerBucket(Cod::class, "Cod Bucket", "cod_bucket", "cod_bucket");
        $registerBucket(Salmon::class, "Salmon Bucket", "salmon_bucket", "salmon_bucket");
        $registerBucket(Pufferfish::class, "Pufferfish Bucket", "pufferfish_bucket", "pufferfish_bucket");
        $registerBucket(TropicalFish::class, "Tropical Fish Bucket", "tropical_fish_bucket", "tropical_fish_bucket");
        $registerBucket(Axolotl::class, "Axolotl Bucket", "axolotl_bucket", "axolotl_bucket");
        $registerBucket(Tadpole::class, "Tadpole Bucket", "tadpole_bucket", "tadpole_bucket");
    }
}