<!DOCTYPE html>
<html lang="id">
  <head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran</title>
        <style>
	        body {
	            font-family: 'Courier New', Courier, monospace; /* Font umum untuk printer termal */
	            font-size: 10pt; /* Sesuaikan ukuran font */
	            width: 58mm; /* Atau 80mm, sesuaikan dengan printer Anda */
	            margin: 0;
	            padding: 5px;
	            color: #000;
	        }
	        .container {
	            width: 100%;
	        }
	        .header, .footer {
	            text-align: center;
	            margin-bottom: 5px;
	        }
	        .header h3 {
	            margin: 0;
	            padding: 0;
	        }
	        .transaction-details p, .item-list p, .summary p {
	            margin: 2px 0;
	        }
	        .item-list table {
	            width: 100%;
	            border-collapse: collapse;
	        }
	        .item-list th, .item-list td {
	            text-align: left;
	            padding: 1px 0;
	        }
	        .item-list .qty, .item-list .price, .item-list .subtotal {
	            text-align: right;
	        }
	        hr {
	            border: none;
	            border-top: 1px dashed #000; /* Garis putus-putus */
	            margin: 5px 0;
	        }
	        .text-right {
	            text-align: right;
	        }
	        .text-center {
	            text-align: center;
	        }
	        /* Sembunyikan elemen yang tidak perlu saat mencetak */
	        @media print {
	            body {
	                margin: 0;
	                padding: 0;
	                /* Atur ukuran kertas jika browser mendukung */
	                /* @page { size: 58mm auto; margin: 0mm; } */
	            }
	            .no-print {
	                display: none;
	            }
	        }
	    </style>
	</head>
	<body>
	    <div class="container">
	        <div class="header">
	            <h3>DATAPRIMA POS</h3>
	            <p>Jl. Trembesi Kemayoran<br>Telp: 0813-xxxx-xxxx</p>
	        </div>
	        <hr>
	        <div class="transaction-details">
	            <p>No. Struk: {{ $sale->reference }}</p>
	            <p>Tanggal : {{ $sale->date }}</p>
	            {{-- <p>Kasir   : Nama Kasir</p> --}}
	            <p>Pelanggan: {{ $sale->customer_name }}</p>
	        </div>
	        <hr>
	        <div class="item-list">
	            <table>
	                <thead>
	                    <tr>
	                        <th>Item</th>
	                        <th class="qty">Qty</th>
	                        <th class="price">Price</th>
	                        <th class="subtotal">Total</th>
	                    </tr>
	                </thead>
	                <tbody>
	                    @foreach($sale->saleDetails as $saleDetail) 
	                    <tr>
	                        <td>{{ $saleDetail->name }}</td>
	                        <td class="qty">{{ $saleDetail->quantity }}</td>
	                        <td class="price">{{ number_format($saleDetail->price, 0, ',', '.') }}</td>
	                        <td class="subtotal">{{ number_format($saleDetail->sub_total, 0, ',', '.') }}</td>
	                    </tr>
	                    @endforeach
	                </tbody>
	            </table>
	        </div>
	        <hr>
	        <div class="summary">
	            <p>Total <span style="float:right;">{{ number_format($sale->total_amount, 0, ',', '.') }}</span></p>
	            {{-- <p>Tunai <span style="float:right;">{{ number_format($order->cash_received, 0, ',', '.') }}</span></p>
	            <p>Kembali <span style="float:right;">{{ number_format($order->change, 0, ',', '.') }}</span></p> --}}
	        </div>
	        <hr>
	        <div class="footer">
	            <p>Terima Kasih Atas Kunjungan Anda!</p>
	            <p>-- www.dataprima.com --</p>
	        </div>
	    </div>
	
	    <script type="text/javascript">
	        // Panggil fungsi cetak saat halaman dimuat
	        window.onload = function() {
	            window.print();
			//	window.preview();
	            // Opsional: Tutup window setelah beberapa saat jika ini adalah popup
	            // setTimeout(function(){ window.close(); }, 1000);
	        }
	    </script>
	</body>
</html>
