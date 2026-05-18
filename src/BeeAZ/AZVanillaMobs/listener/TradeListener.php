<?php

declare(strict_types=1);

namespace BeeAZ\AZVanillaMobs\listener;

use BeeAZ\AZVanillaMobs\Main;
use BeeAZ\AZVanillaMobs\entity\overworld\Villager;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerEntityInteractEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\convert\TypeConverter;
use pocketmine\network\mcpe\protocol\ContainerClosePacket;
use pocketmine\network\mcpe\protocol\ItemStackResponsePacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\UpdateTradePacket;
use pocketmine\network\mcpe\protocol\ContainerOpenPacket;
use pocketmine\network\mcpe\protocol\types\inventory\WindowTypes;
use pocketmine\network\mcpe\protocol\types\inventory\stackresponse\ItemStackResponse;
use pocketmine\scheduler\Task;

class TradeListener implements Listener {

    private Main $plugin;
    public static array $trading = [];

    public function __construct(Main $plugin) {
        $this->plugin = $plugin;
    }

    public function onPlayerInteractEntity(PlayerEntityInteractEvent $event): void {
        $player = $event->getPlayer();
        $entity = $event->getEntity();

        if ($entity instanceof Villager) {
            $event->cancel();
            
            self::$trading[$player->getName()] = $entity;
            
            $openPk = ContainerOpenPacket::entityInv(
                99, WindowTypes::TRADING, $entity->getId()
            );
            $player->getNetworkSession()->sendDataPacket($openPk);
            
            $offersNbt = \BeeAZ\AZVanillaMobs\utils\TradeManager::buildOffersNbt($entity->getTradeRecipes());
            $pk = UpdateTradePacket::create(
                99, WindowTypes::TRADING, 0, 1, $entity->getId(), $player->getId(), "Villager", true, true, $offersNbt
            );
            $player->getNetworkSession()->sendDataPacket($pk);
        }
    }

