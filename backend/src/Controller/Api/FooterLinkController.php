<?php

namespace App\Controller\Api;

use App\Entity\FooterLink;
use App\Repository\FooterLinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/footer-links', name: 'api_footer_links_')]
class FooterLinkController extends AbstractController
{
    private function toArray(FooterLink $l): array
    {
        return [
            'id' => $l->getId(),
            'label' => $l->getLabel(),
            'url' => $l->getUrl(),
            'position' => $l->getPosition(),
            'groupName' => $l->getGroupName(),
            'isActive' => $l->isActive(),
        ];
    }

    private function decodeJson(Request $request): array
    {
        $raw = $request->getContent() ?: '[]';
        $data = json_decode($raw, true);
        return is_array($data) ? $data : [];
    }

    #[Route('', name: 'list', methods: ['GET'])]
    public function list(Request $request, FooterLinkRepository $repo): JsonResponse
    {
        $groupName = trim((string) $request->query->get('groupName', ''));
        $isActiveParam = $request->query->get('isActive', null);

        // isActive peut venir comme "1", "0", "true", "false"
        $isActive = null;
        if ($isActiveParam !== null) {
            $v = strtolower((string) $isActiveParam);
            $isActive = in_array($v, ['1', 'true', 'yes', 'on'], true) ? true : false;
        }

        // Simple et lisible : on utilise findBy avec des critères
        $criteria = [];
        if ($groupName !== '') {
            $criteria['groupName'] = $groupName;
        }
        if ($isActiveParam !== null) {
            $criteria['isActive'] = $isActive;
        }

        $items = $repo->findBy($criteria, ['groupName' => 'ASC', 'position' => 'ASC', 'id' => 'ASC']);

        return $this->json([
            'items' => array_map(fn (FooterLink $l) => $this->toArray($l), $items),
        ]);
    }

    #[Route('/{id<\d+>}', name: 'show', methods: ['GET'])]
    public function show(int $id, FooterLinkRepository $repo): JsonResponse
    {
        $item = $repo->find($id);
        if (!$item) {
            return $this->json(['message' => 'NOT_FOUND'], 404);
        }

        return $this->json($this->toArray($item));
    }

    #[Route('', name: 'create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = $this->decodeJson($request);

        // validations minimales (tu peux renforcer avec Symfony Validator + Assert)
        $label = trim((string)($data['label'] ?? ''));
        $url = trim((string)($data['url'] ?? ''));
        $groupName = trim((string)($data['groupName'] ?? ''));
        $position = $data['position'] ?? null;
        $isActive = $data['isActive'] ?? null;

        if ($label === '' || mb_strlen($label) > 100) {
            return $this->json(['message' => 'INVALID_LABEL'], 400);
        }
        if ($url === '' || mb_strlen($url) > 500) {
            return $this->json(['message' => 'INVALID_URL'], 400);
        }
        if ($groupName === '' || mb_strlen($groupName) > 50) {
            return $this->json(['message' => 'INVALID_GROUP_NAME'], 400);
        }
        if (!is_int($position)) {
            // si le front envoie "3" (string), on convertit
            if (is_numeric($position)) $position = (int) $position;
            else return $this->json(['message' => 'INVALID_POSITION'], 400);
        }
        if (!is_bool($isActive)) {
            // tolère "true"/"false"/1/0
            if (is_string($isActive)) {
                $v = strtolower($isActive);
                if (in_array($v, ['true', '1', 'yes', 'on'], true)) $isActive = true;
                elseif (in_array($v, ['false', '0', 'no', 'off'], true)) $isActive = false;
                else return $this->json(['message' => 'INVALID_IS_ACTIVE'], 400);
            } elseif (is_numeric($isActive)) {
                $isActive = ((int)$isActive) === 1;
            } else {
                return $this->json(['message' => 'INVALID_IS_ACTIVE'], 400);
            }
        }

        $item = (new FooterLink())
            ->setLabel($label)
            ->setUrl($url)
            ->setGroupName($groupName)
            ->setPosition((int) $position)
            ->setIsActive((bool) $isActive);

        $em->persist($item);
        $em->flush();

