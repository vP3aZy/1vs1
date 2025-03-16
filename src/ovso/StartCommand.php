<?php

namespace ovso;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class StartCommand extends Command {

    public function __construct() {
        parent::__construct("1vs1", "Startet ein 1vs1", "/1vs1");
        $this->setPermission("ovso.1vs1.start");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("Dieser Befehl kann nur von Spielern ausgeführt werden.");
            return true;
        }

        $player = $sender;
        $plugin = $player->getServer()->getPluginManager()->getPlugin("ovso");
        $config = $plugin->getConfig();

        if (in_array($player, $plugin->queue)) {
            $player->sendMessage("Du bist bereits in der Warteschlange.");
            return true;
        }

        $plugin->queue[] = $player;
        $player->sendMessage("Du wurdest zur Warteschlange hinzugefügt.");

        if (count($plugin->queue) >= 2) {
            $player1 = array_shift($plugin->queue);
            $player2 = array_shift($plugin->queue);

            KitUI::sendKitSelection($player1, $config->get("kits"));
            KitUI::sendKitSelection($player2, $config->get("kits"));

            // Teleportierung und Nachrichten werden in der KitUI-Callback-Funktion durchgeführt
        }

        return true;
    }
}
