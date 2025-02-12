<?php

declare(strict_types=1);

namespace terpz710\oregen;

use pocketmine\plugin\PluginBase;

final class OreGen extends PluginBase {

    protected function onEnable() : void{
        $this->saveDefaultConfig();
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this), $this);
    }
}