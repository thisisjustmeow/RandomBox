<?php
declare(strict_types=1);

namespace meow\RandomBox\form;

use pocketmine\form\Form;
use pocketmine\player\Player;

use meow\RandomBox\RandomBox;

class RemoveRandomBoxF implements Form
{
    public function __construct(private string $randomBox)
    {
    }

    public function jsonSerialize(): mixed
    {
        return [
            'type' => 'modal',
            'title' => '랜덤박스',
            'content' => '정말 ' . $this->randomBox . ' 랜덤박스를 제거하시겠습니까?' . "\n\n" .
                '제거한 이후에는 해당 랜던박스를 이용할 수 없습니다.',
            'button1' => '네',
            'button2' => '아니요'
        ];
    }
    //TODO: make command class that adds Prize Item to specific randombox

    public function handleResponse(Player $player, $data): void
    {
        if ($data) {
            RandomBox::getInstance()->removeRandomBox($this->randomBox);
            $player->sendMessage($this->randomBox . ' 랜덤박스가 영구적으로 제거되었습니다.');
            $player->sendMessage('만약 추후에 "' . $this->randomBox . '" 라는 이름을 가진 랜덤박스를 만들면 기존의 아이템도 작동할 수 있으니, 유의하시길 바랍니다.');
        } else { // '아니오'를 눌렀을 때
            $player->sendMessage('랜덤박스 제거가 취소되었습니다.');
        }
    }
}