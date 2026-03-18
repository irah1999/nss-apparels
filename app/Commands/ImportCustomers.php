<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class ImportCustomers extends BaseCommand
{
    protected $group       = 'Customers';
    protected $name        = 'import:customers';
    protected $description = 'Imports customers from a CSV file in the background';

    protected $usage       = 'import:customers [uuid]';
    protected $arguments   = [
        'uuid' => 'The UUID of the uploaded CSV file',
    ];

    public function run(array $params)
    {
        $uuid = $params[0] ?? null;
        $logFile = WRITEPATH . 'import_debug.log';
        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Command triggered for: $uuid\n", FILE_APPEND);

        if (!$uuid) {
            file_put_contents($logFile, "Error: UUID missing\n", FILE_APPEND);
            CLI::error('UUID is required');
            return;
        }

        $filepath = WRITEPATH . 'uploads/imports/' . $uuid . '.csv';
        file_put_contents($logFile, "Filepath: $filepath\n", FILE_APPEND);

        if (!file_exists($filepath)) {
            file_put_contents($logFile, "Error: File not found\n", FILE_APPEND);
            CLI::error('File not found: ' . $filepath);
            return;
        }

        $customerModel = new \App\Models\CustomerModel();
        $bulkModel = new \App\Models\BulkImportModel();
        
        $importRow = $bulkModel->where('filepath', $uuid . '.csv')->first();
        
        $handle = fopen($filepath, 'r');
        fgetcsv($handle); // Skip header

        // Count total rows
        $total = 0;
        while (fgetcsv($handle) !== FALSE) { $total++; }
        rewind($handle);
        fgetcsv($handle); // Skip header again

        // Update database row if found
        if ($importRow) {
            $bulkModel->update($importRow['id'], ['total_count' => $total, 'status' => 'processing']);
        }
        
        cache()->save('import_' . $uuid, ['total' => $total, 'current' => 0, 'done' => false], 1200);

        $inserted = 0;
        $failed = 0;
        $current = 0;

        while (($row = fgetcsv($handle)) !== FALSE) {
            $name = isset($row[0]) ? trim($row[0]) : '';
            $phone = isset($row[2]) ? trim($row[2]) : '';
            $current++;

            if (empty($name) || empty($phone)) {
                $failed++;
                continue; // Skip row
            }
            
            $data = [
                'name'         => $name,
                'email'        => isset($row[1]) ? trim($row[1]) : '',
                'phone'        => $phone,
                'joining_date' => !empty($row[3]) ? trim($row[3]) : date('Y-m-d'),
            ];
            
            if ($customerModel->insert($data) === false) {
                $failed++;
            } else {
                $inserted++;
            }
            
            if ($current % 50 === 0) {
                cache()->save('import_' . $uuid, ['total' => $total, 'current' => $current, 'done' => false], 1200);
                if ($importRow) {
                    $bulkModel->update($importRow['id'], [
                        'inserted_count' => $inserted,
                        'failed_count'   => $failed
                    ]);
                }
            }
        }
        fclose($handle);
        cache()->save('import_' . $uuid, ['total' => $total, 'current' => $total, 'done' => true], 1200);
        
        if ($importRow) {
            $bulkModel->update($importRow['id'], [
                'inserted_count' => $inserted,
                'failed_count'   => $failed,
                'status'         => 'completed'
            ]);
        }

        // Keep file for future downloads
        // @unlink($filepath);
        CLI::write('Import completed of ' . $total . ' items.');
    }
}
