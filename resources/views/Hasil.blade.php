<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deteksi Berita Bohong</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js
"></script>
</head>
<body>
    <div class="grid h-screen place-items-center">
        <div class="container mx-auto px-20">
            <div style='background-color:rgb(255, 255, 255)'>
                <div class="relative px-4 mx-auto max-w-7xl sm:px-6 lg:px-8" style="cursor: auto;" action="/cek" method="POST">
                    <div class="max-w-lg mx-auto overflow-hidden rounded-lg shadow-lg lg:max-w-none lg:flex">
                        <div class="flex-1 px-6 py-8 bg-white lg:p-12" style="cursor: auto;">
                            <h3 class="text-2xl font-extrabold text-gray-900 sm:text-3xl" style="cursor: auto;">Hasil Analisa</h3>
                            <div class=" mt-6 border-t-2 border-gray-200"></div>
                            <div class="flex justify-between">
                                <p class="mt-6 text-base text-gray-500">{{$berita}}</p>
                                <div class="mt-6 w-1/2 bg-[#DEE1EC]">
                                    <canvas id="hasil"></canvas>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            var ctx = document.getElementById('hasil').getContext('2d');
            var pieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Hoax', 'Asli'],
                    datasets: [{
                        data: [{{$hoax}}, {{$asli}}],
                        backgroundColor: [
                            '#C82121',
                            '#0D0CB5'
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });
        });
    </script>
</body>
</html>