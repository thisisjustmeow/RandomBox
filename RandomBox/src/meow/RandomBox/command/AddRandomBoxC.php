<?php
declare(strict_types=1);

namespace meow\RandomBox\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\player\Player;

use meow\RandomBox\form\RandomBoxF;

class AddRandomBoxC extends Command
{
    public function __construct()
    {
        parent::__construct('랜덤박스', '랜덤박스를 관리합니다.', '/랜덤박스');
        $this->setPermission(DefaultPermissionNames::GROUP_OPERATOR);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(!$sender instanceof Player || !$this->testPermission($sender)) return;
        $sender->sendForm(new RandomBoxF());
    }
}