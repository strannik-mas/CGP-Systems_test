<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\UserWithImageFilesSaveRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{

    public function store(UserWithImageFilesSaveRequest $request): JsonResponse
    {
        $imageUrls = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // сохраняем в storage/app/public/users
                $path = $image->store('public/users');
                $url = env('APP_URL') . Storage::url($path); // получаем URL для доступа через веб
                $imageUrls[] = $url;
            }
        }

        // Отправляем POST-запрос на API-роут
        $apiResponse = Http::post(url('/api/users'), [
            'name' => $request->name,
            'city' => $request->city,
            'images' => $imageUrls, // массив URL
        ]);

        if ($apiResponse->failed()) {
            return response()->json(['error' => 'Ошибка создания пользователя'], 500);
        }

        return response()->json($apiResponse->json());
    }
}