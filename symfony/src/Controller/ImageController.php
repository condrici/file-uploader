<?php

namespace App\Controller;

use App\Service\Image\ImageApiListingService;
use App\Service\Image\ImagePicturePreviewService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Image\ImageBulkUploadService;

/**
 * @Route("/image", name="image_")
 */
class ImageController extends AbstractController
{
    /** @var ImageBulkUploadService */
    private $imageUploadService;

    /** @var ImagePicturePreviewService */
    private $imagePicturePreviewService;

    /** @var ImageApiListingService */
    private $imageApiListingService;

    /**
     * ImageController constructor.
     * @param ImageBulkUploadService $imageUploadService
     * @param ImagePicturePreviewService $imagePicturePreviewService
     * @param ImageApiListingService $imageApiListingService
     */
    public function __construct(
        ImageBulkUploadService $imageUploadService,
        ImagePicturePreviewService $imagePicturePreviewService,
        ImageApiListingService $imageApiListingService
    ) {
        $this->imageUploadService = $imageUploadService;
        $this->imagePicturePreviewService = $imagePicturePreviewService;
        $this->imageApiListingService = $imageApiListingService;
    }


    /**
     * @Route("/upload", name="uploadpage", methods={"GET"})
     * @return Response
     */
    public function index(): Response
    {
        return $this->render('image/upload.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }

    /**
     * @Route("/upload", name="image_upload", methods={"POST"})
     * @param Request $request
     * @return JsonResponse
     */
    public function bulkUpload(Request $request): JsonResponse
    {
        try {
            $file = $request->files->get('file');
            $this->imageUploadService->csvBulkUpload($file);
            return new JsonResponse('File uploaded successfully', Response::HTTP_OK);
        } catch (\Throwable $exception) {
            return $this->jsonError('Could not process file: ' . $exception->getMessage());
        }
    }

    /**
     * @Route("/preview/{id}", name="image_preview_id", methods={"GET"})
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function previewById(Request $request, int $id): Response
    {
        try {
            return $this->imagePicturePreviewService->getBinaryPreviewImageOrThrow($id);
        } catch (\Throwable $exception) {
            return $this->jsonError('Could not render image: ' . $exception->getMessage());
        }
    }

    /**
     * @Route("/list", name="image_list_all", methods={"GET"})
     * @param Request $request
     * @return JsonResponse
     */
    public function listAll(Request $request): JsonResponse
    {
        try {
            $items = $this->imageApiListingService->listAll();
            return new JsonResponse($items);
        } catch (\Throwable $exception) {
            return $this->jsonError('Could not retrieve items: ' . $exception->getMessage());
        }
    }

    /**
     * @param string $message
     * @return JsonResponse
     */
    private function jsonError(string $message): JsonResponse
    {
        return $this->json(['error' => $message]);
    }
}
