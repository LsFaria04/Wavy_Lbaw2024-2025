@extends('layouts.app')

@section('content')

<p><strong>O senhor doutor José Granja vai fazer esta página</strong></p>
<p>flag{found_logout}</p>
<a href = "{{ route('logout') }}" >Logout</a>

@endSection