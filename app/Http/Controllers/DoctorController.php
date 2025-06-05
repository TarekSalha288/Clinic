<?php

namespace App\Http\Controllers;

use App\Http\Requests\ImageUploadRequest;
use App\Http\Requests\PostArticaleRequest;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Responses\Response;
use App\Services\DoctorService;
use Illuminate\Http\Request;
use Throwable;
use function PHPUnit\Framework\returnArgument;

class DoctorController extends Controller
{
    private DoctorService $doctorService;
    public function __construct(DoctorService $doctorService)
    {
        $this->doctorService = $doctorService;
    }

    public function postArticale(PostArticaleRequest $request)
    {
        $data = [];
        try {
            $data = $this->doctorService->postArtical($request);
            return Response::Success($data['post'], $data['message']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function uploadImages(ImageUploadRequest $request)
    {
        $data = [];
        try {
            $data = $this->doctorService->uploadImage($request, 'posts');
            return Response::Success($data['path'], $data['message']);

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
            return Response::Success($data['user'], $data['message']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function uploadImagesForProfile(ImageUploadRequest $request)
    {
        $data = [];
        try {
            $data = $this->doctorService->uploadImage($request, 'Profile_Photo');
            return Response::Success($data['path'], $data['message']);

        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function getProfileImage()
    {
        $data = [];
        try {
            $data = $this->doctorService->getProfileImage();
            return Response::Success($data['path'], $data['message']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }
    public function deleteProfileImage()
    {
        $data = [];
        try {
            $data = $this->doctorService->deleteProfileImage();
            return Response::Success($data['path'], $data['message']);
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
            return Response::Success($data['article'], $data['message']);

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
            return Response::Success($data['article'], $data['message']);

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
            return Response::Success($data['articles'], $data['message']);

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
            return Response::Success($data['article'], $data['message']);

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
            return Response::Success($data['apointments'], $data['message']);
        } catch (Throwable $th) {
            $message = $th->getMessage();
            return Response::Error($data, $message);
        }
    }

}
