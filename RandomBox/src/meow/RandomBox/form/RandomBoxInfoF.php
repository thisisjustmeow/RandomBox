<?php
declare(strict_types=1);

namespace meow\RandomBox\form;

use pocketmine\form\Form;
use pocketmine\item\Item;
use pocketmine\player\Player;

use meow\RandomBox\RandomBox;

class RandomBoxInfoF implements Form
{
    public function __construct(private string $randomBox)
    {
    }

    public function jsonSerialize(): mixed
    {
        $prizeItems= RandomBox::getInstance()->getPrizeItems($this->randomBox);
        $chances = RandomBox::getInstance()->getCalculatedChances($this->randomBox);
        $text = '§f=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=' . "\n" .
            '보상 아이템 목록' . "\n\n§7";
        /**
         * @var Item $item
         */
        foreach($prizeItems as $key => $item){
            $itemName = $item->hasCustomName() ? $item->getCustomName() : $item->getName();
            $text .= $itemName . '§r§7 ' . $item->getCount() . '개 -> 확률: ' . $chances[$key] . '%%' .  "\n§7";
        }
        $text .= '§f=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=';
        return [
            'type' => 'custom_form',
            'title' => '§l§b|| §r§f' . $this->randomBox . ' 랜덤박스 정보 §l§b||',
            'content' => [
                [
                    'type' => 'label',
                    'text' => $text
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        //
    }
}