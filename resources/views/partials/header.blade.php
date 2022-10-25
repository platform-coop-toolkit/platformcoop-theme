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
      <a class="login" href="{{ wp_logout_url(get_permalink()) }}"><span>{{ __('Logout', 'pcc') }}</span>@svg('logout', ['class' => 'icon--md', 'aria-hidden' => 'true'])</a>
    @else
      <a class="login" href="#open-modal">@svg('login', ['class' => 'icon--md', 'aria-hidden' => 'true'])<span>{{ __('Login', 'pcc') }}</span></a>
    @endif

    <div id="open-modal" class="modal-dialog">
      <div>
        <h2>{{ __('Login', 'pcc') }}</h2>
        <a class="close-modal" title="Close" href="#close">&times;</a>
        @php
        wp_login_form(
          array(
            'label_username' => __('Your Username', 'pcc'),
            'label_password' => __('Your Password', 'pcc'),
            'label_remember' => __('Remember Me', 'pcc'),
            'label_log_in' => __('Log in', 'pcc'),
          )
        );
        @endphp
      </div>
    </div>

  </div>
</header>