<?php
namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class FileDownloadController extends AbstractController
{
    private string $uploadDir;

    public function __construct(ParameterBagInterface $params)
    {
        $this->uploadDir = $params->get(name: 'app.upload_dir');
    }

    #[Route(path: '/admin/file/download', name: 'admin_file_download')]
    public function download(Request $request): BinaryFileResponse
    {
        $path = $request->query->get(key: 'file');

        if (!$path) 
            throw $this->createNotFoundException(
                message: 'File non specificato.',
            );

        $realBase = realpath(path: $this->uploadDir);
        $realPath = realpath(path: $this->uploadDir . DIRECTORY_SEPARATOR . $path);

        if (!$realPath || !str_starts_with(haystack: $realPath, needle: $realBase)) 
            throw $this->createAccessDeniedException(
                message: 'C\'è un problema con il file richiesto.'
            );

        return $this->file(file: $realPath);
    }
}