<?php

namespace Bavfalcon9\ModSpectate\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat as TF;
use pocketmine\Player;
use pocketmine\Server;

class spectate extends Command {
    private $pl;

    public function __construct($pl) {
        parent::__construct("spectate");
        $this->pl = $pl;
        $this->description = "Spectate a user.";
        $this->usageMessage = "/spectate <player>";
        $this->setPermission("spectate.command");
    }
    
    public function execute(CommandSender $sender, string $commandLabel, array $args) : bool {
       if (!$sender->hasPermission('spectate.command') && !$sender->isOp()) {
           $sender->sendMessage(TF::RED . "You do not have permission to use this command.");
           return false;
       }

       if (isset($this->pl->spectators[$sender->getName()])) {
           $person = $this->pl->spectators[$sender->getName()]['player'];
           $location = $this->pl->spectators[$sender->getName()]['location'];
           $gamemode = $this->pl->spectators[$sender->getName()]['gm'];

           unset($this->pl->spectators[$sender->getName()]);
           $sender->sendMessage(TF::GREEN . "You are no longer spectating $person.");
           $sender->teleport($location);
           $sender->setGamemode($gamemode);
           return true;
       }

       if (!isset($args[0])) {
           $sender->sendMessage(TF::RED . "You need to provided a player to spectate.");
           return false;
       }

       $player = Server::getInstance()->getPlayer($args[0]);

       if ($player === NULL) {
           $sender->sendMessage(TF::RED . "Invalid player provided.");
           return true;
       }

       $this->pl->spectators[$sender->getName()] = [
           'gm' => $sender->getGamemode(),
           'location' => $sender->getPosition(),
           'player' => $player->getName()
       ];

       $person = $this->pl->spectators[$sender->getName()]['player'];
       $sender->setGamemode(3);
       $sender->teleport($player->getPosition());
       $sender->sendMessage(TF::GREEN . "You are now spectating $person.");

       return true;
    }
}
