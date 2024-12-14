<?php
declare(strict_types=1);

namespace meow\RandomBox\form;

use pocketmine\form\Form;
use pocketmine\player\Player;

class RandomBoxF implements Form
{
    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'form',
            'title' => '랜덤박스',
            'content' => '작업을 선택하세요.',
            'buttons' => [
                [
                    'text' => '랜덤박스 생성'
                ],
                [
                    'text' => '랜덤박스 제거'
                ],
                [
                    'text' => '랜덤박스 정보확인'
                ],
                [
                    'text' => '랜덤박스 지급받기'
                ]
            ]
        ];
    }

    public function handleResponse(Player $player, $data): void
    {
        if($data === null) return;
        if($data === 0){
            $item = $player->getInventory()->getItemInHand();
            if($item->isNull()){
                $player->sendMessage('아이템을 들고 다시 시도해주세요!');
                return;
            }
            $player->sendForm(new AddRandomBoxF());
        }else{
            $player->sendForm(new RandomBoxListF($data));
        }
    }
}