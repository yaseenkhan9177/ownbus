<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Services\WhatsAppService;

class SendWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $phone;
    protected $template;
    protected $vars;
    protected $message;

    /**
     * Create a new job instance.
     */
    public function __construct($phone, $template, $vars = [], $message = null)
    {
        $this->phone = $phone;
        $this->template = $template;
        $this->vars = $vars;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(WhatsAppService $whatsAppService): void
    {
        if ($this->message) {
            $whatsAppService->send($this->phone, $this->message);
        } else {
            $whatsAppService->sendTemplate($this->phone, $this->template, $this->vars);
        }
    }
}
