<?php

namespace App\Helpers;

use PhpOffice\PhpSpreadsheet\IOFactory;

class FileImportHelper
{
    public static function toArray($file): array
    {
        $extension = $file->getClientOriginalExtension();

        if ($extension === 'csv' || $extension === 'txt') {
            $rows = array_map('str_getcsv', file($file->getPathname()));
            $header = array_shift($rows);
            return array_map(fn($row) => array_combine($header, $row), $rows);
        }

        // Handle xlsx/xls
        $spreadsheet = IOFactory::load($file->getPathname());
        $sheet = $spreadsheet->getActiveSheet()->toArray();
        $header = array_shift($sheet);
        return array_map(fn($row) => array_combine($header, $row), $sheet);
    }
}