<?php
declare(strict_types=1);

namespace meow\RandomBox\form;

use pocketmine\form\Form;
use pocketmine\item\Item;
use pocketmine\player\Player;

use meow\RandomBox\RandomBox;

class RandomBoxListF implements Form
{
    public function __construct(private int $type, private ?Item $item = null)
    {
    }

    public function jsonSerialize(): mixed
    {
        $buttons = [];
        foreach(RandomBox::getInstance()->getRandomBoxList() as $randomBox){
            $buttons[] = [
                'text' => '§r§7' . $randomBox
            ];
        }
        return [
            'type' => 'form',
            'title' => '랜덤박스',
            'content' => '',
            'buttons' => $buttons
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        $randomBox = RandomBox::getInstance()->getRandomBoxList()[$data];
        if(!RandomBox::getInstance()->isExist($randomBox)){
            $player->sendMessage('해당 랜덤박스는 존재하지 않습니다.');
            return;
        }
        if($this->type === 0){
            $player->sendForm(new AddRandomBoxPrizeF($randomBox, $this->item));// add prize
        }elseif($this->type === 1){
            $player->sendForm(new RemoveRandomBoxF($randomBox));// remove
        }elseif($this->type === 2){
            $player->sendForm(new RandomBoxInfoF($randomBox));// info
        }elseif($this->type === 3){
            $item = RandomBox::getInstance()->getKeyItem($randomBox);
            $item->setCount($item->getMaxStackSize());
            $player->getInventory()->addItem($item);
            $player->sendMessage('지급되었습니다.');
        }
    }
}