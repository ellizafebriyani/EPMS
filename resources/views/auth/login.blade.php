@extends('app')
@section('content')
<style>
  .login-card{ max-width:440px; margin:40px auto; padding:24px; border-radius:18px;
    background:linear-gradient(180deg,#17132a,#1f1938); color:#e2e8f0; }
  .login-card h4{ margin-bottom:14px; }
  .login-card .form-control{ background:#120f22; border:1px solid #2a234a; color:#e2e8f0; }
  .btn-login{ background:linear-gradient(90deg,#7c3aed,#22d3ee); border:0; color:#fff; }
</style>
<div class="login-card">
  <h4>Sign in</h4>
  @if ($errors->any())
    <div class="alert alert-danger py-2"><small>{{ $errors->first() }}</small></div>
  @endif
  <form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-3">
      <label class="form-label">Email</label>
      <input type="email" name="email" class="form-control" required autofocus>
    </div>
    <div class="mb-3">
      <label class="form-label">Password</label>
      <input type="password" name="password" class="form-control" required>
    </div>
    <button class="btn btn-login w-100">Login</button>
  </form>
</div>
@endsection
