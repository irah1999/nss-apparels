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

        $customerModel = new \App\Models\CustomerModel();
        $bulkModel = new \App\Models\BulkImportModel();
        
        $importRow = $bulkModel->where('filepath', $uuid . '.csv')->first();

        if (!file_exists($filepath)) {
            file_put_contents($logFile, "Error: File not found\n", FILE_APPEND);
            CLI::error('File not found: ' . $filepath);
            if ($importRow) {
                $bulkModel->update($importRow['id'], ['status' => 'failed']);
            }
            return;
        }

        if (!$importRow) {
            file_put_contents($logFile, "Error: Import record not found in database\n", FILE_APPEND);
            CLI::error('Import record not found');
            return;
        }

        try {
            $handle = fopen($filepath, 'r');
            if (!$handle) throw new \Exception("Cannot open file: " . $filepath);

            fgetcsv($handle); // Skip header

            // Count total rows
            $total = 0;
            while (fgetcsv($handle) !== FALSE) { $total++; }
            rewind($handle);
            fgetcsv($handle); // Skip header again

            // Create Failed Output File
            $failedFilepath = WRITEPATH . 'uploads/imports/' . $uuid . '_failed.csv';
            $failedHandle = fopen($failedFilepath, 'w');
            fputcsv($failedHandle, ['Name', 'Email', 'Phone', 'Joining Date', 'Failure Reason']);

            // Update database row
            $bulkModel->update($importRow['id'], ['total_count' => $total, 'status' => 'processing']);
            cache()->save('import_' . $uuid, ['total' => $total, 'current' => 0, 'done' => false], 1200);

            $inserted = 0;
            $failed = 0;
            $current = 0;

            while (($row = fgetcsv($handle)) !== FALSE) {
                $name = isset($row[0]) ? trim($row[0]) : '';
                $email = isset($row[1]) ? trim($row[1]) : '';
                $phone = isset($row[2]) ? trim($row[2]) : '';
                $joiningDate = isset($row[3]) ? trim($row[3]) : '';
                
                // Remove Excel formatting quotes if present
                $phone = ltrim($phone, "'");
                
                if (stripos($phone, 'e') !== false && is_numeric($phone)) {
                    $phone = sprintf('%.0f', (float) $phone);
                }
                
                $current++;

                if (empty($name) || empty($phone)) {
                    $failed++;
                    $reason = empty($name) ? 'Name is empty' : 'Phone is empty';
                    fputcsv($failedHandle, [$name, $email, $phone, $joiningDate, $reason]);
                    continue; 
                }
                
                $data = [
                    'name'         => $name,
                    'email'        => $email,
                    'phone'        => $phone,
                    'joining_date' => !empty($joiningDate) ? $joiningDate : date('Y-m-d'),
                ];
                
                // Manual check for uniqueness (ignoring soft deleted automatically)
                if ($customerModel->where('phone', $phone)->first()) {
                    $failed++;
                    fputcsv($failedHandle, [$name, $email, $phone, $joiningDate, 'Phone already registered']);
                    continue;
                }

                if ($customerModel->insert($data) === false) {
                    $failed++;
                    $reason = implode(', ', $customerModel->errors());
                    fputcsv($failedHandle, [$name, $email, $phone, $joiningDate, $reason]);
                } else {
                    $inserted++;
                }
                
                if ($current % 50 === 0) {
                    cache()->save('import_' . $uuid, ['total' => $total, 'current' => $current, 'done' => false], 1200);
                    $bulkModel->update($importRow['id'], [
                        'inserted_count' => $inserted,
                        'failed_count'   => $failed
                    ]);
                }
            }
            fclose($handle);
            fclose($failedHandle);
            cache()->save('import_' . $uuid, ['total' => $total, 'current' => $total, 'done' => true], 1200);
            
            $bulkModel->update($importRow['id'], [
                'inserted_count' => $inserted,
                'failed_count'   => $failed,
                'status'         => 'completed'
            ]);
            CLI::write('Import completed of ' . $total . ' items.');

        } catch (\Exception $e) {
            if (isset($handle) && is_resource($handle)) fclose($handle);
            if (isset($failedHandle) && is_resource($failedHandle)) fclose($failedHandle);
            $bulkModel->update($importRow['id'], ['status' => 'failed']);
            file_put_contents($logFile, "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
            CLI::error('Import failed: ' . $e->getMessage());
        }
    }
}
