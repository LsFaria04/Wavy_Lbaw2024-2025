<div id = "navigation-menu" class= "flex justify-items-center flex-row lg:flex-col lg:w-52 w-screen lg:max-h-screen bg-sky-900 p-4 text-white text-nowrap fixed bottom-0 lg:sticky lg:top-0 transition-w ease-in duration-300 z-10 border-r border-gray-300">
    <header class = "lg:flex items-center justify-between overflow-hidden">
            <h1 class = "text-xl font-bold">
                <a href="{{ url('/home') }}" >
                    <span class="hidden md:block">Wavy</span>
                </a>
            </h1>
    </header>
    <nav class = "grow lg:pt-14">
        <ul class = "flex justify-center flex-row lg:flex-col gap-8 md:gap-12 overflow-scroll">
            <li class = "test-white font-medium text-lg">
                <a class = "flex flex-row items-center gap-3" href = "{{ route('home')}}">
                    <svg class = "min-w-[20px]" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 512.001 512.001" style="enable-background:new 0 0 512.001 512.001;" xml:space="preserve" width="20" height="20">
                            <path d="M490.134,185.472L338.966,34.304c-45.855-45.737-120.076-45.737-165.931,0L21.867,185.472   C7.819,199.445-0.055,218.457,0,238.272v221.397C0.047,488.568,23.475,511.976,52.374,512h407.253   c28.899-0.023,52.326-23.432,52.373-52.331V238.272C512.056,218.457,504.182,199.445,490.134,185.472z M448,448H341.334v-67.883   c0-44.984-36.467-81.451-81.451-81.451c0,0,0,0,0,0h-7.765c-44.984,0-81.451,36.467-81.451,81.451l0,0V448H64V238.272   c0.007-2.829,1.125-5.541,3.115-7.552L218.283,79.552c20.825-20.831,54.594-20.835,75.425-0.01c0.003,0.003,0.007,0.007,0.01,0.01   L444.886,230.72c1.989,2.011,3.108,4.723,3.115,7.552V448z" fill = "currentColor"/>
                    </svg>
                    <span class="hidden md:block">Home</span>
                </a>
            </li>
            <li class = "test-white font-medium text-lg">
                <a class = "flex flex-row items-center gap-3" href = "{{ route('search') }}">
                    <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" version="1.1" id="Capa_1" x="0px" y="0px" viewBox="0 0 513.749 513.749" style="enable-background:new 0 0 513.749 513.749;" xml:space="preserve" width="21" height="21" fill="white">
                        <path d="M504.352,459.061l-99.435-99.477c74.402-99.427,54.115-240.344-45.312-314.746S119.261-9.277,44.859,90.15   S-9.256,330.494,90.171,404.896c79.868,59.766,189.565,59.766,269.434,0l99.477,99.477c12.501,12.501,32.769,12.501,45.269,0   c12.501-12.501,12.501-32.769,0-45.269L504.352,459.061z M225.717,385.696c-88.366,0-160-71.634-160-160s71.634-160,160-160   s160,71.634,160,160C385.623,314.022,314.044,385.602,225.717,385.696z"/>
                    </svg>
                    <span class="hidden md:block">Search</span>
                </a>
            </li>
            @auth
            <li class = "test-white font-medium text-lg">
                <a id="notifications-link" class = "flex flex-row items-center gap-3" href = "{{ route('notifications') }}">
                    <svg class = "min-w-[20px]" xmlns="http://www.w3.org/2000/svg" id="Isolation_Mode" data-name="Isolation Mode" viewBox="0 0 24 24" width="20" height="20">
                        <path d="M23.608,17.013l-2.8-10.1A9.443,9.443,0,0,0,2.486,7.4L.321,17.14a2.5,2.5,0,0,0,2.441,3.042H6.905a5.285,5.285,0,0,0,10.154,0H21.2a2.5,2.5,0,0,0,2.409-3.169Zm-20.223.169,2.03-9.137a6.443,6.443,0,0,1,12.5-.326l2.628,9.463Z" fill = "currentColor"/>
                    </svg>

                    <span class="hidden md:block">Notifications</span>
                    <span id="notification-dot" class="notification-indicator absolute top-70 right-3 w-6 h-6 bg-red-500 rounded-full animate-pulse hidden"></span>
                </a>
            </li>
            <!--
            <li class = "test-white font-medium text-lg">
                <a class = "flex flex-row items-center gap-3">
                <svg class = "min-w-[20px]" xmlns="http://www.w3.org/2000/svg" id="Bold" viewBox="0 0 24 24" width="20" height="20">
                    <path d="M18.5,1H5.5A5.506,5.506,0,0,0,0,6.5v11A5.506,5.506,0,0,0,5.5,23h13A5.506,5.506,0,0,0,24,17.5V6.5A5.506,5.506,0,0,0,18.5,1Zm0,3a2.476,2.476,0,0,1,1.643.631l-6.5,6.5a2.373,2.373,0,0,1-3.278,0l-6.5-6.5A2.476,2.476,0,0,1,5.5,4Zm0,16H5.5A2.5,2.5,0,0,1,3,17.5V8.017l5.239,5.239a5.317,5.317,0,0,0,7.521,0L21,8.017V17.5A2.5,2.5,0,0,1,18.5,20Z" fill = "currentColor"/>
                </svg>

                    <span class="hidden md:block">Messages</span>
                </a>
            </li>
            -->
            @endauth
            <li class = "test-white font-medium text-lg">

                <a class = "flex flex-row items-center gap-3" href = "{{ route('groupList')}}">
                    <svg class="feather feather-users" fill="none" height="24" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" viewBox="0 0 24 24" width="24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                    <span class="hidden md:block">Groups</span>
                </a>
            </li>
            @if(Auth::check())
                @if(Auth::user()->isadmin)
                <li class = "test-white font-medium text-lg">
                    <a class = "flex flex-row items-center gap-3" href = "{{ route('admin.index')}}">
                    <svg class = "min-w-[20px]" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="20" height="20">
                        <path d="m8,12c3.309,0,6-2.691,6-6S11.309,0,8,0,2,2.691,2,6s2.691,6,6,6Zm0-9c1.654,0,3,1.346,3,3s-1.346,3-3,3-3-1.346-3-3,1.346-3,3-3Zm-3,11h5v3h-5c-1.103,0-2,.897-2,2v5H0v-5c0-2.757,2.243-5,5-5Zm17.5,4c0-.279-.034-.549-.082-.814l1.53-.881-1.497-2.6-1.543.889c-.413-.353-.885-.632-1.407-.818v-1.776h-3v1.776c-.522.186-.994.464-1.407.818l-1.543-.889-1.497,2.6,1.53.881c-.049.265-.082.535-.082.814s.034.549.082.814l-1.53.881,1.497,2.6,1.543-.889c.413.353.885.632,1.407.818v1.776h3v-1.776c.522-.186.994-.464,1.407-.818l1.543.889,1.497-2.6-1.53-.881c.049-.265.082-.535.082-.814Zm-4.5,1.5c-.827,0-1.5-.673-1.5-1.5s.673-1.5,1.5-1.5,1.5.673,1.5,1.5-.673,1.5-1.5,1.5Z" fill = "currentColor"/>
                    </svg>
                        <span class="hidden md:block">Admin</span>
                    </a>
                </li>
                @endif
                
                
                <li class = "test-white font-medium text-lg">
                    <a class = "flex flex-row items-center gap-3" href = "{{ route('profile', ['username' => Auth::user()->username])}}">
                        <svg class = "min-w-[20px]" xmlns="http://www.w3.org/2000/svg" id="Isolation_Mode" data-name="Isolation Mode" viewBox="0 0 24 24" width="20" height="20">
                            <path d="M21,24H18V19a2,2,0,0,0-2-2H8a2,2,0,0,0-2,2v5H3V19a5.006,5.006,0,0,1,5-5h8a5.006,5.006,0,0,1,5,5Z" fill = "currentColor"/>
                            <path d="M12,12a6,6,0,1,1,6-6A6.006,6.006,0,0,1,12,12Zm0-9a3,3,0,1,0,3,3A3,3,0,0,0,12,3Z" fill = "currentColor"/>
                        </svg>
                        <span class="hidden md:block">Profile</span>
                    </a>
                </li>
            
                <li class = "test-white font-medium text-lg">
                    <a class = "flex flex-row items-center gap-3" href = "{{ route('logout')}}">
                        <svg class = "min-w-[20px] rotate-180" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="20" height="20">
                            <path d="M18.589,0H5.411A5.371,5.371,0,0,0,0,5.318V7.182a1.5,1.5,0,0,0,3,0V5.318A2.369,2.369,0,0,1,5.411,3H18.589A2.369,2.369,0,0,1,21,5.318V18.682A2.369,2.369,0,0,1,18.589,21H5.411A2.369,2.369,0,0,1,3,18.682V16.818a1.5,1.5,0,1,0-3,0v1.864A5.371,5.371,0,0,0,5.411,24H18.589A5.371,5.371,0,0,0,24,18.682V5.318A5.371,5.371,0,0,0,18.589,0Z" fill = "currentColor"/>
                            <path d="M3.5,12A1.5,1.5,0,0,0,5,13.5H5l9.975-.027-3.466,3.466a1.5,1.5,0,0,0,2.121,2.122l4.586-4.586a3.5,3.5,0,0,0,0-4.95L13.634,4.939a1.5,1.5,0,1,0-2.121,2.122l3.413,3.412L5,10.5A1.5,1.5,0,0,0,3.5,12Z" fill = "currentColor"/>
                        </svg>
                        <span class="hidden md:block">Logout</span>
                    </a>
                </li>
            @else
                <li class = "test-white font-medium text-lg">
                    <a class = "flex flex-row items-center gap-3" href = "{{ route('login') }}">
                        <svg class = "min-w-[20px]" xmlns="http://www.w3.org/2000/svg" id="Layer_1" data-name="Layer 1" viewBox="0 0 24 24" width="20" height="20">
                            <path d="M18.589,0H5.411A5.371,5.371,0,0,0,0,5.318V7.182a1.5,1.5,0,0,0,3,0V5.318A2.369,2.369,0,0,1,5.411,3H18.589A2.369,2.369,0,0,1,21,5.318V18.682A2.369,2.369,0,0,1,18.589,21H5.411A2.369,2.369,0,0,1,3,18.682V16.818a1.5,1.5,0,1,0-3,0v1.864A5.371,5.371,0,0,0,5.411,24H18.589A5.371,5.371,0,0,0,24,18.682V5.318A5.371,5.371,0,0,0,18.589,0Z" fill = "currentColor"/>
                            <path d="M3.5,12A1.5,1.5,0,0,0,5,13.5H5l9.975-.027-3.466,3.466a1.5,1.5,0,0,0,2.121,2.122l4.586-4.586a3.5,3.5,0,0,0,0-4.95L13.634,4.939a1.5,1.5,0,1,0-2.121,2.122l3.413,3.412L5,10.5A1.5,1.5,0,0,0,3.5,12Z" fill = "currentColor"/>
                        </svg>
                        <span class="hidden md:block">Login</span>
                    </a>
                </li>
            @endif
        
        </ul>
    </nav>

</div>