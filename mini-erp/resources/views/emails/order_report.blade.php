<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Resumo Diário de Pedidos</title>
</head>
<body>
    <h1>Resumo Diário de Pedidos</h1>
    <p>Aqui estão os pedidos do dia:</p>
    <ul>
        @foreach ($orders as $order)
            <li>
                Pedido #{{ $order->id }} - Total: R$ {{ number_format($order->total_value, 2, ',', '.') }}
            </li>
        @endforeach
    </ul>
</body>
</html>
