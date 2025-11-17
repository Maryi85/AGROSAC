<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class DeleteTestUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:delete-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina los usuarios de prueba de foreman y worker de la base de datos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Emails de usuarios de prueba de foreman
        $foremanTestEmails = [
            'foreman@example.com',
            'carlos.mendoza@agrosac.com',
            'maria.gonzalez@agrosac.com',
            'jose.ramirez@agrosac.com',
        ];

        // Emails de usuarios de prueba de worker
        $workerTestEmails = [
            'worker@example.com',
            'juan.perez@agrosac.com',
            'maria.garcia@agrosac.com',
            'carlos.lopez@agrosac.com',
            'ana.rodriguez@agrosac.com',
            'pedro.martinez@agrosac.com',
        ];

        $allTestEmails = array_merge($foremanTestEmails, $workerTestEmails);

        $this->info('Eliminando usuarios de prueba...');

        $deletedCount = 0;
        $notFoundCount = 0;

        foreach ($allTestEmails as $email) {
            $user = User::where('email', $email)->first();
            
            if ($user) {
                $user->delete();
                $this->line("✓ Eliminado: {$email} ({$user->role})");
                $deletedCount++;
            } else {
                $this->line("✗ No encontrado: {$email}");
                $notFoundCount++;
            }
        }

        $this->newLine();
        $this->info("Proceso completado:");
        $this->line("  - Usuarios eliminados: {$deletedCount}");
        $this->line("  - Usuarios no encontrados: {$notFoundCount}");

        return Command::SUCCESS;
    }
}