        return $this->json($this->toArray($item), 201);
    }

    #[Route('/{id<\d+>}', name: 'put', methods: ['PUT'])]
    public function put(int $id, Request $request, FooterLinkRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $item = $repo->find($id);
        if (!$item) {
            return $this->json(['message' => 'NOT_FOUND'], 404);
        }

        // PUT = remplacement complet → on exige tous les champs
        $data = $this->decodeJson($request);

        if (!array_key_exists('label', $data) ||
            !array_key_exists('url', $data) ||
            !array_key_exists('groupName', $data) ||
            !array_key_exists('position', $data) ||
            !array_key_exists('isActive', $data)
        ) {
            return $this->json(['message' => 'MISSING_FIELDS'], 400);
        }

        // on réutilise la logique create en validant pareil
        $label = trim((string)$data['label']);
        $url = trim((string)$data['url']);
        $groupName = trim((string)$data['groupName']);
        $position = $data['position'];
        $isActive = $data['isActive'];

        if ($label === '' || mb_strlen($label) > 100) return $this->json(['message' => 'INVALID_LABEL'], 400);
        if ($url === '' || mb_strlen($url) > 500) return $this->json(['message' => 'INVALID_URL'], 400);
        if ($groupName === '' || mb_strlen($groupName) > 50) return $this->json(['message' => 'INVALID_GROUP_NAME'], 400);
        if (!is_int($position)) {
            if (is_numeric($position)) $position = (int)$position;
            else return $this->json(['message' => 'INVALID_POSITION'], 400);
        }
        if (!is_bool($isActive)) {
            if (is_string($isActive)) {
                $v = strtolower($isActive);
                if (in_array($v, ['true','1','yes','on'], true)) $isActive = true;
                elseif (in_array($v, ['false','0','no','off'], true)) $isActive = false;
                else return $this->json(['message' => 'INVALID_IS_ACTIVE'], 400);
            } elseif (is_numeric($isActive)) {
                $isActive = ((int)$isActive) === 1;
            } else return $this->json(['message' => 'INVALID_IS_ACTIVE'], 400);
        }

        $item->setLabel($label)
            ->setUrl($url)
            ->setGroupName($groupName)
            ->setPosition((int) $position)
            ->setIsActive((bool) $isActive);

        $em->flush();

        return $this->json($this->toArray($item));
    }

    #[Route('/{id<\d+>}', name: 'patch', methods: ['PATCH'])]
    public function patch(int $id, Request $request, FooterLinkRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $item = $repo->find($id);
        if (!$item) {
            return $this->json(['message' => 'NOT_FOUND'], 404);
        }

        $data = $this->decodeJson($request);

        if (array_key_exists('label', $data)) {
            $label = trim((string)$data['label']);
            if ($label === '' || mb_strlen($label) > 100) return $this->json(['message' => 'INVALID_LABEL'], 400);
            $item->setLabel($label);
        }

        if (array_key_exists('url', $data)) {
            $url = trim((string)$data['url']);
            if ($url === '' || mb_strlen($url) > 500) return $this->json(['message' => 'INVALID_URL'], 400);
            $item->setUrl($url);
        }

        if (array_key_exists('groupName', $data)) {
            $groupName = trim((string)$data['groupName']);
            if ($groupName === '' || mb_strlen($groupName) > 50) return $this->json(['message' => 'INVALID_GROUP_NAME'], 400);
            $item->setGroupName($groupName);
        }

        if (array_key_exists('position', $data)) {
            $position = $data['position'];
            if (!is_int($position)) {
                if (is_numeric($position)) $position = (int)$position;
                else return $this->json(['message' => 'INVALID_POSITION'], 400);
            }
            $item->setPosition((int) $position);
        }

        if (array_key_exists('isActive', $data)) {
            $isActive = $data['isActive'];
            if (!is_bool($isActive)) {
                if (is_string($isActive)) {
                    $v = strtolower($isActive);
                    if (in_array($v, ['true','1','yes','on'], true)) $isActive = true;
                    elseif (in_array($v, ['false','0','no','off'], true)) $isActive = false;
                    else return $this->json(['message' => 'INVALID_IS_ACTIVE'], 400);
                } elseif (is_numeric($isActive)) {
                    $isActive = ((int)$isActive) === 1;
                } else return $this->json(['message' => 'INVALID_IS_ACTIVE'], 400);
            }
            $item->setIsActive((bool) $isActive);
        }

        $em->flush();

        return $this->json($this->toArray($item));
    }

    #[Route('/{id<\d+>}', name: 'delete', methods: ['DELETE'])]
    public function delete(int $id, FooterLinkRepository $repo, EntityManagerInterface $em): JsonResponse
    {
        $item = $repo->find($id);
        if (!$item) {
            return $this->json(['message' => 'NOT_FOUND'], 404);
        }

        $em->remove($item);
        $em->flush();

        return $this->json(['ok' => true]);
    }
}
