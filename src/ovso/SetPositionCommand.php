<?php

namespace ovso;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class SetPositionCommand extends Command {

    public function __construct() {
        parent::__construct("setposition", "Setzt die Spawn-Positionen der Arena", "/1vs1 setposition <1|2>");
        $this->setPermission("ovso.1vs1.setposition");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("Dieser Befehl kann nur von Spielern ausgeführt werden.");
            return true;
        }

        if (count($args) !== 1) {
            $sender->sendMessage("Verwendung: /1vs1 setposition <1|2>");
            return false;
        }

        $position = $args[0];

        if ($position !== "1" && $position !== "2") {
            $sender->sendMessage("Ungültige Position. Verwende 1 oder 2.");
            return false;
        }

        $player = $sender;
        $plugin = $sender->getServer()->getPluginManager()->getPlugin("ovso");
        $config = $plugin->getConfig();

        $config->set("arena" . $position, [
            "x" => $player->getPosition()->getX(),
            "y" => $player->getPosition()->getY(),
            "z" => $player->getPosition()->getZ(),
            "world" => $player->getPosition()->getWorld()->getFolderName()
        ]);

        $config->save();

        $sender->sendMessage("Position " . $position . " wurde gesetzt.");

        return true;
    }
}
