<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Deteksi Berita Bohong</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body>
    <div class="grid h-screen place-items-center">
        <div class="container mx-auto px-20">
            <div style='background-color:rgb(255, 255, 255)'>
                <form class="relative px-4 mx-auto max-w-7xl sm:px-6 lg:px-8" style="cursor: auto;" action="/hoax" method="POST">
                    @csrf
                    <div class="max-w-lg mx-auto overflow-hidden rounded-lg shadow-lg lg:max-w-none lg:flex">
                        <div class="flex-1 px-6 py-8 bg-white lg:p-12" style="cursor: auto;">
                            <h3 class="text-2xl font-extrabold text-gray-900 sm:text-3xl" style="cursor: auto;">Dataset Berita Hoax</h3>
                            <div class=" mt-6 border-t-2 border-gray-200"></div>
                            <textarea required name="berita" rows="4" class="mt-6 block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Input berita disini"></textarea>
                            <button type="submit" class="text-white m-2 bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center inline-flex items-center mr-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                                Tambahkan
                            </button>
                        </div>
                    </div>
                </form>>
            </div>
        </div>
    </div>
    <div id="alert" class="fixed bottom-0 right-0 m-4 bg-yellow-400 text-black py-2 px-4 rounded hidden">
        @php if(session('msg')) echo session('msg'); @endphp
    </div>

    @if(session('msg'))
    <script>
        var alert = document.getElementById("alert");
        function showAlert() {
            alert.classList.remove("hidden");
        }
        function hideAlert() {
            alert.classList.add("hidden");
        }
        showAlert();
        setTimeout(hideAlert, 3000);
    </script>
    @endif
</body>
</html>