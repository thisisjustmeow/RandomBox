<?php
declare(strict_types=1);

namespace meow\RandomBox\form;

use pocketmine\form\Form;
use pocketmine\item\Item;
use pocketmine\player\Player;

use meow\ItemManager\ItemManager;
use meow\RandomBox\RandomBox;

class AddRandomBoxPrizeF implements Form
{
    public function __construct(private string $randomBox, private Item $item)
    {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'custom_form',
            'title' => '랜덤박스 보상추가',
            'content' => [
                [
                    'type' => 'slider',
                    'text' => '추가할 수량을 선택해주세요.',
                    'min' => 1,
                    'max' => $this->item->getMaxStackSize(),
                    'default' => 1
                ],
                [
                    'type' => 'input',
                    'text' => '해당 보상이 선택될 가중치를 입력해주세요. '
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        $item = $this->item;
        if(!isset($data[1]) || !is_numeric($data[1]) || intval($data[1]) < 1){
            $player->sendMessage('가중치는 1보다 커야 합니다.');
            return;
        }
        $item->setCount(intval($data[0]));
        RandomBox::getInstance()->addPrize($this->randomBox, ItemManager::getInstance()->serializeItem($item), intval($data[1]));
        $player->sendMessage($item->getCustomName() . '§r§f 아이템 ' . $item->getCount() . '개가 ' . $this->randomBox . '§r§f 랜덤박스에 추가되었습니다.');
    }
}