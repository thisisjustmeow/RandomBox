<?php
declare(strict_types=1);

namespace meow\RandomBox;

use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

use meow\ItemManager\ItemManager;
use meow\RandomBox\command\AddPrizeC;
use meow\RandomBox\command\AddRandomBoxC;

class RandomBox extends PluginBase
{
    private Config $database;
    private array $db;

    private static ?self $instance;

    public static function getInstance(): ?self
    {
        return self::$instance;
    }

    protected function onLoad(): void
    {
        self::$instance = $this;
    }

    protected function onEnable(): void
    {
        $this->database = new Config($this->getDataFolder() . 'data.yml', Config::YAML, []);
        $this->db = $this->database->getAll();

        $this->getServer()->getCommandMap()->registerAll('randomBox', [
            new AddRandomBoxC(), new AddPrizeC()
        ]);

        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    public function isExist(string $randomBox): bool
    {
        if(isset($this->db[$randomBox])){
            return true;
        }
        return false;
    }

    public function createRandomBox(string $randomBox, string $keyItem): void
    {
        $this->db[$randomBox] = [
            'keyItem' => $keyItem,
            'items' => []
        ];
    }

    public function removeRandomBox(string $randomBox): void
    {
        if(isset($this->db[$randomBox]))
            unset($this->db[$randomBox]);
    }

    public function getRandomBoxList(): array
    {
        return array_keys($this->db);
    }

    public function getRandomBoxInfo(string $randomBox): array
    {
        return $this->db[$randomBox];
    }

    public function getKeyItem(string $randomBox): Item
    {
        $item = ItemManager::getInstance()->deserializeItem($this->db[$randomBox]['keyItem']);
        $item->getNamedTag()->setString('randomBox', $randomBox);
        $item->setCustomName('§r' . $randomBox);
        $lore = [
            '  §r§a★  보상 목록  ★'
        ];
        $prizes = $this->getPrizeItems($randomBox);
        $chances = $this->getCalculatedChances($randomBox);
        /**
         * @var Item $prize
         */
        foreach ($prizes as $key => $prize) {
            $prizeName = $prize->hasCustomName() ? $prize->getCustomName() : $prize->getName();
            //$lore[] = $this->centerText('§r§7    ' . $prizeName . ' ' . $prize->getCount() . '개 => ' . $chances[$key] . '%', 33);
            $lore[] = '§r§7    ' . $prizeName . '§r§7  ' . $prize->getCount() . '개 => ' . $chances[$key] . '%    ';
        }
        $item->setLore($lore);
        return $item; // TODO: 좀더 보기 편하게 바꾸기
    }


    /*
    function centerText(string $text, int $width): string {
        $specialCharCount = substr_count($text, '§');

        $textLength = strlen($text) - ($specialCharCount * 2);

        if ($textLength >= $width) {
            return substr($text, 0, $width);
        }

        $leftPadding = floor(($width - $textLength) / 2);
        $rightPadding = $width - $textLength - $leftPadding;

        return str_repeat(' ', (int)$leftPadding) . $text . str_repeat(' ', (int)$rightPadding);
    }
    */

    public function isKeyItem(Item $item): bool
    {
        if(!$item->getNamedTag()->getTag('randomBox')) return false;
        return true;
    }

    public function getPrizeItems(string $randomBox): array
    {
        $res = [];
        foreach($this->getRandomBoxInfo($randomBox)['items'] as $info){
            $res[] = ItemManager::getInstance()->deserializeItem($info['item']);
        }
        return $res;
    }

    public function getChances(string $randomBox): array
    {
        $res = [];
        foreach($this->getRandomBoxInfo($randomBox)['items'] as $info){
            $res[] = $info['chance'];
        }
        return $res;
    }

    public function getCalculatedChances(string $randomBox): array
    {
        $res = [];
        $chances = $this->getChances($randomBox);
        $maxChance = array_sum($chances);
        foreach($chances as $key => $chance) {
            $res[] = floor(($chance / $maxChance) * 1000) / 10;
        }
        return $res;
    }

    public function getRandomPrizeItem(string|Item $randomBox): Item|null
    {
        if($randomBox instanceof Item)
            $randomBox = $randomBox->getNamedTag()->getTag('randomBox')->getValue();
        $items = $this->getPrizeItems($randomBox);
        if(empty($items)) return null;
        $chances = $this->getChances($randomBox);
        $chanceSum  = array_sum($chances);
        $r = mt_rand(1, $chanceSum);
        $accumulated = 0;
        $prize = VanillaItems::AIR();
        foreach ($chances as $key => $chance) {
            $accumulated += $chance;
            if ($r <= $accumulated) {
                $prize = $items[$key];
                break;
            }
        }
        return $prize;
    }

    public function addPrize(string $randomBox, string $prizeItem, int $chance): void
    {
        $this->db[$randomBox]['items'][] = [
            'chance' => $chance,
            'item' => $prizeItem
        ];
    }

    protected function onDisable(): void
    {
        $this->database->setAll($this->db);
        $this->database->save();
    }
}