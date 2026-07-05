<?php

namespace App\Jobs;

use App\Models\GangSheet;
use App\Http\Controllers\GangSheetController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job para generar imagen de alta resolución de Gang Sheet
 * 
 * Se ejecuta después de que el pago es exitoso
 * Puede ser sincrónico o asincrónico (recomendado asincrónico para imágenes grandes)
 * 
 * Configuración en .env:
 * QUEUE_CONNECTION=database  (o redis, sqs, etc)
 * 
 * Para ejecutar jobs:
 * php artisan queue:work
 */
class GenerateGangSheetImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $gangSheet;

    /**
     * Create a new job instance.
     */
    public function __construct(GangSheet $gangSheet)
    {
        $this->gangSheet = $gangSheet;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        try {
            // \Log::info("Starting image generation for gang sheet {$this->gangSheet->id}");

            $this->gangSheet->update(['status' => 'processing']);

            // Usar los métodos del GangSheetController
            $controller = new GangSheetController();
            
            // Método privado, así que usamos reflección
            $reflection = new \ReflectionClass($controller);
            $method = $reflection->getMethod('generateHighResImage');
            $method->setAccessible(true);
            
            $finalPath = $method->invokeArgs($controller, [$this->gangSheet]);

            // Guardar ruta final
            $this->gangSheet->update([
                'final_path' => $finalPath,
                'status' => 'completed',
            ]);

            // \Log::info("Image generation completed for gang sheet {$this->gangSheet->id}");

            // Aquí podrías enviar email al usuario, webhook, etc
            event(new \App\Events\GangSheetImageGenerated($this->gangSheet));

        } catch (\Exception $e) {
            \Log::error("Image generation failed for gang sheet {$this->gangSheet->id}: {$e->getMessage()}");

            $this->gangSheet->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            // Reintentar el job (opcional)
            if ($this->attempts() < 3) {
                $this->release(delay: 60); // Reintentar en 60 segundos
            }

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception)
    {
        \Log::error("Job failed for gang sheet {$this->gangSheet->id}: {$exception->getMessage()}");

        $this->gangSheet->update([
            'status' => 'failed',
            'error_message' => 'Image generation failed after multiple attempts',
        ]);
    }
}
