<?php


namespace App\Twig;


use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class MenuExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('needMenu', [$this, 'needMenu'])
        ];
    }

    public function needMenu(string $path)
    {
        $menuRequiredPaths = ['client_dashboard', 'client_show_recipients',
            'client_create_transaction', 'client_create_recipient',
            'client_delete_account'];

        return in_array($path, $menuRequiredPaths);
    }
}