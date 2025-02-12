<?php

declare(strict_types=1);

namespace terpz710\oregen;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockFormEvent;

use pocketmine\block\VanillaBlocks;
use pocketmine\block\BlockTypeIds;

class EventListener implements Listener {

    protected array $oreWeights;

    protected int $oreSpawnChance;

    public function __construct(protected OreGen $plugin) {
        $this->plugin = $plugin;
        $config = $this->plugin->getConfig();

        $this->oreSpawnChance = max(0, min(100, (int) $config->get("ore-spawn-chance")));

        $this->oreWeights = $config->get("ore-weights", []);
    }

    public function blockForm(BlockFormEvent $event) : void{
        $block = $event->getBlock();
        $newState = $event->getNewState();
        $world = $block->getPosition()->getWorld();

        if ($newState->getTypeId() === BlockTypeIds::COBBLESTONE) {
            if (mt_rand(1, 100) <= $this->oreSpawnChance) {
                $event->cancel();

                $world->setBlockAt(
                    $block->getPosition()->getX(),
                    $block->getPosition()->getY(),
                    $block->getPosition()->getZ(),
                    $this->getRandomOre()
                );
            }
        }
    }

    private function getRandomOre() : object{
        $ores = [
            "coal" => VanillaBlocks::COAL_ORE(),
            "iron" => VanillaBlocks::IRON_ORE(),
            "copper" => VanillaBlocks::COPPER_ORE(),
            "redstone" => VanillaBlocks::REDSTONE_ORE(),
            "gold" => VanillaBlocks::GOLD_ORE(),
            "diamond" => VanillaBlocks::DIAMOND_ORE(),
            "emerald" => VanillaBlocks::EMERALD_ORE(),
        ];

        $totalWeight = array_sum($this->oreWeights);
        $rand = mt_rand(1, $totalWeight);
        $current = 0;

        foreach ($this->oreWeights as $oreName => $weight) {
            $current += $weight;
            if ($rand <= $current) {
                return $ores[$oreName];
            }
        }

        return VanillaBlocks::COBBLESTONE();
    }
}
