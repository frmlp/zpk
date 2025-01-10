<?php

namespace App\Http\Controllers;

use App\Models\MapFile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class MapFileController extends Controller
{
    public function getMapUIData(): JsonResponse
    {
        error_log('getMapUIData()');
        // Pobranie tylko kolumn id i name z tabeli map_files
        $mapFiles = MapFile::select('id', 'name', 'icon_path')->get();

        return response()->json($mapFiles);
    
    }

    public function getMapFileWithDetails($id)
    {
        error_log('getMapFileWithDetails($id)');
        // Pobranie mapy na podstawie ID
        $mapFile = MapFile::with('pages')->findOrFail($id);
        error_log($mapFile);

        // Sprawdzenie, czy plik istnieje
        $filePath = storage_path("{$mapFile->path}");
        if (!file_exists($filePath)) {
            abort(404, 'File not found');
        }
        error_log('getMapFileWithDetails($id) :: 1');

        // Przygotowanie danych JSON z tabeli map_pages
        $pagesData = $mapFile->pages->map(function ($page) {
            return [
                'page'    => $page->page,
                'coeff_a' => $page->coeff_a,
                'coeff_b' => $page->coeff_b,
                'coeff_c' => $page->coeff_c,
                'coeff_d' => $page->coeff_d,
                'coeff_e' => $page->coeff_e,
                'coeff_f' => $page->coeff_f,
            ];
        });

        error_log($pagesData);

        // Zwrócenie odpowiedzi jako plik PDF + JSON w nagłówkach
        return response()->streamDownload(function () use ($filePath) {
            readfile($filePath);
        }, basename($filePath), [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . basename($filePath) . '"',
            'X-Pages-Data'        => json_encode($pagesData),
        ]);
    }

}
