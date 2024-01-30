<?php

namespace xtcy\spawnerv1\listeners;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\ItemBlock;
use pocketmine\item\Pickaxe;
use pocketmine\item\StringToItemParser;
use xtcy\spawnerv1\block\MonsterSpawner;
use pocketmine\block\MonsterSpawner as PMMonsterSpawner;
use xtcy\spawnerv1\item\items;

class BlockListener implements Listener
{

    public function onPlace(BlockPlaceEvent $event) {
        if($event->isCancelled())return;

        $item = $event->getItem();

        if(!$item instanceof ItemBlock) return;

        $block = $item->getBlock();
        if(
            !$block instanceof MonsterSpawner or
            $block instanceof PMMonsterSpawner
        ){
            return;
        }
        $transaction = $event->getTransaction();

        foreach($transaction->getBlocks() as [$x, $y, $z, $blocks]){
            $transaction->addBlock($blocks->getPosition(), items::MONSTER_SPAWNER()->setLegacyEntityId(items::getSpawnerEntityId($item)));
        }
    }

    public function onSpawnerBreak(BlockBreakEvent $event){
        if($event->isCancelled()){
            return;
        }
        $item = $event->getItem();
        $tile = ($position = $event->getBlock()->getPosition())->getWorld()->getTile($position);
        if(
            !$tile instanceof MonsterSpawner or
            !$item instanceof Pickaxe or
            !$item->hasEnchantment(VanillaEnchantments::SILK_TOUCH())
        ){
            return;
        }
        $event->setDrops([StringToItemParser::getInstance()->parse('52:'. $tile->getLegacyEntityId()) ?? items::MONSTER_SPAWNER()->asItem()]);
    }
}