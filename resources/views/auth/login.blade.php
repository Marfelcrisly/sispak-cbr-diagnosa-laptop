<x-guest-layout>
    <div class="login-logo">
        <div class="logo-mark">CBR</div>
        <div class="logo-text">
            <strong>Sispak CBR</strong>
            Login ke dashboard sistem
        </div>
    </div>

    <div class="login-heading">
        <h1>Selamat Datang</h1>
        <p>Masuk ke sistem pakar untuk mengelola data, diagnosa, dan evaluasi.</p>
    </div>

    @if (session('status'))
        <div class="status">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="error-box">
            <strong>Login gagal.</strong>
            <ul class="error-list">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="form-group">
            <label for="email" class="form-label">Email</label>
            <input
                id="email"
                class="form-input"
                type="email"
                name="email"
                value="{{ old('email') }}"
                required
                autofocus
                autocomplete="username"
                placeholder="Masukkan email"
            >
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input
                id="password"
                class="form-input"
                type="password"
                name="password"
                required
                autocomplete="current-password"
                placeholder="Masukkan password"
            >
        </div>

        <div class="helper-row">
            <label for="remember_me" class="remember-wrap">
                <input id="remember_me" type="checkbox" name="remember">
                <span>Remember me</span>
            </label>

            @if (Route::has('password.request'))
                <a class="link" href="{{ route('password.request') }}">
                    Forgot your password?
                </a>
            @endif
        </div>

        <button type="submit" class="btn-login">
            LOG IN
        </button>
    </form>

    <div class="page-note">
        Gunakan akun yang sudah terdaftar untuk masuk ke sistem.
    </div>
</x-guest-layout>