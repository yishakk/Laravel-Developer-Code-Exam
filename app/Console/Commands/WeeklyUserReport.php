<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class WeeklyUserReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:weekly-user-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate a CSV report of new users registered in the last 7 days and email it to the admin.';

    /**
     * Execute the console command.
     */
    public function __construct()
    {
        parent::__construct();
    }
    public function handle()
    {
        try {
            // Get users created in the last 7 days
            $users = User::where('created_at', '>=', Carbon::now()->subDays(7))->get(['id', 'name', 'email', 'created_at']);

            if ($users->isEmpty()) {
                Log::info('No new users registered in the last 7 days.');
                return;
            }

            // Generate CSV
            $csvData = $this->generateCSV($users);

            // Store the CSV file
            $fileName = 'weekly_new_users_report_' . Carbon::now()->format('Y_m_d') . '.csv';
            Storage::put($fileName, $csvData);

            // Send email with CSV file as an attachment
            $this->sendEmail($fileName);

            // Log success
            Log::info('Weekly new users report generated and sent successfully.');

        } catch (\Exception $e) {
            // Log error
            Log::error('Error generating or sending weekly users report: ' . $e->getMessage());
        }
    }

    private function generateCSV($users)
    {
        $csvData = "id,name,email,created_at\n";
        foreach ($users as $user) {
            $csvData .= "{$user->id},{$user->name},{$user->email},{$user->created_at}\n";
        }
        return $csvData;
    }

    private function sendEmail($fileName)
    {
        $email = 'yishakkibru@gmail.com';

        Mail::raw('Please find the weekly report of new users attached.', function ($message) use ($email, $fileName) {
            $message->to($email)
                ->subject('Weekly New Users Report')
                ->attach(storage_path('app/' . $fileName));
        });
    }
}
