<?php

namespace App\Controller\Admin;

use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminDashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Finder\Finder;

#[AdminDashboard(routePath: '/admin', routeName: 'admin')]
class DashboardController extends AbstractDashboardController
{
    public function index(): Response
    {
        return $this->render(view: 'admin/index.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle(title: 'App Amichiamoci')
            ->setFaviconPath(path: 'assets/logos/favicon.png');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToRoute(
            label: 'App', 
            icon: 'fa-solid fa-mobile',
            routeName: 'home',
        );

        yield MenuItem::section();
        yield MenuItem::linkToDashboard(
            label: 'Dashboard', 
            icon: 'fa fa-home',
        );

        yield MenuItem::section(label: 'Utenti', icon: 'fa-regular fa-user');
        yield MenuItem::linkToRoute(
            label: 'Tutti gli utenti', 
            icon: 'fa fa-list-ol', 
            routeName: 'admin_user_index',
        );
        yield MenuItem::linkToRoute(
            label: 'Password', 
            icon: 'fa fa-unlock-alt', 
            routeName: 'admin_reset_password_request_index',
        );

        yield MenuItem::section(label: 'Amministrazione', icon: 'fa-solid fa-gears');
        yield MenuItem::linkToRoute(
            label: 'File Browser', 
            icon: 'fa fa-folder', 
            routeName: 'admin_file_browser',
        );
    }

    #[Route(path: '/admin/files', name: 'admin_file_browser')]
    public function fileBrowser(): Response
    {
        $uploadDir = $this->getParameter(name: 'app.upload_dir');

        $finder = new Finder();
        $finder->in(dirs: $uploadDir)->depth(levels: '== 0');

        $files = [];

        foreach ($finder as $file) {
            $mime = mime_content_type($file->getRealPath());

            $type = match (true) {
                $file->isDir() => 'directory',
                str_starts_with($mime, 'image/') => 'image',
                $mime === 'application/pdf' => 'pdf',
                str_contains($mime, 'word') => 'word',
                default => 'other',
            };

            $files[] = [
                'name' => $file->getFilename(),
                'size' => self::formatBytes(bytes: $file->getSize()),
                'modifiedAt' => (new \DateTimeImmutable())->setTimestamp(timestamp: $file->getMTime()),
                'type' => $type,
            ];
        }

        return $this->render(view: 'admin/file_browser.html.twig', parameters: [
            'files' => $files,
        ]);
    }

    private static function formatBytes(int $bytes): string
    {
        if ($bytes === 0) 
            return '0 B';

        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $power = (int) floor(log($bytes, 1024));
        $power = min($power, count($units) - 1);

        $value = $bytes / (1024 ** $power);

        return number_format($value, $power === 0 ? 0 : 2) . ' ' . $units[$power];
    }
}
