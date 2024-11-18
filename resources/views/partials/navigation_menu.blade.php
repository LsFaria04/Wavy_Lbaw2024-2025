<div id = "navigation-menu" class= "flex justify-items-center flex-row lg:flex-col lg:w-14 w-screen lg:max-h-screen bg-sky-900 p-4 text-white text-nowrap fixed bottom-0 lg:sticky lg:top-0 transition-w ease-in duration-300 ">
    <header class = " hidden lg:flex items-center justify-between overflow-hidden">
            <h1 class = "text-xl font-bold">
                <a href="{{ url('/home') }}" >
                    <span class = "hidden" >Wavy</span>
                </a>
            </h1>
            <button class="mr-2 rotate-180" onclick = "navigationMenuOperation()">
                <svg xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24" width="25" height="25">
                    <path d="M10.482,19.5a1.5,1.5,0,0,1-1.06-.439L4.836,14.475a3.505,3.505,0,0,1,0-4.95L9.422,4.939a1.5,1.5,0,0,1,2.121,2.122L6.957,11.646a.5.5,0,0,0,0,.708l4.586,4.585A1.5,1.5,0,0,1,10.482,19.5Z" fill = "currentColor"/>
                    <path d="M17.482,19.5a1.5,1.5,0,0,1-1.06-.439l-6-6a1.5,1.5,0,0,1,0-2.122l6-6a1.5,1.5,0,1,1,2.121,2.122L13.6,12l4.939,4.939A1.5,1.5,0,0,1,17.482,19.5Z" fill = "currentColor"/>
                </svg>
            </button>
            

    </header>
    <nav class = "grow  lg:pt-20">
        <ul class = "flex justify-center flex-row lg:flex-col gap-16">
            <li class = "test-white font-medium text-lg">
                <a class = "flex flex-row items-center gap-3" href = "{{ route('home')}}">
                    <svg class = "min-w-[20px]" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512.001 512.001" style="enable-background:new 0 0 512.001 512.001;" xml:space="preserve" width="20" height="20">
                            <path d="M490.134,185.472L338.966,34.304c-45.855-45.737-120.076-45.737-165.931,0L21.867,185.472   C7.819,199.445-0.055,218.457,0,238.272v221.397C0.047,488.568,23.475,511.976,52.374,512h407.253   c28.899-0.023,52.326-23.432,52.373-52.331V238.272C512.056,218.457,504.182,199.445,490.134,185.472z M448,448H341.334v-67.883   c0-44.984-36.467-81.451-81.451-81.451c0,0,0,0,0,0h-7.765c-44.984,0-81.451,36.467-81.451,81.451l0,0V448H64V238.272   c0.007-2.829,1.125-5.541,3.115-7.552L218.283,79.552c20.825-20.831,54.594-20.835,75.425-0.01c0.003,0.003,0.007,0.007,0.01,0.01   L444.886,230.72c1.989,2.011,3.108,4.723,3.115,7.552V448z" fill = "currentColor"/>
                    </svg>
                    <span class = "hidden">Home</span>
                </a>
            </li>
            <li class = "test-white font-medium text-lg">
                <a class = "flex flex-row items-center gap-3">
                <svg class = "min-w-[20px]" xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24" width="20" height="20">
                    <path d="M18.5,1H5.5A5.506,5.506,0,0,0,0,6.5v11A5.506,5.506,0,0,0,5.5,23h13A5.506,5.506,0,0,0,24,17.5V6.5A5.506,5.506,0,0,0,18.5,1Zm0,3a2.476,2.476,0,0,1,1.643.631l-6.5,6.5a2.373,2.373,0,0,1-3.278,0l-6.5-6.5A2.476,2.476,0,0,1,5.5,4Zm0,16H5.5A2.5,2.5,0,0,1,3,17.5V8.017l5.239,5.239a5.317,5.317,0,0,0,7.521,0L21,8.017V17.5A2.5,2.5,0,0,1,18.5,20Z" fill = "currentColor"/>
                </svg>

                    <span class = "hidden">Messages</span>
                </a>
            </li>
            <li class = "test-white font-medium text-lg">
                <a class = "flex flex-row items-center gap-3">
                <svg class = "min-w-[20px]" xmlns="http://www.w3.org/2000/svg" id="Isolation_Mode" data-name="Isolation Mode" viewBox="0 0 24 24" width="20" height="20">
                    <path d="M23.608,17.013l-2.8-10.1A9.443,9.443,0,0,0,2.486,7.4L.321,17.14a2.5,2.5,0,0,0,2.441,3.042H6.905a5.285,5.285,0,0,0,10.154,0H21.2a2.5,2.5,0,0,0,2.409-3.169Zm-20.223.169,2.03-9.137a6.443,6.443,0,0,1,12.5-.326l2.628,9.463Z" fill = "currentColor"/>
                </svg>

                    <span class = "hidden">Notifications</span>
                </a>
            </li>
            @if(Auth::check())
                <li class = "test-white font-medium text-lg">
                    <a class = "flex flex-row items-center gap-3" href = "{{ route('profile', ['username' => Auth::user()->username])}}">
                        <svg class = "min-w-[20px]" xmlns="http://www.w3.org/2000/svg" id="Isolation_Mode" data-name="Isolation Mode" viewBox="0 0 24 24" width="20" height="20">
                            <path d="M21,24H18V19a2,2,0,0,0-2-2H8a2,2,0,0,0-2,2v5H3V19a5.006,5.006,0,0,1,5-5h8a5.006,5.006,0,0,1,5,5Z" fill = "currentColor"/>
                            <path d="M12,12a6,6,0,1,1,6-6A6.006,6.006,0,0,1,12,12Zm0-9a3,3,0,1,0,3,3A3,3,0,0,0,12,3Z" fill = "currentColor"/>
                        </svg>
                        <span class = "hidden">Profile</span>
                    </a>
                </li>
            @else
                <li class = "test-white font-medium text-lg">
                    <a class = "flex flex-row items-center gap-3" href = "{{ route('login') }}">
                        <svg class = "min-w-[20px]" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="20" height="20">
                            <path d="M18.589,0H5.411A5.371,5.371,0,0,0,0,5.318V7.182a1.5,1.5,0,0,0,3,0V5.318A2.369,2.369,0,0,1,5.411,3H18.589A2.369,2.369,0,0,1,21,5.318V18.682A2.369,2.369,0,0,1,18.589,21H5.411A2.369,2.369,0,0,1,3,18.682V16.818a1.5,1.5,0,1,0-3,0v1.864A5.371,5.371,0,0,0,5.411,24H18.589A5.371,5.371,0,0,0,24,18.682V5.318A5.371,5.371,0,0,0,18.589,0Z" fill = "currentColor"/>
                            <path d="M3.5,12A1.5,1.5,0,0,0,5,13.5H5l9.975-.027-3.466,3.466a1.5,1.5,0,0,0,2.121,2.122l4.586-4.586a3.5,3.5,0,0,0,0-4.95L13.634,4.939a1.5,1.5,0,1,0-2.121,2.122l3.413,3.412L5,10.5A1.5,1.5,0,0,0,3.5,12Z" fill = "currentColor"/>
                        </svg>
                        <span class = "hidden">Login</span>
                    </a>
                </li>
            @endif
        
        </ul>
    </nav>

</div>