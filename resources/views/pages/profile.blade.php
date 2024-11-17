@extends('layouts.app')

@section('content')
<section class = "grow" id="profile">
    <p><strong>O senhor doutor José Granja vai fazer esta página</strong></p>
    <p>flag{found_logout}</p>
    <a href = "{{ route('logout') }}" >Logout</a>
</section>
@endSection