@php

$user = wp_get_current_user();

@endphp

<header class="banner">
  <div class="container">
    <a class="brand" href="{{ home_url('/') }}" rel="home">@svg('logo', ['class' => 'logo', 'aria-hidden' => 'true'])<span class="screen-reader-text">{{ get_bloginfo('name', 'display') }}</span></a>
    <nav id="site-navigation">
      <button class="menu-toggle" aria-expanded="false">@svg('open', ['class' => 'open', 'aria-hidden' => 'true'])@svg('close', ['class' => 'close', 'aria-hidden' => 'true'])</button>
      @if (has_nav_menu('primary_navigation'))
      {!! wp_nav_menu(['theme_location' => 'primary_navigation', 'menu_class' => 'nav', 'container' => false]) !!}
      @endif
    </nav>

    @if (is_user_logged_in())
    <a class="login" href="#logout"><span>{{ __('Logout', 'pcc') }}</span>@svg('logout', ['class' => 'icon--md', 'aria-hidden' => 'true'])</a>
    @else
    <a class="login" href="#open-modal">@svg('login', ['class' => 'icon--md', 'aria-hidden' => 'true'])<span>{{ __('Login', 'pcc') }}</span></a>
    @endif

    <div id="open-modal" class="modal-dialog">
      <div>
        <h2>{{ __('Login', 'pcc') }}</h2>
        <a class="close-modal" title="Close" href="#close">&times;</a>
        <form name="loginform" id="loginform">
          <p class="login-username">
            <label for="user_login">{{ __('Your username/e-mail', 'pcc') }}</label>
            <input type="text" name="log" id="user_login" autocomplete="username" class="input" value="" size="20">
          </p>
          <p class="login-password">
            <label for="user_pass">{{ __('Your password', 'pcc') }}</label>
            <input type="password" name="pwd" id="user_pass" autocomplete="current-password" class="input" value="" size="20">
          </p>
          <p class="login-remember"><label><input name="rememberme" type="checkbox" id="rememberme" value="forever"> {{ __('Remember me', 'pcc') }}</label></p>
          <p class="login-submit">
            <input type="submit" name="submit-login" id="submit-login" class="button button-primary" value="Log in">
          </p>
        </form>
      </div>
    </div>

  </div>
</header>