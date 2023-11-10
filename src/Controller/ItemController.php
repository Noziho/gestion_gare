<?php

namespace App\Controller;

use App\Entity\Item;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class ItemController extends AbstractController
{
    private SerializerInterface $serializer;
    private ItemRepository $itemRepository;
    private EntityManagerInterface $em;

    /**
     * @param SerializerInterface $serializer
     * @param ItemRepository $itemRepository
     * @param EntityManagerInterface $em
     */
    public function __construct
    (SerializerInterface $serializer, ItemRepository $itemRepository, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->itemRepository = $itemRepository;
        $this->em = $em;
    }

    #[Route('/', name: 'app_item_all')]
    public function getAll(): JsonResponse
    {

        return new JsonResponse
        (
            $this->serializer->serialize
            ($this->itemRepository->findAll(), 'json', ['groups'=>'item']), Response::HTTP_OK, [], true
        );
    }

    #[Route('/new', name: 'app_item_new', methods: ["POST"])]
    public function new(Request $request): JsonResponse
    {

        $content = $request->toArray();

        if (!$content['content'] && !$content['note']){
            return new JsonResponse
            (['message' => 'Vous devez remplir au moins un des deux champs'], Response::HTTP_BAD_REQUEST);
        }

        $item = $this->serializer->deserialize($request->getContent(), Item::class, 'json');

        $this->em->persist($item);
        $this->em->flush();

        return new JsonResponse
        ($this->serializer->serialize($item, 'json', ['groups'=>'item']), Response::HTTP_OK, [], true);
    }

    #[Route('/edit/{id}', name: 'app_item_edit', methods: ["PUT"])]
    public function edit(Request $request, Item $item = null): JsonResponse
    {
        if ($item instanceof Item) {
            $updatedItem = $this->serializer->deserialize($request->getContent(),
                Item::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $item]
            );

            $this->em->persist($updatedItem);
            $this->em->flush();
            return new JsonResponse(['message' => 'sucessful edited'],Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'Error item not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/delete/{id}', name: 'app_item_delete', methods: ["DELETE"])]
    public function delete(int $id): JsonResponse
    {
        $item = $this->itemRepository->find($id);
        if ($item){
            $this->em->remove($item);
            $this->em->flush();

            return new JsonResponse(['message' => "L'item à bien été supprimé"], Response::HTTP_OK);
        }
        return new JsonResponse
        (['message' => "L'item n'existe pas ou à déjà été supprimé"],Response::HTTP_BAD_REQUEST);
    }

    #[Route('/show/{id}', name: 'app_item_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $item = $this->itemRepository->find($id);
        if ($item) {
            return new JsonResponse
            ($this->serializer->serialize($item, 'json', ['groups' => 'item']), Response::HTTP_OK, [], true);
        }

        return new JsonResponse(["message" => "Item not found"], Response::HTTP_NOT_FOUND, []);
    }

}
