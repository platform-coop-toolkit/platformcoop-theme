<div class="share-buttons wp-block-column">
    <div>Share this:</div>
    <a class="twitter" target="_blank" rel="noreferrer" href="https://twitter.com/intent/tweet?url=<?=get_permalink();?>">
      @svg('twitter', ['aria-hidden' => 'true'])
    </a>
    <a class="facebook" target="_blank" rel="noreferrer" href="http://www.facebook.com/sharer.php?u=<?=get_permalink();?>">
      @svg('facebook', ['aria-hidden' => 'true'])
    </a>
    <a class="linkedin" target="_blank" rel="noreferrer" href="http://www.linkedin.com/shareArticle?mini=true&url=<?=get_permalink();?>&title=<?=!empty($post->post_title) ? $post->post_title : '#' ?>">
      @svg('linkedin', ['aria-hidden' => 'true'])
    </a>
    <a class="reddit" target="_blank" rel="noreferrer" href="http://www.reddit.com/submit?url=<?=get_permalink();?>&title=<?=!empty($post->post_title) ? $post->post_title : '#' ?>">
      @svg('reddit', ['aria-hidden' => 'true'])
    </a>
</div>