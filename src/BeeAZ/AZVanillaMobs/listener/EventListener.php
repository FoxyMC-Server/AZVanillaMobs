<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\listener;

use pocketmine\event\Listener;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\event\player\PlayerToggleSneakEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\InteractPacket;
use pocketmine\network\mcpe\protocol\MovePlayerPacket;
use pocketmine\network\mcpe\protocol\types\PlayerAuthInputFlags;
use pocketmine\world\particle\HeartParticle;
use BeeAZ\AZVanillaMobs\entity\overworld\Zombie;
use BeeAZ\AZVanillaMobs\entity\overworld\Horse;
use BeeAZ\AZVanillaMobs\entity\overworld\Donkey;
use BeeAZ\AZVanillaMobs\entity\overworld\Mule;
use BeeAZ\AZVanillaMobs\entity\overworld\Camel;
use BeeAZ\AZVanillaMobs\entity\overworld\Panda;
use BeeAZ\AZVanillaMobs\entity\overworld\SnowGolem;
use BeeAZ\AZVanillaMobs\entity\overworld\IronGolem;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use BeeAZ\AZVanillaMobs\entity\overworld\Wolf;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\player\Player;

class EventListener implements Listener {

    private \BeeAZ\AZVanillaMobs\Main $plugin;

    public function __construct(\BeeAZ\AZVanillaMobs\Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onEntitySpawn(EntitySpawnEvent $event): void {
        $entity = $event->getEntity();
        if (get_class($entity) === 'pocketmine\entity\Zombie') {
            $location = $entity->getLocation();
            $newZombie = new Zombie($location);

            $newZombie->setHealth($entity->getHealth());
            $newZombie->setMaxHealth($entity->getMaxHealth());

            $entity->close();
            $newZombie->spawnToAll();
        }
    }

    public function onPlayerInteractEntity(PlayerEntityInteractEvent $event): void {
        $player = $event->getPlayer();
        $entity = $event->getEntity();

        if ($entity instanceof Horse || $entity instanceof Donkey || $entity instanceof Mule || $entity instanceof Camel) {
            $event->cancel();

            $item = $player->getInventory()->getItemInHand();
            $itemName = \pocketmine\world\format\io\GlobalItemDataHandlers::getSerializer()->serializeType($item)->getName();

            if ($itemName === "minecraft:saddle") {
                if (!$entity->isSaddled()) {
                    $entity->setSaddled(true);
                    $item->setCount($item->getCount() - 1);
                    $player->getInventory()->setItemInHand($item);
                    return;
                }
            }

            if ($entity->isSaddled()) {
                $entity->mountPlayer($player);
            }
        } elseif ($entity instanceof Panda) {
            $item = $player->getInventory()->getItemInHand();
            $bambooItem = \pocketmine\item\StringToItemParser::getInstance()->parse("bamboo");

            if ($bambooItem !== null && $item->equals($bambooItem, false, false)) {
                $event->cancel();

                $entity->getWorld()->addParticle($entity->getLocation()->add(0, 1.5, 0), new HeartParticle(3));

                if (!$entity->isTamed()) {
                    $entity->setTamed(true);
                }

                $entity->startEating();

                $item->setCount($item->getCount() - 1);
                $player->getInventory()->setItemInHand($item);
                return;
            }
        }
    }

    public function onPlayerToggleSneak(PlayerToggleSneakEvent $event): void {
        $player = $event->getPlayer();
        if ($event->isSneaking()) {
            foreach ($player->getWorld()->getEntities() as $entity) {
                if (($entity instanceof Horse || $entity instanceof Donkey || $entity instanceof Mule || $entity instanceof Camel) && $entity->getRider() !== null && $entity->getRider()->getId() === $player->getId()) {
                    $entity->dismountPlayer();
                    break;
                }
            }
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void {
        $packet = $event->getPacket();
        if ($packet instanceof PlayerAuthInputPacket) {
            $player = $event->getOrigin()->getPlayer();
            if ($player !== null) {
                foreach ($player->getWorld()->getEntities() as $entity) {
                    if (($entity instanceof Horse || $entity instanceof Donkey || $entity instanceof Mule || $entity instanceof Camel) && $entity->getRider() !== null && $entity->getRider()->getId() === $player->getId()) {
                        $inputFlags = $packet->getInputFlags();
                        $horseLocation = $entity->getLocation();

                        $seatHeight = 2.0;
                        if ($entity instanceof Horse) {
                            $seatHeight = 2.3;
                        } elseif ($entity instanceof Camel) {
                            $seatHeight = 2.8;
                        }

                        $expectedY = $horseLocation->y + $seatHeight;
                        $eyePosition = new \pocketmine\math\Vector3($horseLocation->x, $expectedY + 1.62, $horseLocation->z);

                        $originalPosition = $packet->getPosition();
                        $clientFeetY = $originalPosition->y - 1.62;

                        if (abs($clientFeetY - $expectedY) > 0.12) {

                            $pk = MovePlayerPacket::create(
                                $player->getId(),
                                $eyePosition,
                                $player->getLocation()->pitch,
                                $player->getLocation()->yaw,
                                $player->getLocation()->yaw,
                                MovePlayerPacket::MODE_NORMAL,
                                $player->onGround,
                                $entity->getId(),
                                0,
                                0,
                                0
                            );
                            $player->getNetworkSession()->sendDataPacket($pk);
                        }

                        $eyePosition = new \pocketmine\math\Vector3($horseLocation->x, $expectedY + 1.62, $horseLocation->z);
                        try {
                            $reflection = new \ReflectionClass($packet);
                            $property = $reflection->getProperty("position");
                            $property->setAccessible(true);
                            $property->setValue($packet, $eyePosition);
                        } catch (\ReflectionException $e) {

                        }

                        $moveVecX = $packet->getMoveVecX();
                        $moveVecZ = $packet->getMoveVecZ();

                        if (abs($moveVecX) > 0.05 || abs($moveVecZ) > 0.05) {
                            $yaw = $player->getLocation()->yaw;

                            $forwardX = -sin(deg2rad($yaw));
                            $forwardZ = cos(deg2rad($yaw));

                            $rightX = cos(deg2rad($yaw));
                            $rightZ = sin(deg2rad($yaw));

                            $speed = 0.38;
                            if ($entity instanceof Horse) {
                                $speed = 0.52;
                            } elseif ($entity instanceof Camel) {
                                $speed = 0.42;
                            }

                            $motionX = ($forwardX * $moveVecZ + $rightX * $moveVecX) * $speed;
                            $motionZ = ($forwardZ * $moveVecZ + $rightZ * $moveVecX) * $speed;

                            $currentLocation = $entity->getLocation();
                            $newPos = $currentLocation->add($motionX, 0, $motionZ);

                            $blockAtNewPos = $entity->getWorld()->getBlock($newPos);
                            $blockAbove = $entity->getWorld()->getBlock($newPos->add(0, 1.0, 0));

                            if (!$blockAtNewPos->isSolid()) {

                                $entity->teleport($newPos);
                            } else if (!$blockAbove->isSolid()) {

                                $entity->teleport($newPos->add(0, 1.0, 0));
                            }
                        }
                        break;
                    }
                }
            }
        } elseif ($packet instanceof InteractPacket) {
            if ($packet->action === InteractPacket::ACTION_LEAVE_VEHICLE) {
                $player = $event->getOrigin()->getPlayer();
                if ($player !== null) {
                    foreach ($player->getWorld()->getEntities() as $entity) {
                        if (($entity instanceof Horse || $entity instanceof Donkey || $entity instanceof Mule || $entity instanceof Camel) && $entity->getRider() !== null && $entity->getRider()->getId() === $player->getId()) {
                            $entity->dismountPlayer();
                            break;
                        }
                    }
                }
            }
        }
    }

    public function onBlockBreak(\pocketmine\event\block\BlockBreakEvent $event): void {
        $block = $event->getBlock();
        $blockName = strtolower($block->getName());

        if (str_contains($blockName, "beehive") || str_contains($blockName, "bee nest") || str_contains($blockName, "bee_nest") || str_contains($blockName, "tổ ong")) {
            $player = $event->getPlayer();
            if ($player->isCreative()) return;

            foreach ($block->getPosition()->getWorld()->getEntities() as $entity) {
                if ($entity instanceof \BeeAZ\AZVanillaMobs\entity\overworld\Bee) {
                    if ($entity->getLocation()->distanceSquared($block->getPosition()) <= 256) {
                        $entity->anger($player);
                    }
                }
            }
        }
    }

    public function onBlockPlace(BlockPlaceEvent $event): void {
        if ($event->isCancelled()) return;
        $block = null;
        foreach ($event->getTransaction()->getBlocks() as [$x, $y, $z, $b]) {
            $block = $b;
        }
        if ($block === null) return;

        $typeId = $block->getTypeId();

        if ($typeId === BlockTypeIds::CARVED_PUMPKIN || $typeId === BlockTypeIds::LIT_PUMPKIN || $typeId === BlockTypeIds::PUMPKIN) {
            $world = $block->getPosition()->getWorld();
            $pos = $block->getPosition();

            $below1 = $world->getBlock($pos->subtract(0, 1, 0));
            $below2 = $world->getBlock($pos->subtract(0, 2, 0));

            if ($below1->getTypeId() === BlockTypeIds::SNOW && $below2->getTypeId() === BlockTypeIds::SNOW) {

                $this->plugin->getScheduler()->scheduleDelayedTask(
                    new class($world, $pos) extends \pocketmine\scheduler\Task {
                        private $world;
                        private $pos;
                        public function __construct($world, $pos) {
                            $this->world = $world;
                            $this->pos = $pos;
                        }
                        public function onRun() : void {
                            $this->world->setBlock($this->pos, VanillaBlocks::AIR());
                            $this->world->setBlock($this->pos->subtract(0, 1, 0), VanillaBlocks::AIR());
                            $this->world->setBlock($this->pos->subtract(0, 2, 0), VanillaBlocks::AIR());
                        }
                    },
                    1
                );

                $location = new \pocketmine\entity\Location($pos->getX() + 0.5, $pos->getY() - 2, $pos->getZ() + 0.5, $world, (float) mt_rand(0, 360), 0.0);
                $golem = new SnowGolem($location);
                $golem->spawnToAll();
                return;
            }

            if ($below1->getTypeId() === BlockTypeIds::IRON && $below2->getTypeId() === BlockTypeIds::IRON) {

                $leftArm = $world->getBlock($pos->add(-1, -1, 0));
                $rightArm = $world->getBlock($pos->add(1, -1, 0));
                $isXAligned = ($leftArm->getTypeId() === BlockTypeIds::IRON && $rightArm->getTypeId() === BlockTypeIds::IRON);

                $frontArm = $world->getBlock($pos->add(0, -1, -1));
                $backArm = $world->getBlock($pos->add(0, -1, 1));
                $isZAligned = ($frontArm->getTypeId() === BlockTypeIds::IRON && $backArm->getTypeId() === BlockTypeIds::IRON);

                if ($isXAligned || $isZAligned) {

                    $this->plugin->getScheduler()->scheduleDelayedTask(
                        new class($world, $pos, $isXAligned) extends \pocketmine\scheduler\Task {
                            private $world;
                            private $pos;
                            private $isXAligned;
                            public function __construct($world, $pos, $isXAligned) {
                                $this->world = $world;
                                $this->pos = $pos;
                                $this->isXAligned = $isXAligned;
                            }
                            public function onRun() : void {
                                $this->world->setBlock($this->pos, VanillaBlocks::AIR());
                                $this->world->setBlock($this->pos->subtract(0, 1, 0), VanillaBlocks::AIR());
                                $this->world->setBlock($this->pos->subtract(0, 2, 0), VanillaBlocks::AIR());

                                if ($this->isXAligned) {
                                    $this->world->setBlock($this->pos->add(-1, -1, 0), VanillaBlocks::AIR());
                                    $this->world->setBlock($this->pos->add(1, -1, 0), VanillaBlocks::AIR());
                                } else {
                                    $this->world->setBlock($this->pos->add(0, -1, -1), VanillaBlocks::AIR());
                                    $this->world->setBlock($this->pos->add(0, -1, 1), VanillaBlocks::AIR());
                                }
                            }
                        },
                        1
                    );

                    $location = new \pocketmine\entity\Location($pos->getX() + 0.5, $pos->getY() - 2, $pos->getZ() + 0.5, $world, (float) mt_rand(0, 360), 0.0);
                    $golem = new IronGolem($location);
                    $golem->spawnToAll();
                }
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void {
        if ($event->isCancelled()) return;
        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            $entity = $event->getEntity();

            if ($entity instanceof Player && $damager instanceof \pocketmine\entity\Living) {
                $uuid = $entity->getUniqueId()->toString();
                foreach ($entity->getWorld()->getNearbyEntities($entity->getBoundingBox()->expandedCopy(16, 8, 16)) as $near) {
                    if ($near instanceof Wolf && $near->isTamed() && $near->getOwnerUuid() === $uuid && !$near->isSitting()) {
                        $near->setAngryTarget($damager);
                    }
                }
            }

            if ($damager instanceof Player && $entity instanceof \pocketmine\entity\Living) {

                if (!($entity instanceof Wolf && $entity->isTamed() && $entity->getOwnerUuid() === $damager->getUniqueId()->toString())) {
                    $uuid = $damager->getUniqueId()->toString();
                    foreach ($damager->getWorld()->getNearbyEntities($damager->getBoundingBox()->expandedCopy(16, 8, 16)) as $near) {
                        if ($near instanceof Wolf && $near->isTamed() && $near->getOwnerUuid() === $uuid && !$near->isSitting()) {
                            $near->setAngryTarget($entity);
                        }
                    }
                }
            }
        }
    }
}
