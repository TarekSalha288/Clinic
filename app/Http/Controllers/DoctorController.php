<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageUploadRequest;
use App\Http\Requests\PostArticaleRequest;
use App\Http\Requests\PostPreviewRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Responses\Response;
use App\Models\Preview;
use App\Services\DoctorService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Throwable;
use function PHPUnit\Framework\returnArgument;

class DoctorController extends Controller
{
    private DoctorService $doctorService;
    private UserService $userService;
    public function __construct(DoctorService $doctorService, UserService $userService)
    {
        $this->doctorService = $doctorService;
        $this->userService = $userService;
    }

    public function postArticale(PostArticaleRequest $request)
    {
        $data = [];
        try {
            $data = $this->doctorService->postArtical($request);
            return Response::Success($data['post'], $data['message'], $data['code']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function uploadImages(ImageUploadRequest $request)
    {
        $data = [];
        try {
            $data = $this->userService->uploadImage($request, 'posts');
            return Response::Success($data['path'], $data['message'], $data['code']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }

    public function uploadImagesForProfile(ImageUploadRequest $request)
    {
        $data = [];
        try {
            $data = $this->userService->uploadImage($request, 'Doctor_Profile_Photo');
            return Response::Success($data['path'], $data['message'], $data['code']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $data = [];
        try {
            $data = $this->doctorService->updateProfile($request);
            return Response::Success($data['user'], $data['message'], $data['code']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function updateArticle(PostArticaleRequest $request, $id)
    {
        $data = [];
        try {
            $data = $this->doctorService->updateArticle($request, $id);
            return Response::Success($data['article'], $data['message'], $data['code']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function deleteArticle($id)
    {
        $data = [];
        try {
            $data = $this->doctorService->deleteArticle($id);
            return Response::Success($data['article'], $data['message'], $data['code']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getArticles()
    {
        $data = [];
        try {
            $data = $this->doctorService->getArticles();
            return Response::Success($data['articles'], $data['message'], $data['code']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getArticleById($id)
    {
        $data = [];
        try {
            $data = $this->doctorService->getArticleById($id);
            return Response::Success($data['article'], $data['message'], $data['code']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getApointments()
    {
        $data = [];
        try {
            $data = $this->doctorService->getApointments();
            return Response::Success($data['apointments'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function postPreview(PostPreviewRequest $request, $patient_id)
    {
        $data = [];
        try {
            $data = $this->doctorService->postPreview($request, $patient_id);
            return Response::Success($data['preview'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function updatePreview(PostPreviewRequest $request, $preview_id)
    {
        $data = [];
        try {
            $data = $this->doctorService->updatePreview($request, $preview_id);
            return Response::Success($data['preview'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function deletePreview($preview_id)
    {
        $data = [];
        try {
            $data = $this->doctorService->deletePreview($preview_id);
            return Response::Success($data['preview'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getPreviews()
    {
        $data = [];
        try {
            $data = $this->doctorService->getPreviews();
            return Response::Success($data['previews'], $data['message'], $data['code']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }

}
