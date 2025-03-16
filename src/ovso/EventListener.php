<?php

namespace ovso;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\item\VanillaItems;
use pocketmine\item\ItemIdentifier;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\player\Player;
use pocketmine\event\entity\EntityDamageEvent;
use pocketmine\event\entity\EntityDamageByEntityEvent;

class EventListener implements Listener {

  public function onPlayerJoin(PlayerJoinEvent $event): void {
      $player = $event->getPlayer();
      $item = VanillaItems::IRON_SWORD();
      $item->setCustomName("§bHerausfordern");
      $player->getInventory()->setItem(4, $item);
    }

    public function onPlayerInteract(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $item = $player->getInventory()->getItemInHand();
        $plugin = $player->getServer()->getPluginManager()->getPlugin("ovso");
        $config = $plugin->getConfig();

        if ($item->getCustomName() === "§bHerausfordern") {
            $targetBlock = $event->getBlock()->getPosition()->getWorld()->getBlock($event->getBlock()->getPosition()->add(0, 1, 0));
            if (!($targetBlock instanceof Air)) {
                if ($targetBlock instanceof Sign){ // Überprüfe, ob der Block ein Schild ist.
                    $targetName = $targetBlock->getText();
                    $target = $player->getServer()->getPlayerByPrefix($targetName);
                    if ($target instanceof Player) {
                        // ... (Rest des Codes)
                    } else {
                        $player->sendMessage("Dieser Spieler wurde nicht gefunden!");
                    }
                } else {
                    $player->sendMessage("Bitte klicke auf ein Spieler mit einem Spielernamen darüber.");
                }
            } else {
                $player->sendMessage("Bitte klicke auf einen Spieler mit einem Spielernamen darüber.");
            }
        } else {
            if (isset($plugin->challenges[$player->getName()])) {
                $challenge = $plugin->challenges[$player->getName()];
                $challenger = $player->getServer()->getPlayerByName($challenge["challenger"]);

                if (time() - $challenge["timestamp"] > $config->get("challenge_timeout")) {
                    $player->sendMessage("Die Herausforderung ist abgelaufen.");
                    unset($plugin->challenges[$player->getName()]);
                    return;
                }

                if ($challenger instanceof Player) {
                    $config = $plugin->getConfig();

                    $pos1 = new \pocketmine\world\Position(
                        $config->get("arenas.arena1.spawn1.x"),
                        $config->get("arenas.arena1.spawn1.y"),
                        $config->get("arenas.arena1.spawn1.z"),
                        $player->getServer()->getWorldManager()->getWorldByName($config->get("arenas.arena1.world"))
                    );

                    $pos2 = new \pocketmine\world\Position(
                        $config->get("arenas.arena2.spawn2.x"),
                        $config->get("arenas.arena2.spawn2.y"),
                        $config->get("arenas.arena2.spawn2.z"),
                        $player->getServer()->getWorldManager()->getWorldByName($config->get("arenas.arena2.world"))
                    );

                    $challenger->teleport($pos1);
                    $player->teleport($pos2);

                    $challenger->sendMessage("1vs1 gestartet gegen " . $player->getName() . "!");
                    $player->sendMessage("1vs1 gestartet gegen " . $challenger->getName() . "!");

                    unset($plugin->challenges[$player->getName()]);
                } else {
                    $player->sendMessage("Der Herausforderer ist nicht mehr online.");
                    unset($plugin->challenges[$player->getName()]);
                }
            }
        }
    }

    public function onEntityDamage(EntityDamageEvent $event): void {
    $entity = $event->getEntity();
    $plugin = $entity->getServer()->getPluginManager()->getPlugin("ovso");

    if ($entity instanceof Player && $event->getFinalDamage() >= $entity->getHealth()) {
        $event->cancel(); // Verhindert den Tod des Spielers

        if ($event instanceof EntityDamageByEntityEvent) {
            $damager = $event->getDamager();
            if ($damager instanceof Player) {
                $this->endGame($damager, $entity);
            }
        } else {
            $this->endGame(null, $entity); // Tod durch andere Ursache (z.B. Fallschaden)
        }
    }
}

private function endGame(?Player $winner, Player $loser): void {
    $plugin = $loser->getServer()->getPluginManager()->getPlugin("ovso");
    $config = $plugin->getConfig();

    if ($winner !== null) {
        $winner->sendMessage("Du hast gewonnen!");
        $loser->sendMessage("Du hast verloren!");
    } else {
        $loser->sendMessage("Du bist gestorben!");
    }

    // Spieler zurückteleportieren
    $loser->teleport($loser->getServer()->getDefaultLevel()->getSafeSpawn());
    if ($winner !== null) {
        $winner->teleport($winner->getServer()->getDefaultLevel()->getSafeSpawn());
    }

    // Spieler aus der 1vs1-Liste entfernen (falls vorhanden)
    // Hier müsstest du deine 1vs1-Listen oder -Variablen überprüfen und die Spieler entfernen

    // Kit entfernen
    $loser->getInventory()->clearAll();
    $loser->getArmorInventory()->clearAll();
    $loser->getEffects()->clear();
    if ($winner !== null) {
        $winner->getInventory()->clearAll();
        $winner->getArmorInventory()->clearAll();
        $winner->getEffects()->clear();
    }

}
}
