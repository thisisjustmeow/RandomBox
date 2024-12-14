<?php
declare(strict_types=1);

namespace meow\RandomBox\form;

use pocketmine\form\Form;
use pocketmine\player\Player;

use meow\ItemManager\ItemManager;
use meow\RandomBox\RandomBox;

class AddRandomBoxF implements Form
{
    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'custom_form',
            'title' => '랜덤박스',
            'content' => [
                [
                    'type' => 'input',
                    'text' => '랜덤박스 이름을 입력해주세요.' . "\n\n" . '랜덤박스 아이템은 현재 손에 든 아이템으로 지정됩니다.'
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        if(!isset($data[0])){
            $player->sendMessage('이름을 입력해주세요.');
            return;
        }
        if(RandomBox::getInstance()->isExist((string) $data[0])){
            $player->sendMessage('이미 존재하는 랜덤박스입니다.');
        }
        $item = ItemManager::getInstance()->serializeItem($player->getInventory()->getItemInHand()->setCount(1));
        RandomBox::getInstance()->createRandomBox((string) $data[0], $item);
        $player->sendMessage($data[0] . '§r§f 랜덤박스를 생성했습니다.');
    }
}