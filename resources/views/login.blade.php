
<h1> login </h1>
<form action="{{ route('login') }}" method="POST">
@csrf

<label> Username </label>
<input name="username">
@error('username')<small class="text-danger">{{ $message }}</small> @enderror

<label> Password </label>
<input name="password" type="password">
@error('password')<small class="text-danger">{{ $message }}</small> @enderror


<button type="submit"> login </button>
</form>
