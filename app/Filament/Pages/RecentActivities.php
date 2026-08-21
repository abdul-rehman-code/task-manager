<?php

// namespace App\Filament\Pages;

// use Filament\Pages\Page;
// // Yahan apna sahi model use karein, misal ke taur par agar Task model hai:
// // use App\Models\Task; 

// class RecentActivities extends Page
// {
//     protected static ?string $navigationIcon = 'heroicon-o-clock';
//     protected static string $view = 'filament.pages.recent-activities';
//     protected static ?string $navigationLabel = 'Recent Activities';

//     public function getViewData(): array
//     {
//         return [
//             // Apne model ka naam yahan likhein jo aapke project mein maujood ho
//             'activities' => \App\Models\Task::latest()->paginate(10), // Misal ke taur par Task model ya jo bhi aapka activity model ho
//         ];
//     }
// }