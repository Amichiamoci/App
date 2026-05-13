<?php

namespace App\Controller;

use App\Entity\Anagraphical;
use App\Form\AnagraphicalFormType;
use App\Repository\ApiManager;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;


class ProfileController extends AbstractController
{
    private string $uploadDirectory;

    public function __construct(ParameterBagInterface $params)
    {
        $this->uploadDirectory = $params->get(name: 'app.upload_dir');
    }


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


    private function anagraphical_handling(
        ApiManager $apiManager,

        Anagraphical $anagraphical,
        ?Anagraphical $original = null,
    ): ?Anagraphical {
        if ($original === null && !$anagraphical->Document->hasFile())
        {
            // We are creating the data, but a document was not provided

            throw new \InvalidArgumentException(
                message: 'È necessario fornire un proprio documento quando si creano i propri dati.',
            );
        }
        $document = $anagraphical->Document->getFile();

        $file_name = null;
        if ($document !== null)
        {
            $ext = '.' . $document->guessExtension();
            $file_name = 'document-' . uniqid(more_entropy: true) . $ext;
            try {
                $document->move(directory: $this->uploadDirectory, name: $file_name);
            } catch (FileException) {
                return null;
            }
        }

        if ($original === null || $anagraphical !== $original || !empty($file_name))
        {
            // We are creating the data or making changes to existing
            $_s = $anagraphical->Subscription;
            $anagraphical = $apiManager->HandleAnagraphical(
                anagraphical: $anagraphical,
                userId: $this->getUser()->getUserIdentifier(),
                documentFile: $file_name,
            );

            if ($anagraphical === null)
            {
                // The update went wrong
                return null;
            }
            $anagraphical->Subscription = $_s;
        }

        if (!$anagraphical->hasSubscription())
            return $anagraphical;

        $file_name = null;
        if ($anagraphical->Subscription->hasCertificate())
        {
            $ext = '.' . $anagraphical->Subscription->Certificate->guessExtension();
            $file_name = 'certificate-' . uniqid(more_entropy: true) . $ext;
            try {
                $anagraphical->Subscription->Certificate->move(
                    directory: $this->uploadDirectory, 
                    name: $file_name
                );
            } catch (FileException) {

                $this->addFlash(
                    type: 'warn', 
                    message: 'È avvenuto un errore durante il caricamento del certificato. Si prega di riprovare più tardi',
                );
                $anagraphical->Subscription->Certificate = null;
            }
        }
        $anagraphical->Subscription = $apiManager->HandleSubscription(
            anagraphical: $anagraphical->Id, 
            userId: $this->getUser()->getUserIdentifier(),
            subscription: $anagraphical->Subscription,
            certificate: $file_name,
        );
        if (!$anagraphical->hasSubscription())
        {
            // Subscription hadling went wrong

            if ($original->hasSubscription())
            {
                $this->addFlash(
                    type: 'error', 
                    message: 'Non è stato possibile modificare l\'iscrizione. Riprova più tardi',
                );
            } else {
                $this->addFlash(
                    type: 'success', 
                    message: 'Non è stato possibile creare l\'iscrizione. Riprova più tardi',
                );
            }
            return $anagraphical;
        }
        if (!$original->hasSubscription() && !$anagraphical->Subscription->hasCertificate())
        {
            $this->addFlash(
                type: 'warn', 
                message: 'L\'iscrizione è stata effettuata, tuttavia un certificato non è stato presentato. Non sarà possibile scendere in campo fino ache il certificato non sarà caricato',
            );
        }

        if ($original->hasSubscription())
        {
            $this->addFlash(
                type: 'success', 
                message: 'Iscrizione modificata correttamente',
            );
        } else {
            $this->addFlash(
                type: 'success', 
                message: 'Iscrizione modificata correttamente',
            );
        }

        return $anagraphical;
    }
    
