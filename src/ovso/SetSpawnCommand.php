<?php

namespace ovso;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class SetSpawnCommand extends Command {

    public function __construct() {
        parent::__construct("setspawn", "Setzt einen Spawn für die Arena-Erstellung", "/1vs1 setspawn <1|2>");
        $this->setPermission("ovso.1vs1.setspawn");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("Dieser Befehl kann nur von Spielern ausgeführt werden.");
            return true;
        }

        if (count($args) !== 1) {
            $sender->sendMessage("Verwendung: /1vs1 setspawn <1|2>");
            return false;
        }

        $spawnNumber = (int) $args[0];

        if ($spawnNumber !== 1 && $spawnNumber !== 2) {
            $sender->sendMessage("Ungültige Spawn-Nummer. Verwende 1 oder 2.");
            return false;
        }

        $player = $sender;
        $plugin = $player->getServer()->getPluginManager()->getPlugin("ovso");
        $createCommand = $plugin->getServer()->getCommandMap()->getCommand("1vs1")->getSubCommand("create");

        if ($createCommand instanceof CreateArenaCommand) {
            $createCommand->setSpawn($player, $spawnNumber);
        }

        return true;
    }
}
