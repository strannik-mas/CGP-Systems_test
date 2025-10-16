<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserWithImagesSaveRequest;
use App\Models\User;
use App\Models\UserImage;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Возвращает список пользователей с количеством изображений, отсортированных по убыванию количества изображений.
     * Пагинация по 50 пользователей на страницу.
     * @return JsonResponse
     */
    public function index()
    {
        // получаем пользователей с количеством изображений, сортируем по убыванию и пагинируем
        $users = User::query()
            ->withCount('images')
            ->orderByDesc('images_count')
            ->paginate(50, ['name', 'city']);

        // убираем лишние поля из ответа
        $users->getCollection()->transform(fn ($user) => [
            'name' => $user->name,
            'city' => $user->city,
            'images_count' => $user->images_count,
        ]);

        return response()->json($users);
    }

    public function store(UserWithImagesSaveRequest $request): JsonResponse
    {
        try {
            $imageUrls = $request->input('images', []);
            DB::beginTransaction();
            $user = User::create($request->only(['name', 'city']));
            if (! empty($imageUrls)) {
                $user->images()->saveMany(
                    array_map(fn ($url) => new UserImage(['image' => $url, 'user_id' => $user->id]), $imageUrls)
                );
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Failed to create user', 'message' => $e->getMessage()], 500);
        }
        return response()->json(['status' => 'success', 'message' => 'User created successfully'], 201);
    }
}