    #[Route(path: '/profile/get_involved/{id}', name: 'get_involved')]
    public function get_involved(
        ApiManager $apiManager, 
        Request $request,
        int $id
    ): Response
    {
        $anagraphical = array_find(
            array: $apiManager->ManagedAnagraphicals(email: $this->getUser()->getUserIdentifier()),
            callback: function (Anagraphical $a) use ($id): bool {
                return $a->Id === $id;
            },
        );
        if (!isset($anagraphical)) // Do not change with "=== null"
        {
            // Record not found or not accessible
            throw $this->createAccessDeniedException(message: 'Dati non trovati o non accessibili');
        }

        $original_anagraphical = clone $anagraphical;
        $form = $this->createForm(
            type: AnagraphicalFormType::class, 
            data: $anagraphical,
            options: [
                'document_types' => $apiManager->DocumentTypes(),
                'churches' => $apiManager->Churches(),
                'anagraphical_only' => false,
                'action' => $this->generateUrl(
                    route: 'get_involved', 
                    parameters: ['id' => $id],
                ),
            ]
        );
        $form->handleRequest(request: $request);

        $status_code = $form->isSubmitted() && !$form->isValid() ? 422 : 200;

        if ($form->isSubmitted() && $form->isValid())
        {
            /**
             * @var Anagraphical
             */
            $anagraphical = $form->getData();
            if ($anagraphical->Id !== $id)
            {
                // Tried to change the target id
                throw $this->createAccessDeniedException(message: 'Dati non trovati o non accessibili');
            }

            // Load fields not visible to user
            $anagraphical->reverseTaxCode();
            $anagraphical->Email = $this->getUser()->getUserIdentifier();

            // Stuff really happens here
            $anagraphical = $this->anagraphical_handling(
                apiManager: $apiManager,
                
                anagraphical: $anagraphical,
                original: $original_anagraphical,
            );
            if ($anagraphical !== null)
                return $this->redirectToRoute(route: 'profile');

            $this->addFlash(
                type: 'error', 
                message: 'Non è stato possibile effettuare l\'iscrizione. Riprova più tardi',
            );
            $status_code = 500;
        }

        return $this->render(
            view: 'profile/subscribe.html.twig', 
            parameters: [
                'form' => $form,
                'title' => 'Iscriviti per il ' . date(format: 'Y'),
            ],
            response: new Response(content: null, status: $status_code),
        );
    }

    #[Route(path: '/profile/signup/{id}', name: 'signup')]
    public function signup(
        ApiManager $apiManager,
        Request $request,

        ?int $id = null,
    ): Response
    {
        $anagraphical = array_find(
            array: $apiManager->ManagedAnagraphicals(email: $this->getUser()->getUserIdentifier()),
            callback: function (Anagraphical $a) use ($id): bool {
                return $a->Id === $id;
            },
        );

        $original_anagraphical = $anagraphical !== null ? (clone $anagraphical) : null;
        $form = $this->createForm(
            type: AnagraphicalFormType::class, 
            data: $anagraphical,
            options: [
                'document_types' => $apiManager->DocumentTypes(),
                'churches' => $apiManager->Churches(),
                'anagraphical_only' => true,
                'force_document_upload' => $anagraphical === null, // If we are creating the data we are gonna need at least one file
                'action' => $this->generateUrl(
                    route: 'signup', 
                    parameters: ['id' => $id],
                ),
            ]
        );
        $form->handleRequest(request: $request);

        $status_code = $form->isSubmitted() && !$form->isValid() ? 422 : 200;

        if ($form->isSubmitted() && $form->isValid())
        {
            /**
             * @var Anagraphical
             */
            $anagraphical = $form->getData();
            if (!empty($id) && $anagraphical->Id !== $id)
            {
                // Tried to change the target id
                throw $this->createAccessDeniedException(message: 'Dati non trovati o non accessibili');
            }

            // Load fields not visible to user
            $anagraphical->reverseTaxCode();
            $anagraphical->Email = $this->getUser()->getUserIdentifier();

            // Stuff really happens here
            $anagraphical = $this->anagraphical_handling(
                apiManager: $apiManager,
                
                anagraphical: $anagraphical,
                original: $original_anagraphical,
            );
            if ($anagraphical !== null)
            {
                return $this->redirectToRoute(route: 'profile');
            }

            $this->addFlash(
                type: 'error', 
                message: 'Non è stato possibile caricare i dati anagrafici. Si prega di riprovare più tardi. ',
            );
            $this->addFlash(
                type: 'warn', 
                message: 
                'Se hai già partecipato in passato, e non vedi i tuoi dati, ' . 
                'è possibile che siano già presenti ma associati ad una mail diversa da quella da quella che stai usando. ' .
                'Nel caso non ti ricordassi quale email potresti aver usato alla tua prima iscrizione, ' . 
                'prova a contattare uno staffista: sarà in grado di dirti l\'email corretta e, ' . 
                'se serve, spostare i dati dal vecchio account a questo.',
            );
            $status_code = 500;
        }

        return $this->render(
            view: 'profile/subscribe.html.twig', 
            parameters: [
                'form' => $form,
                'title' => empty($id) ? 'Aggiungi i tuoi dati' : 'Modifica i tuoi dati'
            ],
            response: new Response(content: null, status: $status_code),
        );
    }
}