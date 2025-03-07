<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Order;
use App\Mail\DailyOrderReport;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendOrderReportEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:send-order-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envia um e-mail diário com o resumo dos pedidos';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::where('role', 'admin')->get(); // Envia para os administradores

        // Buscar pedidos dos últimos 7 dias e agrupar por status
        $orders = Order::where('created_at', '>=', Carbon::now()->subDays(7))
            ->with('supplier', 'products')
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('status'); // Agrupar por status

        foreach ($users as $user) {
            Mail::to($user->email)->send(new DailyOrderReport($orders));
        }

        $this->info('E-mail de relatório de pedidos enviado com sucesso!');
    }
}
