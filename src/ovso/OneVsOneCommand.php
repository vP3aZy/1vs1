<?php

namespace ovso;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class OneVsOneCommand extends Command {

    private array $subCommands = [];

    public function __construct() {
        parent::__construct("1vs1", "Hauptbefehl für 1vs1", "/1vs1 <create|setposition|resetposition|setspawn>");
        $this->setPermission("ovso.1vs1");
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): bool {
        if (isset($args[0]) && isset($this->subCommands[$args[0]])) {
            $subCommand = $this->subCommands[$args[0]];
            // Entferne den Unterbefehlsnamen aus den Argumenten
            array_shift($args);
            return $subCommand->execute($sender, $commandLabel, $args);
        }

        $sender->sendMessage($this->getUsage());
        return true;
    }

    public function registerSubCommand(Command $subCommand): void {
        $this->subCommands[$subCommand->getName()] = $subCommand;
    }
}
