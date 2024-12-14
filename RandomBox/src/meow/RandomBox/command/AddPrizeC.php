<?php
declare(strict_types=1);

namespace meow\RandomBox\command;

use meow\RandomBox\form\RandomBoxListF;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\permission\DefaultPermissionNames;
use pocketmine\player\Player;

use meow\ItemManager\ItemManager;
use meow\RandomBox\RandomBox;

class AddPrizeC extends Command
{
    public function __construct()
    {
        parent::__construct('랜덤박스보상추가', '/랜덤박스보상추가', '/랜덤박스보상추가');
        $this->setPermission(DefaultPermissionNames::GROUP_OPERATOR);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if(!$sender instanceof Player || !$this->testPermission($sender)) return;
        $item = $sender->getInventory()->getItemInHand();
        if($item->isNull()){
            $sender->sendMessage('공기는 보상 아이템으로 추가할 수 없습니다.');
            return;
        }
        $sender->sendForm(new RandomBoxListF(0, $item));
    }
}