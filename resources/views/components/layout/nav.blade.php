<nav class="border-b border-border px-6">
    <div class="max-w-7xl mx-auto h-16 flex items-center justify-between">
        <div>
            <a href="/">
                <img src="/images/logoipsum-226.svg" alt="Logo">
            </a>
        </div>
        <div class="flex gap-x-5 items-center">
            @auth
                <form action="/logout" method="POST">
                    @csrf
                    <button>Log Out</button>
                </form>
            @endauth
            @guest
                <a href="/register">Register</a>
                <a href="/login" class="btn">Sign in</a>
            @endguest
        </div>
    </div>
</nav>
