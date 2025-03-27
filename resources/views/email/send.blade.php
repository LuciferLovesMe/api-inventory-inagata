<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Notifikasi</title>
</head>
<body>
    <h3>Peringatan Stok Sedang ada di bawah batas minimum(10)!</h3>
    <h5>Barang {{ $data->item->name_item }}. Gudang {{ $data->warehouse->name_warehouse }}. Sisa stok: {{ $data->stock }} buah.</h5>
</body>
</html>