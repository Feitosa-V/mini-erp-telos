<!DOCTYPE html>
<html>
<head>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #333;
            text-align: center;
            font-size: 24px;
        }
        h2 {
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }
        .order-list {
            list-style: none;
            padding: 0;
        }
        .order-item {
            background: #f9f9f9;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            border-left: 5px solid;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>📊 Resumo Diário de Pedidos</h1>

    @php
        $statusMap = [
            'Pending' => ['label' => 'Pendente', 'color' => '#ffc107'],
            'Completed' => ['label' => 'Concluído', 'color' => '#28a745'],
            'Canceled' => ['label' => 'Cancelado', 'color' => '#dc3545']
        ];
    @endphp

    @foreach ($orders as $status => $orderList)
        @php
            $statusInfo = $statusMap[$status] ?? ['label' => ucfirst($status), 'color' => '#000'];
        @endphp
        <h2 style="color: <?= $statusInfo['color'] ?> ">{{ $statusInfo['label'] }}</h2>
        <ul class="order-list">
            @foreach ($orderList as $order)
                <li class="order-item" style="border-left-color: <?= $statusInfo['color'] ?>;">
                    <strong>Pedido #{{ $order->id }} - Total: R$ {{ number_format($order->total_value, 2, ',', '.') }}</strong> <br>
                    <small>Fornecedor: {{ $order->supplier->name }}</small>
                </li>
            @endforeach
        </ul>
    @endforeach

    <div class="footer">
        Este e-mail foi gerado automaticamente pelo sistema.
    </div>
</div>

</body>
</html>
