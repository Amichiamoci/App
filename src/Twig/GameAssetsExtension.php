<?php

namespace App\Twig;

use Symfony\Component\Asset\Packages;
use Symfony\Component\Finder\Finder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class GameAssetsExtension extends AbstractExtension
{
    private Packages $packages;
    private string $projectDir;

    public function __construct(Packages $packages, string $projectDir)
    {
        $this->packages = $packages;
        $this->projectDir = $projectDir;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('game_asset_list',     [$this, 'getGameAssetList']),
            new TwigFunction('game_assets',         [$this, 'getGameAssets']),
            new TwigFunction('game_years_to_logos', [$this, 'getYearsAndLogos']),
        ];
    }

    public function getYearsAndLogos(): array
    {
        $logosPath = $this->projectDir . '/assets/icons/game/logos';
        if (!is_dir($logosPath)) {
            return [];
        }

        $finder = Finder::create()
            ->files()
            ->in($logosPath)
            ->ignoreDotFiles(true)
            ->sortByName();

        $logos = [];
        foreach ($finder as $file) {
            $relativePath = 'icons/game/logos/' . ltrim(str_replace($logosPath, '', $file->getRealPath()), '/\\');
            $relativePath = str_replace('\\', '/', $relativePath);
            $fileName = $file->getFilename();
            $year = pathinfo($fileName, PATHINFO_FILENAME);

            $logos[$year] = [
                'file' => 'logos/' . $fileName,
                'asset' => $this->packages->getUrl($relativePath),
            ];
        }

        return $logos;
    }

    private function genericAssets(): array
    {
        $gamePath = $this->projectDir . '/assets/icons/game';
        if (!is_dir($gamePath)) {
            return [];
        }

        $finder = Finder::create()
            ->files()
            ->in($gamePath)
            ->depth('== 0')
            ->ignoreDotFiles(true)
            ->sortByName();

        $assets = [];
        foreach ($finder as $file) {
            $fileName = $file->getFilename();
            $relativePath = 'icons/game/' . $fileName;
            $assets[$fileName] = $this->packages->getUrl($relativePath);
        }

        return $assets;
    }

    public function getGameAssets(): array
    {
        $assets = $this->genericAssets();

        $yearsAndLogos = $this->getYearsAndLogos();
        foreach ($yearsAndLogos as $year => $pair)
            $assets[$pair['file']] = $pair['asset'];

        return $assets;
    }

    public function getGameAssetList(): array
    {
        return array_values($this->getGameAssets());
    }
}