    public function onDataPacketReceive(DataPacketReceiveEvent $event): void {
        $packet = $event->getPacket();
        
        if ($packet instanceof PlayerAuthInputPacket) {
            $player = $event->getOrigin()->getPlayer();
            if ($player !== null && isset(self::$trading[$player->getName()])) {
                $villager = self::$trading[$player->getName()];
                if (!$villager->isAlive() || $player->getWorld() !== $villager->getWorld() || $player->getLocation()->distanceSquared($villager->getLocation()) > 64) {
                    unset(self::$trading[$player->getName()]);
                    $player->getNetworkSession()->sendDataPacket(ContainerClosePacket::create(99, WindowTypes::TRADING, true));
                }
            }
        } elseif ($packet instanceof ContainerClosePacket) {
            $player = $event->getOrigin()->getPlayer();
            if ($player !== null && isset(self::$trading[$player->getName()])) {
                if ($packet->windowId === 99) {
                    unset(self::$trading[$player->getName()]);
                }
            }
        } elseif ($packet instanceof \pocketmine\network\mcpe\protocol\ItemStackRequestPacket) {
            $player = $event->getOrigin()->getPlayer();
            
            if ($player !== null && isset(self::$trading[$player->getName()])) {
                
                $tradeUIs = [31, 32, 33, 47, 48, 49, 50, 51, 99];
                $touchesTradeUI = false;
                $isTradeAttempt = false;
                $recipeId = null;

  
                foreach ($packet->getRequests() as $request) {
                    foreach ($request->getActions() as $action) {
                        $c1 = method_exists($action, 'getSource') ? $action->getSource()->getContainerName()->getContainerId() : -1;
                        $c2 = method_exists($action, 'getDestination') ? $action->getDestination()->getContainerName()->getContainerId() : -1;
                        $c3 = method_exists($action, 'getSlot1') ? $action->getSlot1()->getContainerName()->getContainerId() : -1;
                        $c4 = method_exists($action, 'getSlot2') ? $action->getSlot2()->getContainerName()->getContainerId() : -1;

                        if (in_array($c1, $tradeUIs, true) || in_array($c2, $tradeUIs, true) || in_array($c3, $tradeUIs, true) || in_array($c4, $tradeUIs, true)) {
                            $touchesTradeUI = true;
                        }

                
                        if ($action instanceof \pocketmine\network\mcpe\protocol\types\inventory\stackrequest\CraftRecipeStackRequestAction) {
                            $touchesTradeUI = true;
                            $isTradeAttempt = true;
                            $recipeId = $action->getRecipeId();
                        }
                    }
                }

      
                if (!$touchesTradeUI) {
                    return; 
                }

                $event->cancel();
                

                if ($isTradeAttempt && $recipeId !== null) {
                    $villager = self::$trading[$player->getName()];
                    $recipes = $villager->getTradeRecipes();
                    $inv = $player->getInventory();

  
                    $trade = $recipes[$recipeId] ?? null;

                    if ($trade !== null) {
                        $buyA = clone $trade['buyA'];
                        $buyB = $trade['buyB'] !== null ? clone $trade['buyB'] : null;
                        $sell = clone $trade['sell'];


                        if ($buyB !== null && $buyA->getTypeId() === $buyB->getTypeId() && $buyA->getCustomName() === $buyB->getCustomName()) {
                            $buyA->setCount($buyA->getCount() + $buyB->getCount());
                            $buyB = null;
                        }

                        if ($this->hasItems($inv, $buyA) && ($buyB === null || $this->hasItems($inv, $buyB))) {
                            $this->removeItems($inv, $buyA);
                            if ($buyB !== null) {
                                $this->removeItems($inv, $buyB);
                            }

                            $leftovers = $inv->addItem($sell);
                            foreach ($leftovers as $l) {
                                $player->getWorld()->dropItem($player->getLocation(), $l);
                            }
                            
                            $player->getWorld()->addSound($player->getLocation(), new \pocketmine\world\sound\XpLevelUpSound(30));
                        }
                    }
                }

                $responses = [];
                foreach ($packet->getRequests() as $request) {
                    $responses[] = new ItemStackResponse(ItemStackResponse::RESULT_ERROR, $request->getRequestId(), []);
                }
                $player->getNetworkSession()->sendDataPacket(ItemStackResponsePacket::create($responses));

                $this->plugin->getScheduler()->scheduleDelayedTask(
                    new class($player) extends Task {
                        private $player;
                        public function __construct($player) { $this->player = $player; }
                        public function onRun() : void {
                            if ($this->player->isOnline()) {
                                $invManager = $this->player->getNetworkSession()->getInvManager();
                                if ($invManager !== null) {
                                    $invManager->syncContents($this->player->getInventory());
                                    $invManager->syncContents($this->player->getCursorInventory());
                                }
                            }
                        }
                    },
                    1 
                );
            }
        }
    }

    public function onPlayerQuit(PlayerQuitEvent $event): void {
        $name = $event->getPlayer()->getName();
        if (isset(self::$trading[$name])) {
            unset(self::$trading[$name]);
        }
    }

    private function hasItems(\pocketmine\inventory\Inventory $inv, \pocketmine\item\Item $target): bool {
        if ($target->isNull()) return true;
        $count = 0;
        foreach ($inv->getContents() as $item) {

            if ($item->getTypeId() === $target->getTypeId() && $item->getCustomName() === $target->getCustomName()) {
                $count += $item->getCount();
            }
        }
        return $count >= $target->getCount();
    }

    private function removeItems(\pocketmine\inventory\Inventory $inv, \pocketmine\item\Item $target): void {
        if ($target->isNull()) return;
        $count = $target->getCount();
        foreach ($inv->getContents() as $slot => $item) {
            if ($item->getTypeId() === $target->getTypeId() && $item->getCustomName() === $target->getCustomName()) {
                $take = min($count, $item->getCount());
                $item->setCount($item->getCount() - $take);
                $inv->setItem($slot, $item->getCount() > 0 ? $item : VanillaItems::AIR());
                
                $count -= $take;
                if ($count <= 0) return;
            }
        }
    }
}