<?php

declare(strict_types=1);

namespace VsrStudio\AreaMusic;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;
use pocketmine\network\mcpe\protocol\StopSoundPacket;
use pocketmine\player\Player;
use pocketmine\utils\Config;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;

class Main extends PluginBase implements Listener {

    private Config $config;
    private array $musicAreas = [];
    private array $pos1 = [];
    private array $pos2 = [];
    private array $playingMusic = [];

    protected function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);

        // Load music area from config
        $this->config = new Config($this->getDataFolder() . "music_areas.yml", Config::YAML);
        $this->musicAreas = $this->config->get("music_areas", []);
    }

    protected function onDisable(): void {
        $this->config->set("music_areas", $this->musicAreas);
        $this->config->save();
    }

    public function onMove(PlayerMoveEvent $event): void {
        $player = $event->getPlayer();
        $pos = $player->getPosition();
        $inArea = false;

        foreach ($this->musicAreas as $area) {
            $minX = min($area["pos1"]["x"], $area["pos2"]["x"]);
            $maxX = max($area["pos1"]["x"], $area["pos2"]["x"]);
            $minY = min($area["pos1"]["y"], $area["pos2"]["y"]);
            $maxY = max($area["pos1"]["y"], $area["pos2"]["y"]);
            $minZ = min($area["pos1"]["z"], $area["pos2"]["z"]);
            $maxZ = max($area["pos1"]["z"], $area["pos2"]["z"]);

            if (
                $pos->getX() >= $minX && $pos->getX() <= $maxX &&
                $pos->getY() >= $minY && $pos->getY() <= $maxY &&
                $pos->getZ() >= $minZ && $pos->getZ() <= $maxZ
            ) {
                $inArea = true;
                if (!isset($this->playingMusic[$player->getName()])) {
                    $this->playMusic($player);
                }
                return;
            }
        }

        if (!$inArea && isset($this->playingMusic[$player->getName()])) {
            $this->stopMusic($player);
        }
    }

    public function playMusic(Player $player): void {
        $playerName = $player->getName();
        $this->playingMusic[$playerName] = true;

        $packet = PlaySoundPacket::create(
            soundName: "Ramadhan",
            x: $player->getPosition()->getX(),
            y: $player->getPosition()->getY(),
            z: $player->getPosition()->getZ(),
            volume: 1.0,
            pitch: 1.0
        );
        $player->getNetworkSession()->sendDataPacket($packet);

        // Schedule replay after 3 minutes 40 seconds (220 seconds)
        $this->getScheduler()->scheduleDelayedTask(new class($this, $player) extends Task {
            private Main $plugin;
            private Player $player;

            public function __construct(Main $plugin, Player $player) {
                $this->plugin = $plugin;
                $this->player = $player;
            }

            public function onRun(): void {
                if (isset($this->plugin->playingMusic[$this->player->getName()])) {
                    $this->plugin->playMusic($this->player);
                }
            }
        }, 32 * 20);
    }

    private function stopMusic(Player $player): void {
        unset($this->playingMusic[$player->getName()]);

        $packet = StopSoundPacket::create("Ramadhan", false, false);
        $player->getNetworkSession()->sendDataPacket($packet);
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if (!$sender instanceof Player) {
            $sender->sendMessage("Command ini hanya bisa digunakan dalam game!");
            return false;
        }

        switch ($command->getName()) {
            case "musicpos1":
                $this->pos1[$sender->getName()] = $sender->getPosition();
                $sender->sendMessage("Pos1 telah ditetapkan di: " . $this->formatVector($this->pos1[$sender->getName()]));
                return true;

            case "musicpos2":
                $this->pos2[$sender->getName()] = $sender->getPosition();
                $sender->sendMessage("Pos2 telah ditetapkan di: " . $this->formatVector($this->pos2[$sender->getName()]));
                return true;

            case "setmusicarea":
                if (!isset($this->pos1[$sender->getName()]) || !isset($this->pos2[$sender->getName()])) {
                    $sender->sendMessage("Tentukan Pos1 dan Pos2 terlebih dahulu dengan /musicpos1 dan /musicpos2.");
                    return false;
                }

                $this->musicAreas[] = [
                    "pos1" => [
                        "x" => $this->pos1[$sender->getName()]->getX(),
                        "y" => $this->pos1[$sender->getName()]->getY(),
                        "z" => $this->pos1[$sender->getName()]->getZ()
                    ],
                    "pos2" => [
                        "x" => $this->pos2[$sender->getName()]->getX(),
                        "y" => $this->pos2[$sender->getName()]->getY(),
                        "z" => $this->pos2[$sender->getName()]->getZ()
                    ]
                ];

                $this->config->set("music_areas", $this->musicAreas);
                $this->config->save();

                $sender->sendMessage("Area musik telah disimpan dari Pos1 ke Pos2.");
                return true;
        }

        return false;
    }

    private function formatVector(Vector3 $vector): string {
        return "(" . round($vector->getX(), 1) . ", " . round($vector->getY(), 1) . ", " . round($vector->getZ(), 1) . ")";
    }
}
