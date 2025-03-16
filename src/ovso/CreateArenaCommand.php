<?php

namespace ovso;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\world\World;

class CreateArenaCommand extends Command {

    private array $creationData = [];

    public function __construct() {
        parent::__construct("create", "Erstellt eine neue Arena", "/1vs1 create <arenaName>");
        $this->setPermission("ovso.1vs1.create");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("Dieser Befehl kann nur von Spielern ausgeführt werden.");
            return true;
        }

        if (count($args) !== 1) {
            $sender->sendMessage("Verwendung: /1vs1 create <arenaName>");
            return false;
        }

        $arenaName = $args[0];
        $player = $sender;
        $plugin = $player->getServer()->getPluginManager()->getPlugin("ovso");
        $config = $plugin->getConfig();

        if ($config->get("arenas." . $arenaName) !== null) {
            $player->sendMessage("Eine Arena mit diesem Namen existiert bereits.");
            return true;
        }

        $this->creationData[$player->getName()] = [
            "arenaName" => $arenaName,
            "world" => $player->getWorld()->getFolderName(),
            "spawn1" => null,
            "spawn2" => null
        ];

        $player->sendMessage("Arena-Erstellung gestartet. Verwende /1vs1 setspawn 1, um Spawn 1 zu setzen.");

        return true;
    }

    public function setSpawn(Player $player, int $spawnNumber): void {
        if (!isset($this->creationData[$player->getName()])) {
            $player->sendMessage("Du erstellst gerade keine Arena.");
            return;
        }

        $data = &$this->creationData[$player->getName()];
        $data["spawn" . $spawnNumber] = [
            "x" => $player->getPosition()->getX(),
            "y" => $player->getPosition()->getY(),
            "z" => $player->getPosition()->getZ()
        ];

        $player->sendMessage("Spawn " . $spawnNumber . " wurde gesetzt. Verwende /1vs1 setspawn " . ($spawnNumber === 1 ? 2 : 1) . ", um den anderen Spawn zu setzen.");

        if ($data["spawn1"] !== null && $data["spawn2"] !== null) {
            $this->saveArena($player);
        }
    }

    private function saveArena(Player $player): void {
        $data = $this->creationData[$player->getName()];
        $plugin = $player->getServer()->getPluginManager()->getPlugin("ovso");
        $config = $plugin->getConfig();

        $config->set("arenas." . $data["arenaName"], [
            "world" => $data["world"],
            "spawn1" => $data["spawn1"],
            "spawn2" => $data["spawn2"]
        ]);

        $config->save();
        unset($this->creationData[$player->getName()]);

        $player->sendMessage("Arena '" . $data["arenaName"] . "' wurde erstellt.");
    }
}
