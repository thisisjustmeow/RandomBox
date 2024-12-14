<?php
declare(strict_types=1);

namespace meow\RandomBox;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;

class EventListener implements Listener
{
    public function onItemClick(PlayerInteractEvent $event): void
    {
        $item = $event->getItem();
        $player = $event->getPlayer();
        if(RandomBox::getInstance()->isKeyItem($item)){
            $prize = RandomBox::getInstance()->getRandomPrizeItem($item);
            if(is_null($prize)){
                $player->sendMessage('해당 랜덤박스는 보상이 설정되어있지 않습니다.');
                return;
            }
            //if(!$player->getInventory()->canAddItem($prize)){ -> 원하는 아이템만 쏙쏙 빼먹을 수 있음 ㅠㅜ
            if(count($player->getInventory()->getContents()) === 36){//인벤토리에 어떤 아이템이든 추가할 수 있을 때만
                $player->sendMessage('인벤토리를 비우고 다시 시도해주세요.');
                return;
            }
            $player->getInventory()->removeItem($item->setCount(1));
            $player->getInventory()->addItem($prize);
            $prizeName = $prize->hasCustomName() ? $prize->getCustomName() : $prize->getName();
            $player->sendMessage('랜덤박스에서 ' . $prizeName . ' 아이템을 획득했습니다.');
        }
    }
}