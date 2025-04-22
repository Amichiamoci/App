<?php

namespace App\Controller;

use App\Entity\Anagraphical;
use App\Form\AnagraphicalFormType;
use App\Repository\ApiManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


class ProfileController extends AbstractController
{
    #[Route(path: '/profile', name: 'profile')]
    public function index(ApiManager $apiManager): Response
    {
        return $this->render(
            view: 'profile/index.html.twig', 
            parameters: [
                'anagraphicals' => $apiManager->ManagedAnagraphicals(email: $this->getUser()->getUserIdentifier())
            ],
        );
    }

    
    private static function certificate_handling(
        ?UploadedFile $certificate, 
        string $uploadDirectory,
        ApiManager $apiManager,
        int $subscriptionId,
    ): bool
    {
        if ($certificate === null || !$certificate)
        {
            return false;
        }

        $fileName = uniqid(more_entropy: true) . '.' . $certificate->guessExtension();
        try {
            $file = $certificate->move(directory: $uploadDirectory, name: $fileName);
        } catch (FileException $e) {
            return false;
        }

        return false;/*$apiManager->SubscriptionCertificate(
            subscriptionId: $subscriptionId, 
            filePath: $file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename(),
        );*/
    }

    
    #[Route(path: '/profile/get_involved/{id}', name: 'get_involved')]
    public function get_involved(
        ApiManager $apiManager, 
        Request $request,
        int $id,
        #[Autowire('%kernel.project_dir%/public/uploads/certificates')] string $uploadDirectory
    ): Response
    {
        $anagraphical = array_find(
            array: $apiManager->ManagedAnagraphicals(email: $this->getUser()->getUserIdentifier()),
            callback: function (Anagraphical $a) use ($id): bool {
                return $a->Id === $id;
            }
        );
        if ($anagraphical === null)
        {
            // Record not found or not accessible
            throw $this->createAccessDeniedException(message: 'Dati non trovati o non accessibili');
        }

        $form = $this->createForm(
            type: AnagraphicalFormType::class, 
            data: $anagraphical,
            options: [
                'document_types' => $apiManager->DocumentTypes(),
                'churches' => $apiManager->Churches(),
                'anagraphical_only' => false,
            ]
        );
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid())
        {
            /*
            $subscription = $apiManager->(subscription: $form->getData());
            if ($subscription !== null)
            {
                // Subscription ok


                /// @var UploadedFile $certificate
                $certificate = $form->get(name: 'certificate')->getData();
                $certificate_uploaded = self::certificate_handling(
                    certificate: $certificate, 
                    uploadDirectory: $uploadDirectory,
                    apiManager: $apiManager,
                    subscriptionId: $subscription->getId(),
                );
                if ($certificate_uploaded) {
                    $this->addFlash(
                        type: 'success', 
                        message: 'Iscrizione correttamente effettuata con certificato',
                    );
                } else {
                    $this->addFlash(
                        type: 'warn', 
                        message: 'L\'iscrizione è stata effettuata, tuttavia il certificato non è stato consegnato: senza di esso non è possibile partecipare alle attività sportive.',
                    );
                }
            } else {
                $this->addFlash(
                    type: 'error', 
                    message: 'Non è stato possibile effettuare l\'iscrizione. Riprova più tardi',
                );
            }*/
        }

        return $this->render(
            view: 'profile/subscribe.html.twig', 
            parameters: [
                'form' => $form->createView(),
                'title' => 'Iscriviti per il ' . date(format: 'Y')
            ],
        );
    }

    #[Route(path: '/profile/signup/{id}', name: 'signup')]
    public function signup(
        ApiManager $apiManager,
        Request $request,
        ?int $id = null,
    ): Response
    {
        $anagraphical = null;

        if (!empty($id))
        {
            $anagraphical = array_find(
                array: $apiManager->ManagedAnagraphicals(email: $this->getUser()->getUserIdentifier()),
                callback: function (Anagraphical $a) use ($id): bool {
                    return $a->Id === $id;
                }
            );
        }
        $form = $this->createForm(
            type: AnagraphicalFormType::class, 
            data: $anagraphical,
            options: [
                'document_types' => $apiManager->DocumentTypes(),
                'churches' => $apiManager->Churches(),
                'anagraphical_only' => true,
            ]
        );
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $this->addFlash(
                type: 'warn', 
                message: 'Valid',
            );
        }

        return $this->render(
            view: 'profile/subscribe.html.twig', 
            parameters: [
                'form' => $form->createView(),
                'title' => empty($id) ? 'Aggiungi i tuoi dati' : 'Modifica i tuoi dati'
            ],
        );
    }
}