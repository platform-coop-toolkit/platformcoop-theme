@php
  global $template;
  $custom_temp = 'page-custom-blog.blade.php';
  if (basename($template) === $custom_temp) {
    add_filter('body_class', function ($classes) {
      $classes = ['blog logged-in admin-bar no-customize-support wp-embed-responsive multi-author-false app-data index-data home-data'];
      return $classes;
    });
  }
@endphp

<!doctype html>
<html class="no-js" {!! get_language_attributes() !!}>
  @include('partials.head')
  <body @php body_class() @endphp>
    <a class="skip-link" href="#main">{{ __('Skip to main content', 'pcc') }}</a>
    @php wp_body_open() @endphp
    @php do_action('get_header') @endphp
    @include('partials.header')
    <main id="main" class="main">
      @yield('content')
    </main>
    @php do_action('get_footer') @endphp
    @include('partials.footer')
    @php wp_footer() @endphp
  </body>
</html>
