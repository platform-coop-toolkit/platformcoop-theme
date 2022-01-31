<div class="project-breadcrumbs">
  <p class="breadcrumb">
    <a href="/">{{ __('Home', 'pcc') }}</a>
    @if($post->post_parent)
      @foreach (SinglePccProject::ancestors() as $a)
        <a href="{{ $a['url'] }}">{{ $a['name'] }}</a>
      @endforeach
      <a href="#">{{ $post->post_title }}</a>
    @else
      <a href="#">{{ $post->post_title }}</a>
    @endif
  </p>
</div>
