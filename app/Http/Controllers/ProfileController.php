<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Демонстрационный профиль: в следующих заданиях данные будут из модели пользователя.
     */
    public function show(): View
    {
        return view('pages.user', [
            'initials' => 'ЕБ',
            'fullName' => 'Ерке Баксаисов',
            'roleLabel' => 'Студент',
            'bio' => 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Commodi delectus eaque facilis ipsum itaque non placeat quae quia ratione ullam, unde vel, voluptate voluptates! Aperiam atque distinctio facere magni veniam!',
            'courses' => [
                ['title' => 'Курс 1', 'status' => 'В процессе', 'badgeClass' => 'text-bg-secondary'],
                ['title' => 'Курс 2', 'status' => 'Завершён', 'badgeClass' => 'text-bg-success'],
                ['title' => 'Курс 3', 'status' => 'Скоро', 'badgeClass' => 'text-bg-warning text-dark'],
            ],
        ]);
    }
}
