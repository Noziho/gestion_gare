<?php

namespace App\Controller;

use App\Entity\TrainStation;
use App\Repository\TrainStationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class TrainStationController extends AbstractController
{
    private SerializerInterface $serializer;
    private TrainStationRepository $trainStationRepository;
    private EntityManagerInterface $em;

    /**
     * @param SerializerInterface $serializer
     * @param TrainStationRepository $trainStationRepository
     * @param EntityManagerInterface $em
     */
    public function __construct
    (SerializerInterface $serializer, TrainStationRepository $trainStationRepository, EntityManagerInterface $em)
    {
        $this->serializer = $serializer;
        $this->trainStationRepository = $trainStationRepository;
        $this->em = $em;
    }

    #[Route('/', name: 'app_trainStation_all')]
    public function getAll(): JsonResponse
    {

        return new JsonResponse
        (
            $this->serializer->serialize
            ($this->trainStationRepository->findAll(), 'json', ['groups'=>'trainStation']), Response::HTTP_OK, [], true
        );
    }

    #[Route('/new', name: 'app_trainStation_new', methods: ["POST"])]
    public function new(Request $request): JsonResponse
    {

        $content = $request->toArray();

        if (!$content['content'] && !$content['note']){
            return new JsonResponse
            (['message' => 'Vous devez remplir au moins un des deux champs'], Response::HTTP_BAD_REQUEST);
        }

        $trainStation = $this->serializer->deserialize($request->getContent(), TrainStation::class, 'json');

        $this->em->persist($trainStation);
        $this->em->flush();

        return new JsonResponse
        ($this->serializer->serialize($trainStation, 'json', ['groups'=>'trainStation']), Response::HTTP_OK, [], true);
    }

    #[Route('/edit/{id}', name: 'app_trainStation_edit', methods: ["PUT"])]
    public function edit(Request $request, TrainStation $trainStation = null): JsonResponse
    {
        if ($trainStation instanceof TrainStation) {
            $updatedTrainStation = $this->serializer->deserialize($request->getContent(),
                TrainStation::class,
                'json',
                [AbstractNormalizer::OBJECT_TO_POPULATE => $trainStation]
            );

            $this->em->persist($updatedTrainStation);
            $this->em->flush();
            return new JsonResponse(['message' => 'sucessful edited'],Response::HTTP_OK);
        }
        return new JsonResponse(['message' => 'Error trainStation not found'], Response::HTTP_NOT_FOUND);
    }

    #[Route('/delete/{id}', name: 'app_trainStation_delete', methods: ["DELETE"])]
    public function delete(int $id): JsonResponse
    {
        $trainStation = $this->trainStationRepository->find($id);
        if ($trainStation){
            $this->em->remove($trainStation);
            $this->em->flush();

            return new JsonResponse(['message' => "La trainStation à bien été supprimé"], Response::HTTP_OK);
        }
        return new JsonResponse
        (['message' => "La trainStation n'existe pas ou à déjà été supprimé"],Response::HTTP_BAD_REQUEST);
    }

    #[Route('/show/{id}', name: 'app_trainStation_show', methods: ['GET'])]
    public function show(int $id): JsonResponse
    {
        $trainStation = $this->trainStationRepository->find($id);
        if ($trainStation) {
            return new JsonResponse
            ($this->serializer->serialize
            ($trainStation, 'json', ['groups' => 'trainStation']), Response::HTTP_OK, [], true);
        }

        return new JsonResponse(["message" => "TrainStation not found"], Response::HTTP_NOT_FOUND, []);
    }
}
