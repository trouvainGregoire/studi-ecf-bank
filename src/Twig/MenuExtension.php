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
            'client_delete_account', 'banker_dashboard', 'banker_pending_accounts', 'banker_pending_recipients',
            'banker_pending_removal_accounts'];

        return in_array($path, $menuRequiredPaths);
    }
}