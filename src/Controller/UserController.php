<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api/user')]
class UserController extends AbstractController
{
    private SerializerInterface $serializer;
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    /**
     * @param SerializerInterface $serializer
     * @param UserRepository $userRepository
     * @param EntityManagerInterface $em
     */
    public function __construct
    (SerializerInterface $serializer, UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    #[Route('/', name: 'app_user_all')]
    public function getAll(): JsonResponse
    {

        return new JsonResponse
        (
            $this->serializer->serialize
            ($this->userRepository->findAll(), 'json', ['groups'=>'user']), Response::HTTP_OK, [], true
        );
    }

    #[Route('/new', name: 'app_user_new', methods: ["POST"])]
    public function new(Request $request): JsonResponse
    {

        $content = $request->toArray();

        if (!$content['content'] && !$content['note']){
            return new JsonResponse
            (['message' => 'Vous devez remplir au moins un des deux champs'], Response::HTTP_BAD_REQUEST);
        }

        $user = $this->serializer->deserialize($request->getContent(), User::class, 'json');

        $this->em->persist($user);
        $this->em->flush();

        return new JsonResponse
        ($this->serializer->serialize($user, 'json', ['groups'=>'user']), Response::HTTP_OK, [], true);
    }

    #[Route('/edit/{id}', name: 'app_user_edit', methods: ["PUT"])]
    public function edit(Request $request, User $user = null): JsonResponse
    {
        if ($user instanceof User) {
            $updatedUser = $this->serializer->deserialize($request->getContent(),
                User::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $user]
            );

            $this->em->persist($updatedUser);
            $this->em->flush();
            return new JsonResponse(['message' => 'sucessful edited'],Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'Error user not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/delete/{id}', name: 'app_user_delete', methods: ["DELETE"])]
    public function delete(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if ($user){
            $this->em->remove($user);
            $this->em->flush();

            return new JsonResponse(['message' => "Le user à bien été supprimé"], Response::HTTP_OK);
        }
        return new JsonResponse
        (['message' => "Le user n'existe pas ou à déjà été supprimé"],Response::HTTP_BAD_REQUEST);
    }

    #[Route('/show/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $user = $this->userRepository->find($id);
        if ($user) {
            return new JsonResponse
            ($this->serializer->serialize
            ($user, 'json', ['groups' => 'user']), Response::HTTP_OK, [], true);
        }

        return new JsonResponse(["message" => "User not found"], Response::HTTP_NOT_FOUND, []);
    }
}
