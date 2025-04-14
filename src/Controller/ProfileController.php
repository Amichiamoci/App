<?php

namespace App\Controller;

use App\Entity\Subscription;
use App\Form\AnagraphicalFormType;
use App\Form\SubscribeFormType;
use App\Repository\ApiManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

use function PHPUnit\Framework\throwException;

class ProfileController extends AbstractController
{
    #[Route(path: '/profile', name: 'profile')]
    public function index(ApiManager $apiManager): Response
    {
        return $this->render(
            view: 'profile/index.html.twig', 
            parameters: [
                'anagraphicals' => [],
                /*'anagraphicals' => $apiManager->ManagedAnagraphicals(email: $this->getUser()->getUserIdentifier())*/
            ],
        );
    }

    /*
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

        return $apiManager->SubscriptionCertificate(
            subscriptionId: $subscriptionId, 
            filePath: $file->getPath() . DIRECTORY_SEPARATOR . $file->getFilename(),
        );
    }*/

    /*
    #[Route(path: '/profile/get_involved/{id}', name: 'get_involved')]
    public function get_involved(
        ApiManager $apiManager, 
        Request $request,
        int $id,
        #[Autowire('%kernel.project_dir%/public/uploads/certificates')] string $uploadDirectory
    ): Response
    {
        $subscription = new Subscription(anagraphical: $id, shirt: '', church: 0);
        $form = $this->createForm(
            type: SubscribeFormType::class, 
            data: $subscription,
            options: ['churches' => $apiManager->Churches()]
        );
        $form->handleRequest(request: $request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $subscription = $apiManager->Subscription(subscription: $form->getData());
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
            }
        }

        return $this->render(
            view: 'profile/subscribe.html.twig', 
            parameters: [
                'form' => $form,
            ],
        );
    }
    */

    #[Route(path: '/profile/signup', name: 'signup')]
    public function signup(
        ApiManager $apiManager,
        Request $request,
    ): Response
    {
        $form = $this->createForm(
            type: AnagraphicalFormType::class, 
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
            ],
        );
    }
}